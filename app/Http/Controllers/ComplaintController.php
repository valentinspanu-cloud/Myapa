<?php

namespace App\Http\Controllers;

use App\Models\ComplaintComment;
use App\Models\ComplaintStatus;
use App\Models\ComplaintType;
use App\Http\Requests\ComplaintRequest;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Mail\ConsumerComplaint;
use App\Mail\AdminComplaint;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class ComplaintController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the complaint create form and a history of the last 24 months complaints
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $locations = session('locations');

        $types = ComplaintType::all();

        if ($request['location']) {
            session(['currentLocation' => $locations[$request['location']]]);
            return redirect()->back();
        }

        $complaints = Complaint::where('user_id', auth()->user()->id)->orderby('id', 'desc')->get();

        return view('complaint.index', [
            'complaints' => $complaints,
            'locations' => $locations,
            'types' => $types,
            'currentLocation' => session('currentLocation')
        ]);
    }

    /**
     * Save a complaint. This can be done only by the consumer
     *
     * @param ComplaintRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ComplaintRequest $request)
    {
        $complaint = new Complaint();
        $complaint->subject = $request['subject'];
        $complaint->description = $request['description'];
        $complaint->user_id = auth()->user()->id;
        $complaint->type_id = $request['type_id'];
        $complaint->location = session('currentLocation')['cod_loc'];
        $complaint->status_id = Complaint::UNSOLVED;
        $complaint->save();

        try {

            Mail::to(auth()->user())->send(new ConsumerComplaint($complaint));

            $resp = ComplaintType::find($request['type_id']);
            foreach ($resp->users as $us) {
                $u = User::find($us->user_id);
                Mail::to($u)->send(new AdminComplaint($complaint));
            }

        } catch (\Exception $e) {
            \Log::error("Nu s-a trimis email la crearea unei sesizari");
            \Log::error($e->getMessage());
        }

        return redirect()->back()->with('success', trans('general.pages.complaints.success'));
    }

    /**
     * View a complaint
     *
     * @param Complaint $complaint
     * @return array|string
     * @throws \Throwable
     */
    public function show(Complaint $complaint)
    {
        if (!isAdmin() && $complaint->user_id != auth()->user()->id) {
            return redirect()->back();
        }

        return view('complaint.admin.view', compact('complaint'))->render();
    }

    /**
     * Edit a complaint
     *
     * @param Complaint $complaint
     * @return array|string
     * @throws \Throwable
     */
    public function edit($id)
    {
        $complaint = Complaint::withTrashed()->find($id);
        $statuses = ComplaintStatus::all();
        $types = ComplaintType::all();

        return view('complaint.admin.edit', compact('complaint', 'statuses', 'types'))->render();
    }

    /**
     * Update a complaint, this function is used for complaint solving by an admin
     *
     * @param ComplaintRequest $request
     * @param Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComplaintRequest $request, Complaint $complaint)
    {
        $user = User::find($complaint['user_id']);
	
	$type_id = $complaint->type_id;

        $comment = new ComplaintComment();
        $comment->complaint_id = $complaint->id;
        $comment->user_id = auth()->user()->id;
        $comment->status_id = $request->status_id;
        $comment->comment = $request->comment;
        $comment->type_id = $request->type_id;
        $comment->save();       
        
        if ($comment->type_id != $complaint->type_id) {
            $resp = ComplaintType::find($comment->type_id);

            foreach ($resp->users as $us) {
                $u = User::find($us->user_id);
                Mail::to($u)->send(new AdminComplaint($complaint));
            }
        }

        $complaint->status_id = $request->status_id;
        $complaint->type_id = $request->type_id;
        $complaint->update();
        if ($type_id == $request->type_id) {
            Mail::to($user)->send(new ConsumerComplaint($complaint));
        }
        return redirect()->back()->with('success', trans('general.pages.complaints.success'));
    }

    /**
     * Complaints list for admin
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function admin(Request $request)
    {
        return view('complaint.admin.admin');
    }

    /**
     * Complaints for Datatables Ajax
     *
     * @param Request $request
     * @return false|string
     * @throws \Throwable
     */
    public function getComplaints(Request $request)
    {
$types = ComplaintType::all();
        $complaintTypes = [];

        foreach ($types as $type) {
            if (!empty($type->users)) {
                foreach ($type->users as $user) {
                    $complaintTypes[$type->id][] = $user->user_id;
                }
            }
        }


        $request['page'] = ($request['start'] / $request['length']) + 1;
        $columns = [
            'complaints.id',
            'type.name',
            'reporter.name',
            'complaints.location',
            'complaints.created_at',
            'complaints.subject',
            'complaints.description',
            'status.name',
        ];

        $json = new \stdClass();
        $json->draw = $request->get('draw');
        $json->data = [];

        $complaints = Complaint::leftjoin('users as reporter', 'complaints.user_id', '=', 'reporter.id')
            ->leftjoin('complaint_types as type', 'complaints.type_id', '=', 'type.id')
            ->leftjoin('complaint_statuses as status', 'complaints.status_id', '=', 'status.id')
            ->with('reporter.codes')
            ->withTrashed();

        if ($request->has('order')) {
            foreach ($request->get('order') as $value) {
                $complaints->orderBy($columns[$value['column']], $value['dir']);
            }
        }

        $complaints->select('complaints.*', 'reporter.name as reporterName', 'type.name as type', 'status.name as status');

        if (!empty($request->get('search')['value'])) {
            foreach ($columns as $column) {
                $complaints->orWhere($column, 'LIKE', "%" . $request->get('search')['value'] . "%");
            }
        }

        if (($request->has('from') && $request->get('to')) && ($request->get('to') != $request->get('from'))) {
            $complaints->where('complaints.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->get('from') . ' 00:00:00')));
            $complaints->where('complaints.created_at', '<=', date('Y-m-d H:i:s', strtotime($request->get('to') . ' 23:59:59')));
        } elseif (($request->has('from') && $request->get('to')) && ($request->get('to') == $request->get('from'))) {
            $complaints->where('complaints.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->get('from') . ' 00:00:00')));
            $complaints->where('complaints.created_at', '<', date('Y-m-d H:i:s', strtotime($request->get('to') . ' 00:00:00') + 86400));
        }

        if ($request->get('length') > -1) {
            $complaints = $complaints->paginate($request->get('length'));
        } else {
            $complaints = $complaints->paginate(1000000);
        }

        $json->recordsFiltered = $json->recordsTotal = $complaints->total();
$counter = 0;
        foreach ($complaints->items() as $k => $complaint) {

  if (in_array(auth()->user()->id, $complaintTypes[$complaint['type_id']]) || auth()->user()->hasRole('admin')) {         
    	   $k =  $counter;
	    $json->data[$k][] = $complaint['id'];
            $json->data[$k][] = $complaint['type'];
            $json->data[$k][] = $complaint['reporterName'];
            $json->data[$k][] = $complaint['location'];
            $json->data[$k][] = $complaint['created_at'];
            $json->data[$k][] = $complaint['subject'];
            $json->data[$k][] = $complaint['status'];
            $json->data[$k][] = view('complaint.admin.action', compact('complaint'))->render();
$counter++;        
}
}

$json->data  = array_map('array_values', $json->data);

        return json_encode($json);
    }

    public function destroy(Complaint $complaint, $delete = false)
    {
        $complaint->delete();

        if ($delete) {
            return 'success';
        }

        return redirect()->back()->with('success_delete', trans('general.pages.complaints.delete'));
    }

    public function destroyAll(Request $request)
    {
        if (empty($request->get('ids'))) {
            return redirect()->back()->with('success_delete', 'Nu ati selectat nicio sesizare');
        }

        $ids = explode(',', $request->get('ids'));

        foreach ($ids as $complaint_id) {
            $this->destroy(Complaint::find($complaint_id), 1);
        }

        return redirect()->back()->with('success_delete', 'Sesizarile au fost sterse');
    }

}
