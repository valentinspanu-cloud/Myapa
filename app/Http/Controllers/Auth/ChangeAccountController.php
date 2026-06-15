<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Google;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Support\Str;
use TimeHunter\LaravelGoogleReCaptchaV3\Facades\GoogleReCaptchaV3;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;

class ChangeAccountController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ChangeAccountController Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');
    }

    protected function clientAccountAuth(Request $request, $user)
    {
        if (!isAdmin()) {
            $data = ApiController::getPersonalData($params = [
                'cod_client' => 'E3715',
                'nr_contract' => '504280'
            ]);

            if (!$data) {
                return redirect()->back()->with('siverror', trans('general.pages.register.sivapp_fail'))->withInput();
            }
        }


	  if(Str::contains($request->get('previous'), '//aquaservtulcea.ro')){
            $request['previous'] = env('APP_URL');
        }

        return redirect($request->get('previous') ?? route('home'));
    }

}
