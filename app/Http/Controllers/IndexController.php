<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['role:consumer', 'auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $period = indexPeriod();
        $today = intval(date('d'));
        $isReady = true;

        if ($today > $period['to'] || $today < $period['from']) {
            $isReady = false;
        }

        return view('index.index', [
            'hasMeter' => !empty(session('currentLocation')['currentWaterMeter']),
            'isReady' => $isReady,
            'currentLocation' => session('currentLocation'),
            'locations' => session('locations'),
            'waterMeters' => session('waterMeters'),
            'period' => $period,
            'user' => auth()->user()
        ]);
    }

    /**
     * Store a newly created index in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $currentLocation = session('currentLocation');
        $min = 0;
	      $max = 0;

        if (!empty($currentLocation['currentWaterMeter']['indexes'])) {

	    //$min = $currentLocation['currentWaterMeter']['indexes'][0]['index_nou'] ? $currentLocation['currentWaterMeter']['indexes'][0]['index_vechi'] : $currentLocation['currentWaterMeter']['indexes'][0]['index_nou'];

	 if ($currentLocation['currentWaterMeter']['indexes'][0]['index_nou'] != ''){
            $min = $currentLocation['currentWaterMeter']['indexes'][0]['index_vechi'];
        }
        else{
            $min = $currentLocation['currentWaterMeter']['indexes'][0]['index_vechi'];
        }

         
  	     if (in_array(auth()->user()->category, ['FAG','FAS', 'FBL','FCA','FIN'])) {
                    $max = $min + 99999;
            }

           /* if (in_array(auth()->user()->category, ['FAJ', 'FAA'])) {
                    $max = $min + 9999;
            }
            
            */
        }

        $this->validate($request, [
          'value' => 'required|integer|gte:'.$min.'|between:' . $min . ',' . $max
        ]);
	

        $response = ApiController::saveIndex($request['value']);

        if (isset($response['io_mesaj']) && $response['io_mesaj'] != 'Actualizare facuta cu succes') {
            $request->session()->flash('error', trans('general.pages.index.error'));
            return redirect(route('index.list'));
        }

        $request->session()->flash('success', trans('general.pages.index.success'));

        return redirect(route('index.list'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWaterMeters(Request $request)
    {
        $period = indexPeriod();
        $today = intval(date('d'));
        $isReady = true;

        if ($today > $period['to'] || $today < $period['from']) {
            $isReady = false;
        }

        $codLocatie['cod_loc'] = $request['locationCode'];
        session(['currentLocation' => $codLocatie]);



        $waterMeters = ApiController::getWaterMeters(session('currentLocation')['cod_loc']);
       // print_r(session('currentLocation')['cod_loc']);
      //
        $response = [
            'waterMeters' => $waterMeters,
            'form' => view()->make('index.partials.form', [
                'hasMeter' => !empty(session('currentLocation')['currentWaterMeter']),
                'period' => $period,
                'isReady' => $isReady,
                'currentLocation' => session('currentLocation'),
                'user' => auth()->user()])->render(),
                'indexes' => view()->make('index.partials.list', [
                'currentLocation' => session('currentLocation'),
                'user' => auth()->user()
            ])->render()
        ];

        if (empty($waterMeters)) {
            $response['message'] = trans('general.pages.index.no_waterMeters');
        }
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getIndexes(Request $request)
    {
        $period = indexPeriod();
        $today = intval(date('d'));
        $isReady = true;

        if ($today > $period['to'] || $today < $period['from']) {
            $isReady = false;
        }

        ApiController::getIndexes($request['waterMeterCode']);
      //  $currentLocation = session('currentLocation');
 if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
 //ApiController::getIndexes('01683653/ZEN');
 //$request['waterMeterCode'] = '01683653/ZEN';
 }
        
        
        $currentLocation['contorCurent'] = $request['waterMeterCode'];
        session(['contorCurent' => $currentLocation]);
        
        
                    if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
          //dd($currentLocation);
         // dd(session('personal'));
        // dd(auth()->user()->codes[0]['client_code']);
        // session(['currentLocation' => $currentLocation]);
        //dd(session('currentLocation'));
        
        
          }

        $response = [
            'form' => view()->make('index.partials.form', [
                'hasMeter' => !empty(session('currentLocation')['currentWaterMeter']),
                'period' => $period,
                'isReady' => $isReady,
                'currentLocation' => session('currentLocation'),
                'user' => auth()->user()
            ])->render(),

            'indexes' => view()->make('index.partials.list', [
                'currentLocation' => session('currentLocation'),
                'user' => auth()->user()])->render()
        ];
           if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
          //dd($currentLocation);
         // dd(session('personal'));
        // dd(auth()->user()->codes[0]['client_code']);
      //  dd(session('currentLocation')['contorSelectat']['cod_contor']);
        
          }
        if (empty($currentLocation['currentWaterMeter']['indexes'])) {
            $response['message'] = trans('general.pages.index.no_indexes');
        }

        return $response;
    }

}
