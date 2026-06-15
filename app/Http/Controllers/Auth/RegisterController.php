<?php

namespace App\Http\Controllers\Auth;

use App\Models\Sector;
use App\Traits\Google;
use App\Models\User;
use App\Models\ClientCode;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\ApiController;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, Google;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register(Request $request)
    {
        \Log::info("REGISTER START - " . date("H:i:s"));
        $this->validator($request->all())->validate();


        $response = $this->recaptchaAuth($request->all());

        if (!empty($response['score']) && $response['score'] < 0.2) {
            $request->session()->flash('error', trans('general.pages.login.recaptcha_error'));
            return redirect()->back();
        }

        $params = [
           'cod_client' => $request['client_code'],
            'nr_contract' => $request['contract_nr'] 
        ];

        $data = ApiController::getPersonalData($params);

        if (!$data) {
            return redirect()->back()->with('siverror', trans('general.pages.register.sivapp_fail'))->withInput();
        }

        session(['personal' => $data]);

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);
        // Activeaza MLF dupa login
        try {
            $clientId = session("personal")["id"] ?? null;
            \Log::info("MLF dupa login: " . $clientId);
            if ($clientId) { ApiController::cURL("set_mlf", ["sid" => $clientId], "PUT"); \Log::info("MLF activat"); }
        } catch (\Exception $e) { \Log::info("Eroare MLF: " . $e->getMessage()); }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'numeric', 'digits:10', 'regex:/0[\d]{3}[\d]{3}[\d]{3}$/u', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!$#%@&^~,.)(*]).*\S$/'],
            'client_code' => ['required', 'unique:client_codes'],
            'contract_nr' => ['required'],
            'agree' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => 1,
            'notify' => 1,
            'notify_sms' => 1,
            'notify_invoice' => 1,
            'name' => session('personal')['nume'],
            'category' => session('personal')['category'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('consumer');

        ClientCode::create([
            'client_code' => $data['client_code'],
            'contract_nr' => $data['contract_nr'],
            'client_id' => session('personal')['id'],
            'user_id' => $user->id
        ]);

 	try {
            ApiController::flagAccountForInvoice(session('personal')['id']);
  	    $sectors = ApiController::getSectors(session('personal')['id']);
            if(!empty($sectors)) {
                foreach ($sectors as $sector) {
                    $newSector = new Sector();
                    $newSector->user_id = $user->id;
                    $newSector->sector_code = $sector['cod_sect'];
                    $newSector->city = $sector['localitate'];
                    $newSector->save();
                }
            }
	} catch (CustomException $e) {
            \Log::info('Eroare FLAG: ' . $e->getMessage());
        }
        // Seteaza notify dupa API
        $user->notify = 1;
        $user->notify_sms = 1;
        $user->notify_invoice = 1;
        $user->save();

        return $user;
    }
}
