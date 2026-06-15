<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Sector;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['role:admin', 'auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function edit()
    {
        $settings = Setting::all();
        $accounts = ApiController::getAccounts();
//     $users = User::role('consumer')->with('codes')->where('notify_invoice', 1)->get();

  //   foreach($users as $user){
    //   ApiController::electronicInvoice($user['codes'][0]['client_id']);
      //}

        return view('settings.edit', compact('settings', 'accounts'));
    }

    /**
     * Update a setting
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {

        foreach ($request->key as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            $setting->value = $value;
            $setting->save();
        }

        return redirect(route('settings.edit'))->with('success', @trans('general.pages.settings.success'));
    }
}
