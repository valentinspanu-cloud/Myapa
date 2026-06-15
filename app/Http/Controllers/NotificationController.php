<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Jobs\ConsumerNotification;
use App\Mail\NotificationConsumer;
use App\Mail\NotificationSmsConsumer;
use App\Models\NotificationType;
use App\Models\Sector;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Vonage\Laravel\Facade\Vonage;
use PHPMailer\PHPMailer\PHPMailer;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        if (strpos(\Request::url(), 'trimite') === false) {
            $this->middleware('auth');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $deletedNotifications = UserNotification::onlyTrashed()->where('user_id', auth()->user()->id)->get()->pluck('notification_id')
            ->toArray();

        $notifications = Notification::where('created_at', '>', (new Carbon(auth()->user()->created_at))->format('Y-m-d H:i:s'))
            ->where(function ($q) {
                $q->where('category', auth()->user()->category)->whereNull('sectors')->orWhere('receiver_id', auth()->user()->id);
                $q->orWhere('receiver_id', null)->where('category', 'All')->whereNull('sectors');
                $q->orWhereRaw("JSON_CONTAINS(sectors, CAST(? AS JSON))", [auth()->user()->id]);

            })->whereNotIn('id', $deletedNotifications)
            ->orderBy('id', 'desc')
            ->with('type')
            ->get();
            
            

        $seenNotifications = UserNotification::where('user_id', auth()->user()->id)->get()
            ->pluck('notification_id')
            ->toArray();


        return view('notifications.index', compact('notifications', 'seenNotifications'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        /*$users = User::where('id', 16)->get();
        $notification = Notification::find(5);
        foreach ($users as $k => $user) {
            for ($i = 0; $i < 10; $i++) {
                Mail::to($user)->send(new NotificationConsumer($notification));
                //sleep(2);
            }
        }*/

        return view('notifications.admin');
    }

    public function getNotifications(Request $request)
    {
        $request['page'] = ($request['length'] > 0) ? ($request['start'] / $request['length']) + 1 : 1;
        $columns = [
            'notifications.id',
            'type.name',
            'users.name',
            'notifications.subject',
            'notifications.created_at',
            'status.name'
        ];

        $json = new \stdClass();
        $json->draw = $request->get('draw');
        $json->data = [];

        $notifications = Notification::leftjoin('users', 'notifications.user_id', '=', 'users.id')
            ->leftjoin('notification_types as type', 'notifications.type_id', '=', 'type.id')
            ->leftjoin('notification_statuses as status', 'notifications.status_id', '=', 'status.id');

        if ($request->has('order')) {
            foreach ($request->get('order') as $value) {
                $notifications->orderBy($columns[$value['column']], $value['dir']);
            }
        }

        $notifications->select('notifications.*', 'type.name as type', 'users.name as username', 'status.name as status');

        if (!empty($request->get('search')['value'])) {
            foreach ($columns as $column) {
                $notifications->orWhere($column, 'LIKE', "%" . $request->get('search')['value'] . "%");
            }
        }

        if (($request->has('from') && $request->get('to')) && ($request->get('to') != $request->get('from'))) {
            $notifications->where('notifications.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->get('from') . ' 00:00:00')));
            $notifications->where('notifications.created_at', '<=', date('Y-m-d H:i:s', strtotime($request->get('to') . ' 23:59:59')));
        } elseif (($request->has('from') && $request->get('to')) && ($request->get('to') == $request->get('from'))) {
            $notifications->where('notifications.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->get('from') . ' 00:00:00')));
            $notifications->where('notifications.created_at', '<', date('Y-m-d H:i:s', strtotime($request->get('to') . ' 00:00:00') + 86400));
        }

        if ($request->get('length') > -1) {
            $notifications = $notifications->paginate($request->get('length'));
        } else {
            $notifications = $notifications->paginate(1000000);
        }

        $json->recordsFiltered = $json->recordsTotal = $notifications->total();

        foreach ($notifications->items() as $k => $notification) {
            $json->data[$k][] = $notification['id'];
            $json->data[$k][] = $notification['type'];
            $json->data[$k][] = $notification['username'];
            $json->data[$k][] = $notification['subject'];
            $json->data[$k][] = $notification['created_at']->format('d-m-Y H:i');
            $json->data[$k][] = $notification['status'];
            $json->data[$k][] = view('notifications.action', compact('notification'))->render();
        }

        return json_encode($json);
    }

    /**
     * Create form for a notification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $types = NotificationType::all();
        $clients = User::role('consumer')->where(function ($q) {
            $q->where('notify', 1)->orWhere('notify_sms', 1);
        })->where('status', 1)->get();
        $categories = $clients->pluck('category', 'category');
        $sectors = \DB::select('SELECT id, sector_code, city FROM sectors WHERE id IN (SELECT MIN(id) FROM sectors GROUP BY sector_code, city) order by sector_code');

        return view('notifications.create', compact('types', 'categories', 'clients', 'sectors'));
    }

    /**
     * Save a notification
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $sectors = [];

        $request->validate([
            'subject' => 'required|max:255',
            'type_id' => 'required|numeric',
            'content' => 'required',
            'sms_content' => 'max:500'
        ]);

        if ($request['receiver_id']) {
            $count = 1;
        } elseif ($request['category'] != 'all') {
            $count = User::role('consumer')
                ->where(function ($q) {
                    $q->where('notify', 1)->orWhere('notify_sms', 1);
                })
                ->where('status', 1)
                ->where('category', $request['category'])
                ->get()
                ->count();
        } elseif (!empty($request['sector'])) {

            foreach ($request['sector'] as $sector) {
                $data = explode('--', $sector);
                $sectors = array_merge($sectors, Sector::where('sector_code', $data[0])->where('city', $data[1])->get()->pluck('user_id')->toArray());
            }
            $count = User::role('consumer')
                ->where(function ($q) {
                    $q->where('notify', 1)->orWhere('notify_sms', 1);
                })->whereIn('id', $sectors)
                ->where('status', 1)
                ->get()
                ->count();
        } else {
            $count = User::where(function ($q) {
                $q->where('notify', 1)->orWhere('notify_sms', 1);
            })
                ->where('status', 1)
                ->get()
                ->count();
        }

        $notification = new Notification();
        $notification->subject = $request['subject'];
        $notification->content = $request['content'];
        $notification->sms_content = $request['sms_content'];
        $notification->type_id = $request['type_id'];
        $notification->date_from = $request['date_from'] ? date('Y-m-d', strtotime($request['date_from'])) : null;
        $notification->date_to = null;
        $notification->user_id = auth()->user()->id;
        $notification->send_email = $request->get('send_email') ? 1 : 0;
        $notification->status_id = Notification::PENDING;
        $notification->receiver_id = $request['receiver_id'];
        $notification->category = $request['category'];
        $notification->target_count = $count;
        $notification->sectors = $sectors ?: null;
        $notification->save();

        \DB::table('crons')->insert(['notification_id' => $notification->id, 'status' => 'ready']);

        return redirect(route('notification.admin'))->with('success', trans('general.pages.notifications.success'))->withInput();
    }


public function trimite_mail($de_la,$catre,$subiect,$mesaj)
{
    $header  = 'MIME-Version: 1.0' . "\n";
    $header .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
    $header .= 'From: '. $de_la . "\n";

    @mail($catre, $subiect, $mesaj, $header);
}
    

    /**
     * Send notifications by CRON
     *
     * @param Request $request
     * @return string
     */
    public function send(Request $request)
    {
// $sendSMS = $this->sendSMS('+40751097307', 'ssss');
  //                        \Log::info("SMS: " . '12312312' . '--' . print_r($sendSMS, 1));
//die();

        if ($request->get('key') != env('CRON_KEY')) {
            return 'no permission';
        }

        $notificationCron = \DB::table('crons')->where('status', 'ready')->get();

        foreach ($notificationCron as $notif) {

            \DB::table('crons')->where('id', $notif->id)->update(['status' => 'done']);

            $notification = Notification::find($notif->notification_id);
            if (!$notification) { continue; }

            if ($notification->date_from && ($notification->date_from->format('Y/m/d') != Carbon::now()->format('Y/m/d'))) {
                continue;
            }

            if ($notification->receiver_id) {
                $users = User::where('id', $notification->receiver_id)->get();
            } elseif ($notification->category != 'all') {
                $users = User::role('consumer')
                    ->where(function ($q) {
                        $q->where('notify', 1)->orWhere('notify_sms', 1);
                    })
                    ->where('status', 1)
                    ->where('category', $notification->category)
                    ->get();
            } elseif ($notification->sectors) {
                $users = User::role('consumer')
                    ->where(function ($q) {
                        $q->where('notify', 1)->orWhere('notify_sms', 1);
                    })->whereIn('id', is_array($notification->sectors) ? $notification->sectors : json_decode($notification->sectors, 1))
                    ->where('status', 1)
                    ->get();
            } else {
                $users = User::role('consumer')
                    ->where(function ($q) {
                        $q->where('notify', 1)->orWhere('notify_sms', 1);
                    })
                    ->where('status', 1)
                    ->get();
            }

            if ($notification->content && $notification->send_email) {

                try {
                    foreach ($users as $k => $user) {
		if($notification->type_id == 2){
                            if(!ApiController::getLastInvoice($user)){
                                continue;
                            }
                        }
                        Mail::to($user)->send(new NotificationConsumer($notification));
                        sleep(2);
		\Log::info("Mail trimis catre: " . $user->email);
                    }
                    $notification->status_id = Notification::SENT;
                    $notification->update();
                } catch (\Exception $e) {
                    $notification->status_id = Notification::NOTSENT;
                    $notification->update();
                    \Log::error('Eroare CRON EMAIL ' . $e->getMessage());
                }
            }

            if ($notification->sms_content) {
                try {
                    foreach ($users as $user) {
                        if ($user->phone) {
			    if($notification->type_id == 2){
                            if(!ApiController::getLastInvoice($user)){
                                continue;
                            }
                        }
			
			     //Mail::to('+4'.$user->phone.'@acetsv.sms')->send(new NotificationSmsConsumer($notification));

                            $sendSMS = $this->sendSMS('+4' . $user->phone, $notification->sms_content);
                            \Log::info('SMS: ' . $user->phone . '--' . print_r($sendSMS, 1));
			    sleep(2);
                        }
                    }
                } catch (\Exception $e) {
                    $notification->status_id = Notification::NOTSENT;
                    $notification->update();
                    \Log::error('Eroare CRON SMS ' . $e->getMessage());
                }
            }
        }

        return 'success';
    }

    /**
     * Send SMS to users that checked notify_sms
     *
     * @param $phoneNumber
     * @param $message
     * @return mixed|string
     */
    private function sendSMS($phoneNumber, $message)
    {
        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return "socket_create() failed: reason: " . socket_strerror(socket_last_error());
        }


        /* Make a connection to the Diafaan SMS Server host */
        $result = socket_connect($socket, env('SMS_HOST'), env('SMS_PORT'));
        if ($result === false) {
            return "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket));
        }

        /* Create the HTTP API query string */
        $query = '/http/send-message/';
        $query .= '?username=' . urlencode(env('SMS_USERNAME'));
        $query .= '&password=' . urlencode(env('SMS_PASSWORD'));
        $query .= '&to=' . urlencode($phoneNumber);
        $query .= '&message=' . urlencode($message);

        /* Send the HTTP GET request */
        $in = "GET " . $query . " HTTP/1.1\r\n";
        $in .= "Host: " . env('SMS_HOST') . "\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $out = '';
        socket_write($socket, $in, strlen($in));

        /* Get the HTTP response */
        $out = '';
        while ($buffer = socket_read($socket, 2048)) {
            $out = $out . $buffer;
        }

        socket_close($socket);

        /* Extract the last line of the HTTP response to filter out the HTTP header and get the send result*/
        $lines = explode("\n", $out);

        return end($lines);
    }


    /**
     * Vizualizare notificare
     *
     * @param Notification $notification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Notification $notification)
    {
        if (!isAdmin()) {

            $seenNotification = UserNotification::where('user_id', auth()->user()->id)
                ->where('notification_id', $notification->id)
                ->first();

            if (empty($seenNotification)) {
                $userNotification = new UserNotification();
                $userNotification->user_id = auth()->user()->id;
                $userNotification->notification_id = $notification->id;
                $userNotification->save();
            }
        }

        return view('notifications.view', compact('notification'));
    }

    /**
     * Create notification each month for index saving
     *
     * @param Request $request
     * @return string
     */
    public function createIndexNotification(Request $request)
    {
        if ($request->get('key') != env('CRON_KEY')) {
            return 'no permission';
        }

        $notification = new Notification();
        $notification->subject = 'MyAPA-Aquaserv Tulcea Informare perioada de transmitere index';
        $notification->content = '<p>Vă reamintim  că puteți transmite indexul autocitit al contorului de apă în intervalul 20-30 al acestei luni. </p><p>Vă mulțumim,<br /><a href="https://my.aquaservtulcea.ro/" target="_blank" title=MyAPA Aquaserv Tulcea"> MyAPA Aquaserv Tulcea </a></p>';
        $notification->sms_content = 'Stimate client, vă reamintim că puteți transmite indexul autocitit al contorului de apă în intervalul 20-30 al acestei luni. Vă mulțumim, Echipa Aquaserv';
        $notification->send_email = $request->get('send_email') ? 1 : 0;
        $notification->status_id = Notification::PENDING;
        $notification->user_id = 1;
        $notification->type_id = 4;
        $notification->receiver_id = null;
        $notification->category = 'all';
        $notification->target_count = User::role('consumer')->get()->count();
        $notification->date_from = null;
        $notification->date_to = null;
        $notification->save();

        \DB::table('crons')->insert(['notification_id' => $notification->id, 'status' => 'ready']);

        return 'success';
    }

    /**
     * Create notification each month for invoice
     *
     * @param Request $request
     * @return string
     */
    public function createInvoiceNotification(Request $request)
    {
        if ($request->get('key') != env('CRON_KEY')) {
            return 'no permission';
        }

        $notification = new Notification();
        $notification->subject = 'Informare emitere factura';
        $notification->content = '<p>>Vă informăm că s-au emis facturile pentru prestațiile de bază (apă și canal).<br/>  Clienții care au optat pentru emiterea acestora doar în format electronic vor regăsi în portal facturile în format PDF. </p><p>Vă mulțumim,<br />Aquaserv.</p>';
        $notification->sms_content = 'Stimate client, Vă informăm că vi s-a emis factura pentru serviciile furnizate de către Aquaserv. Vă mulțumim, Aquaserv.';
        $notification->send_email = $request->get('send_email') ? 1 : 0;
        $notification->status_id = Notification::PENDING;
        $notification->user_id = 1;
        $notification->type_id = 2;
        $notification->receiver_id = null;
        $notification->category = 'all';
        $notification->target_count = User::role('consumer')->get()->count();
        $notification->date_from = null;
        $notification->date_to = null;
        $notification->save();

        \DB::table('crons')->insert(['notification_id' => $notification->id, 'status' => 'ready']);

        return 'success';
    }

    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            \DB::table('crons')->where('notification_id', $id)->delete();
            \DB::table('user_notification')->where('notification_id', $id)->delete();
            $notification->delete();
        }
        return redirect(route('notification.admin'))->with('success', 'Notificarea a fost ștearsă.');
    }

    public function destroy($id, $delete = false)
    {
        if (!UserNotification::where('notification_id', $id)->where('user_id', auth()->user()->id)->exists()) {
            $userNotification = new UserNotification();
            $userNotification->user_id = auth()->user()->id;
            $userNotification->notification_id = $id;
            $userNotification->save();
        } else {
            $userNotification = UserNotification::where('notification_id', $id)->where('user_id', auth()->user()->id)->first();
        }

        $userNotification->delete();

        if ($delete) {
            return 'success';
        }

        return redirect()->back()->with('success', 'Notificarea a fost stearsa');
    }

    public function destroyAll(Request $request)
    {
        if (empty($request->get('ids'))) {
            return redirect()->back()->with('success', 'Nu ati selectat nicio notificare');
        }

        $ids = explode(',', $request->get('ids'));

        foreach ($ids as $notifcation_id) {
            $this->destroy($notifcation_id, 1);
        }

        return redirect()->back()->with('success', 'Notificarile au fost sterse');
    }
}


