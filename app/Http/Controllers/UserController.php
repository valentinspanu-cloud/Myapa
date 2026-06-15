<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\Sector;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\UserStatus;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClientCode;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the users list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::doesnthave('codes')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Create a new user, it will always be administrator because the consumer must have a contract number and client code
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $statuses = UserStatus::all();
        $roles = Role::where('id', '<>', 2)->get();

        return view('users.create', compact('statuses', 'roles'));
    }


    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = 0;
        $user->password = bcrypt($request->input('password'));
        $user->email_verified_at = now();
        $user->status = $request->input('status');
        $user->category = null;
        $user->save();
        $user->assignRole($request['role']);

        $request->session()->flash('success', trans('general.pages.users.save_success'));

        return redirect(route('users'));
    }

    /**
     * Show My Account
     *
     * @return \Illuminate\Http\Response
     */
    public function account()
    {

        $user = auth()->user();
        $locations = session('locations');
        $locationsAll = session('locationsAll');

        return view('users.profile', compact('user', 'locations', 'locationsAll'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $statuses = UserStatus::all();
        $roles = Role::where('id', '<>', 2)->get();

        if ($user->hasRole('consumer')) {
            return view('users.consumer', compact('user', 'statuses'));
        }

        return view('users.edit', compact('user', 'statuses', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UserRequest $request, User $user)
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->status = $request->input('status');

        if ($request->input('password_confirmation')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->update();
        $user->syncRoles($request['role']);

        $request->session()->flash('success', trans('general.pages.users.save_success'));

        return redirect(route('users'));
    }
    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateConsumer(Request $request, User $user)
    {
        /* $user->name = $request->input('name');
         $user->email = $request->input('email');
         $user->phone = $request->input('phone');*/
        $user->status = $request->input('status');
        $user->update();
        $request->session()->flash('success', trans('general.pages.users.save_success'));

        return redirect(route('users'));
    }

    /**
     * Update user in my account section
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws GuzzleException
     */
    public function patch(Request $request, User $user)
    {
        if ($request['change']) {
            if (!Hash::check($request['old_pass'], $user->password)) {
                return redirect()->back()->withErrors(['old_pass' => trans('general.pages.users.no_hash')]);
            }

            $rules['password'] = 'confirmed|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!$#%@)(&*^~,.)(]).*\S$/';
            $user->password = bcrypt($request['password']);
        }

        if ($request['save']) {

            if (!Hash::check($request['confirm_old_pass'], $user->password)) {
                return redirect()->back()->withErrors(['confirm_old_pass' => trans('general.pages.users.no_hash')]);
            }

            $rules = [
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|numeric|digits:10|regex:/0[\d]{3}[\d]{3}[\d]{3}$/u',
            ];

            $user->email = $request['email'];
            $user->phone = $request['phone'];
            $user->notify = $request['notify'] ?: null;
            $user->notify_sms = $request['notify_sms'] ?: null;
            if ($request['notify_invoice']) {
                try {
                   $response =  ApiController::electronicInvoice($request['notify_invoice']);
                   if ($response) {
                        $user->notify_invoice = $request['notify_invoice'];
                    }
                } catch (CustomException $e) {
                    \Log::info('Activare factura electronica: ' . $e->getMessage());
                }
            }
        }

        $request->validate($rules);

        $user->update();

        return redirect()->back()->with('success', trans('general.pages.users.update_success'));
    }


    /**
     * Add locations in my account section
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws GuzzleException
     */
    public function addLocations(Request $request, User $user)
    {

        if ($request['saveLoc']) {

            $params = [
                'cod_client' => $request['cod_client'],
                'nr_contract' => $request['nr_contract']
            ];

           // auth()->user()->codes[0]['id'] ='5782';

            $data = ApiController::getPersonalData($params);

            //dd($data);
            if (!$data) {
                return redirect()->back()->with('error', trans('general.pages.register.sivapp_fail'));
            }

            session(['personal' => $data]);

           // dd(session('personal'));
            auth()->user()->codes[0]['client_id']  = session('personal')['id'];
            auth()->user()->codes[0]['client_code'] = session('personal')['cod'];
            auth()->user()->codes[0]['contract_nr'] = session('personal')['nr_contract'];


            //Auth::loginUsingId(1);
            ClientCode::create([
                'client_code' => $data['cod'],
                'contract_nr' => $data['nr_contract'],
                'client_id' => session('personal')['id'],
                'user_id' => $user->id
            ]);

            try {
                ApiController::flagAccountForInvoice(session('personal')['id']);
                ApiController::electronicInvoice('1');
                


           } catch (CustomException $e) {
                \Log::info('Eroare FLAG: ' . $e->getMessage());
            }
            return redirect()->back()->with('success', trans('general.pages.users.update_success'));
        }
    }


    /**
     * Change locations in account
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws GuzzleException
     */
    public function changeLoc(Request $request, User $user)
    {

        if ($request['alege']) {


            session(['selectedLocation' => $request['alege']]);
            Auth::setUser($user);

            Auth::user()->fresh()->name;
            // auth()->user()->codes[0]['id'] ='5782';

            /*$data = ApiController::getPersonalData($params);

            //dd($data);
            if (!$data) {
                return redirect()->back()->with('error', trans('general.pages.register.sivapp_fail'));
            }

            session(['personal' => $data]);

           // dd(session('personal'));
            auth()->user()->codes[0]['client_id']  = session('personal')['id'];
            auth()->user()->codes[0]['client_code'] = session('personal')['cod'];
            auth()->user()->codes[0]['contract_nr'] = session('personal')['nr_contract'];


            //Auth::loginUsingId(1);
            ClientCode::create([
                'client_code' => $data['cod'],
                'contract_nr' => $data['nr_contract'],
                'client_id' => session('personal')['id'],
                'user_id' => $user->id
            ]);

            try {
                ApiController::flagAccountForInvoice(session('personal')['id']);*/

            /* $sectors = ApiController::getSectors(session('personal')['id']);
             if(!empty($sectors)) {
                 foreach ($sectors as $sector) {
                     $newSector = new Sector();
                     $newSector->user_id = $user->id;
                     $newSector->sector_code = $sector['cod_sect'];
                     $newSector->city = $sector['localitate'];
                     $newSector->save();
                 }
             }
            */
            /*  } catch (CustomException $e) {
                  \Log::info('Eroare FLAG: ' . $e->getMessage());
              }*/
             // ApiController::electronicInvoice('1');
            return redirect()->back()->with('success', trans('general.pages.users.update_success'));
        }
    }

    /**
     * Delete a user and it's client codes and complaints
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, User $user)
    {

	try {
            ApiController::flagAccountForInvoice($user->codes[0]['client_id'], 'D');
        } catch (CustomException $e) {
            \Log::info('Eroare Stergere FLAG: ' . $e->getMessage());
        }

        ClientCode::where('user_id', $user->id)->delete();
        UserNotification::where('user_id', $user->id)->forceDelete();
        Complaint::where('user_id', $user->id)->delete();
        Transaction::where('user_id', $user->id)->delete();
        $user->syncRoles();
        $user->delete();

        if ($user->id == auth()->user()->id) {
            $request->session()->flash('success', trans('general.pages.users.consumer_delete_success'));
            Auth::logout();
            return redirect(route('login'));
        } else {
            $request->session()->flash('success', trans('general.pages.users.delete_success'));
            return redirect()->back();
        }
    }


    /**
     * Retrieve all the users for the datatables
     *
     * @param Request $request
     * @return false|string
     * @throws \Throwable
     */
    public function users(Request $request)
    {
        $request['page'] = ($request['start'] / $request['length']) + 1;
        $columns = [
            'users.id',
            'codes.client_code',
            'codes.client_id',
            'users.name',
            'users.phone',
            'users.email',
        ];

        $json = new \stdClass();
        $json->draw = $request->get('draw');
        $json->data = [];

        $users = User::leftjoin('user_statuses as status', 'users.status', '=', 'status.id')
            ->leftjoin('client_codes as codes', 'users.id', '=', 'codes.user_id');

        if ($request->has('order')) {
            foreach ($request->get('order') as $value) {
                $users->orderBy($columns[$value['column']], $value['dir']);
            }
        }

        $users->select('users.*', 'status.name as status', 'codes.client_code', 'codes.client_id');

        if (!empty($request->get('search')['value'])) {
            foreach ($columns as $column) {
                $users->orWhere($column, 'LIKE', "%" . $request->get('search')['value'] . "%");
            }
        }

        $users->whereHas('codes');

        if ($request->get('length') > -1) {
            $users = $users->paginate($request->get('length'));
        } else {
            $users = $users->paginate(1000000);
        }
        $counter = $counter2 = 0;
        foreach ($users->items() as $k => $user) {
            if (!$user->hasRole('consumer')) {
                $counter++;
                continue;
            }
            $json->data[$counter2][] = $user['id'];
            $json->data[$counter2][] = $user['client_code'];
            $json->data[$counter2][] = $user['client_id'];
            $json->data[$counter2][] = $user['name'];
            $json->data[$counter2][] = $user['email'];
            $json->data[$counter2][] = $user['phone'];
            $json->data[$counter2][] = $user['status'];
            $json->data[$counter2][] = view('users.action', compact('user'))->render();
            $counter2++;
        }

        $json->recordsFiltered = $json->recordsTotal = ($users->total() - $counter);
        return json_encode($json);
    }

    public function clients()
    {

    }

}
