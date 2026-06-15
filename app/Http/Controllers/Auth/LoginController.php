<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Traits\Google;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use TimeHunter\LaravelGoogleReCaptchaV3\Facades\GoogleReCaptchaV3;
use TimeHunter\LaravelGoogleReCaptchaV3\Validations\GoogleReCaptchaV3ValidationRule;
class LoginController extends Controller
{
    use Google, AuthenticatesUsers {
        login as parentLogin;
    }

    protected $redirectTo = '/';

    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $isAdmin = $user->roles->pluck('name')->intersect([
            'admin', 'complaints_manager', 'notifications_manager',
            'closingwater_manager', 'bulletinanalysis_manager'
        ])->isNotEmpty();

        if ($isAdmin) {
            return redirect()->route('home');
        }

        $firstCode = $user->codes()->first();
        if (!$firstCode) {
            return redirect()->route('home');
        }

        $data = ApiController::getPersonalData($params = [
            'cod_client' => $firstCode->client_code,
            'nr_contract' => $firstCode->contract_nr
        ]);

        if (!$data) {
            return redirect()->back()->with('siverror', trans('general.pages.register.sivapp_fail'))->withInput();
        }

        if (Str::contains($request->get('previous'), '//aquaservtulcea.ro')) {
            $request['previous'] = env('APP_URL');
        }

        return redirect($request->get('previous') ?? route('home'));
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $response = $this->recaptchaAuth($request->all());
        if (!empty($response['score']) && $response['score'] < 0.2) {
            $request->session()->flash('error', trans('general.pages.login.recaptcha_error'));
            return redirect()->back();
        }
        if (!empty($user) && $user->status > 1) {
            $request->session()->flash('error', trans('general.pages.login.inactive_user'));
            return redirect()->back();
        }
        return $this->parentLogin($request);
    }
}
