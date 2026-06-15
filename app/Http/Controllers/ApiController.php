<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client as CURL;
use App\Models\User;

class ApiController
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Get personal data about the consummer and also do the register if data exists.
     * If data does not exist, it means that the consummer client code or contract number are wrong
     *
     * @param $data
     * @return bool|mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getPersonalData($data)
    {
        $response = ApiController::cURL('detalii_client', $data);

        if (empty($response['items'])) {
            return false;
        }

        session(['personal' => $response['items'][0]]);

        return $response['items'][0];
    }

    /**
     * Get the locations based on the client code and contract number
     * Also set the first location as the currentLocation for the select boxes default value
     * Set the location code as key of the location array in order to check later with waterMeters and indexes
     *
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getLocations()
    {
        $newLocations = [];

        ApiController::getIndexTypes();
        $locations = ApiController::cURL('contr_loc', [
            'cod_client' => auth()->user()->codes[0]['client_code'],
            'nr_contract' => auth()->user()->codes[0]['contract_nr']
        ]);
        
         $params = [
                'cod_client' => auth()->user()->codes[0]['client_code'],
                'nr_contract' => auth()->user()->codes[0]['contract_nr']
            ];
         $data = ApiController::getPersonalData($params);

        if (!empty($locations['items'])) {

            foreach ($locations['items'] as $location) {
                $newLocations[$location['cod_loc']] = $location;
            }

            $currentLocation = $locations['items'][0];
            
             if (!empty(session('currentLocation'))) {
                $currentLocation = $locations['items'][0];

                if(!empty(session('codLocc'))){
                    $currentLocation = session('codLocc');
                }
                else {
                    $currentLocation = $locations['items'][0];
                }

            }
            else {
                $currentLocation = $locations['items'][0];
            }

            session(['locations' => $newLocations]);
            session(['currentLocation' => $currentLocation]);

            ApiController::getwaterMeters($currentLocation['cod_loc']);
            
        }
        return $newLocations;

    }


    /**
     * Get the locations based on the client code and contract number
     * Also set the first location as the currentLocation for the select boxes default value
     * Set the location code as key of the location array in order to check later with waterMeters and indexes
     *
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getLocationsAll()
    {

        $newLocations_all = [];
        $locations_all = array();
      //  ApiController::getIndexTypes();
        foreach (auth()->user()->codes_all as $codeUser) {
            $locations_all[] = ApiController::cURL('contr_loc', [
                'cod_client' => $codeUser['client_code'],
                'nr_contract' => $codeUser['contract_nr']
            ]);
        }


        // $newArray = [];
        foreach($locations_all as $i => $item) {
            // Match the two arrays together. Get the same index from the 2nd array.
            $locations_all['items'][] = $item['items'][0];
        }


        if (!empty($locations_all['items'])) {

            foreach ($locations_all['items'] as $location) {
                $newLocations_all[$location['cod_loc']] = $location;
            }

            session(['locationsAll' => $newLocations_all]);

        }
        
        return $newLocations_all;
    }

    /**
     * Get all the waterMeters of a location and also set the current waterMeter for later
     *
     * @param $locationCode
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getWaterMeters($locationCode)
    {

        $newWaterMeters = [];

        $waterMeters = ApiController::cURL('contori', [
            'cod_client' => auth()->user()->codes[0]['client_code'],
            'cod_locatie' => $locationCode
        ]);

        $currentLocation = session('currentLocation');
        
        if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
         //dd(session('contorCurent'));
          }

        if($locationCode != $currentLocation['cod_loc']){
            $currentLocation = session('locations')[$locationCode];
        }

        if(empty($currentLocation['currentWaterMeter'])){
            $currentLocation['currentWaterMeter'] = [];
        }
        
        if(!empty(session('contorCurent'))){
        $currentLocation['currentWaterMeter'] = session('contorCurent');
        }


        if (!empty($waterMeters['items'])) {

            foreach ($waterMeters['items'] as $waterMeter) {
                $newWaterMeters[$waterMeter['cod_contor']] = $waterMeter;
            }

            session(['waterMeters' => $newWaterMeters]);
   
            if(empty($currentLocation['currentWaterMeter'])){
                $currentLocation['currentWaterMeter'] = $waterMeters['items'][0];
                $waterMeterIndex = $waterMeters['items'][0]['cod_contor'];
            }else{
                 $currentLocation['currentWaterMeter'] = session('contorCurent');
                // $waterMeterIndex = session('contorCurent');
                 $waterMeterIndex = $currentLocation['currentWaterMeter']['contorCurent'];
            }
            
            if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
        //  dd($waterMeterIndex);
         // dd(session('personal'));
        // dd(auth()->user()->codes[0]['client_code']);
 //dd(session('contorCurent')['cod_contor']);
        
          }

            ApiController::getIndexes($waterMeterIndex);


        }

        if(empty($waterMeters['items'])){
            session(['waterMeters' => []]);
        }

        ApiController::getInvoices($locationCode);
            if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
        //  dd($waterMeterIndex);
         // dd(session('personal'));
        // dd(auth()->user()->codes[0]['client_code']);
 //dd(session('contorCurent')['cod_contor']);
        
          }
        return $newWaterMeters;
    }

    /**
     * Get indexes for the last 24 months for a specific waterMeter
     * Add these indexes in session for the current waterMeter for the current location
     * The months limit is dynamic based on the env parameter INDEX_MONTH_LIMIT
     *
     * @param $waterMeterCode
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getIndexes($waterMeterCode)
    {
        $indexes = ApiController::cURL('idx_contor', [
            'cod_contor' => $waterMeterCode,
            'nr_luni' => env('INDEX_MONTH_LIMIT', 24),
            'cod_client' => auth()->user()->codes[0]['client_code']
        ]);
                              if($_SERVER['REMOTE_ADDR'] == '86.127.131.54' ){
        //  dd($waterMeterCode);
         // dd(session('personal'));
        // dd(auth()->user()->codes[0]['client_code']);
       // dd(session('contorCurent'));
        
          }
      //  $currentLocation['currentWaterMeter']['cod_contor'] = $waterMeterCode;
      //  session(['currentLocation' => $currentLocation]);

        $currentLocation = session('currentLocation');
        $currentLocation['currentWaterMeter'] = session('waterMeters')[$waterMeterCode];
       // $currentLocation['currentWaterMeter'] = $waterMeterCode;
        $currentLocation['currentWaterMeter']['indexes'] = $indexes['items'];
        


        session(['currentLocation' => $currentLocation]);
//if($_SERVER['REMOTE_ADDR'] == '195.82.130.8'){
        //  dd($indexes);
//}
        return $indexes['items'];
    }

    /**
     * Send the index to SIVAPPS
     *
     * @param $index
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function saveIndex($index)
    {
//dd(date('d-M-Y'));
        $saveIndex = ApiController::cURL('autocitire', [
            'cod_client' => auth()->user()->codes[0]['client_code'],
            'cod_contor' => session('currentLocation')['currentWaterMeter']['cod_contor'],
            'luna_an' =>  date('d-M-Y'),
            'index_nou' => $index
        ], 'PUT');

        return $saveIndex;
    }

    /**
     * Get the index types, if the index type is different from CIT it cannot be changed in minus
     *
     * @return array
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getIndexTypes()
    {
        $types = [];

        $indexTypes = ApiController::cURL('tipuri_citiri');

        if (!empty($indexTypes['items'])) {
            foreach ($indexTypes['items'] as $type) {
                $types[$type['cod_tip_calc']] = $type['desc_tip_calc'];
            }
        }

        if (!session('types')) {
            session(['types' => $types]);
        }

        return $types;
    }

    /**
     * Get all the invoices for the current location
     *
     * @param $currentLocationCode
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getInvoices($currentLocationCode)
    {
        //E37154LC_00001
        //E37134LC_00001
//dd(env('INDEX_MONTH_LIMIT', 240)); //24


        $invoices = ApiController::cURL('lista_facturi', [
            'cod_client' => auth()->user()->codes[0]['client_code'],
            'cod_locatie' => $currentLocationCode, //$currentLocationCode
            'nr_luni' => env('INDEX_MONTH_LIMIT', 240)
        ]);


        $currentLocation = session('currentLocation');
if($_SERVER['REMOTE_ADDR'] == '86.127.133.207'){
//231374LC_00001//
//2454409404LC_00001
$user = User::find(auth()->user()->id);
             // dd($user->codes[0]['client_id']);
            }
        $allInvoices = $payedInvoices = $unPayedInvoices = [];

        if (!empty($invoices['items'])) {
            krsort($invoices['items']);

            foreach ($invoices['items'] as $item) {
                if (!$item['rest_icpl']) {
                    $payedInvoices[$item['idfactura']] = $item;
                } else {
                    $unPayedInvoices[$item['idfactura']] = $item;
                }

                $allInvoices[$item['idfactura']] = $item;
            }
        }
        //  dd($currentLocationCode);
        $currentLocation['payedInvoices'] = $payedInvoices;
        $currentLocation['unPayedInvoices'] = $unPayedInvoices;
        $currentLocation['invoices'] = $allInvoices;

        session(['currentLocation' => $currentLocation]);

        return [
            'payedInvoices' => $payedInvoices,
            'unPayedInvoices' => $unPayedInvoices
        ];
    }

    /**
     * Get the last invoice
     *
     * @param $user
     * @return bool
     * @throws CustomException
     * @throws GuzzleException
     */
    public static function getLastInvoice($user)
    {
        $notify = false;

        $locations = ApiController::cURL('contr_loc', [
            'cod_client' => $user->codes[0]['client_code'],
            'nr_contract' => $user->codes[0]['contract_nr']
        ]);

        if (!empty($locations['items'])) {
            foreach ($locations['items'] as $location) {
                $invoice = ApiController::cURL('lista_facturi', [
                    'cod_client' => $user->codes[0]['client_code'],
                    'cod_locatie' => $location['cod_loc'],
                    'nr_luni' => 1
                ]);

                if(!empty($invoice['items'][0])) {
                    if (date('m', strtotime($invoice['items'][0]['datafactura'])) == date('m')) {
                        $notify = true;
                    }
                }
            }
        }

        return $notify;
    }

    /**
     * Get single invoice information
     *
     * @param $id
     * @return bool|mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getInvoice($id)
    {
        $invoice = ApiController::cURL('factura', [
            'id_factura' => $id,
        ]);

        if (empty($invoice['items'])) {
            return false;
        }

        return $invoice;
    }


    /**
     * Get client current sold
     *
     * @return false|mixed
     * @throws CustomException
     * @throws GuzzleException
     */
    public static function getSoldForUser($user)
    {
        try {
            $sold = ApiController::cURL('sold', [
                'idfirma' => $user->codes[0]['client_id'],
                'datasold' => date('d-M-Y'),
                'i_iddocei' => null,
                'i_codloc' => null
            ]);
            if (empty($sold['items'])) return 0;
            return $sold['items'][0]['sold'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function getSold()
    {
        $sold = ApiController::cURL('sold', [
            'idfirma' => auth()->user()->codes[0]['client_id'],
            'datasold' => date('d-M-Y'),
            'i_iddocei' => null,
            'i_codloc' => null
        ]);

        if (empty($sold['items'])) {
            return false;
        }

        return $sold['items'][0]['sold'];
    }

    /**
     * Pay the invoice
     *
     */
    public static function saveInvoice($transaction)
    {
        $response = $transaction['response'];
        $extraData = json_decode($response['ExtraData'], 1);
        $user = User::find($extraData['user_id']);

        foreach ($extraData['invoiceData'] as $invoice) {
            $saveInvoice = ApiController::cURL('plata_factura', [
                'sid' => 1,
                'idfeldoc' => 767,
                'nrfact' => $invoice['numarfactura'],
                'iddocei' => $invoice['idfactura'],
                'id_client' => $user->codes[0]['client_id'], //auth()->user()->id,auth()->user()->codes[0]['client_id']
                'data_inc' => date('d-M-Y'),
                'nrdivgen' => 'DV_' . $user->codes[0]['client_code'],
                'codbanca' => explode('||', getSetting('bank'))[0],
                'val_inc' => $invoice['rest_icpl'],
                'err_mes' => null,
            ], 'PUT');

            \Log::info('Plata factura:' . print_r($invoice, 1));
            \Log::info('Plata factura id_client:' . print_r($user->codes[0]['client_id'], 1));
            \Log::info('Plata factura nrdivgen - client code:' . print_r($user->codes[0]['client_code'], 1));
            \Log::info('Raspuns Plata factura:' . print_r($saveInvoice, 1));
        }

        return $saveInvoice;
    }

    /**
     * Get the bank accounts for invoice payments
     *
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getAccounts()
    {
        $accounts = ApiController::cURL('conturi');

        return $accounts['items'];
    }

    /**
     * Set only electronic invoice, no more paper invoice
     *
     * @param $value
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function electronicInvoice($value)
    {
        return ApiController::cURL('set_mlf', [
            'sid' =>  auth()->user()->codes[0]['client_id']
        ], 'PUT');
        
    }

    /**
     * If a client creates a new account it will save its client_id in a special table in SVAP for billing purposes
     * If the account is deleted, hes client_id will be removed from the table
     * The purpose of that table is to server to Invoice operators in order to know which client needs the invoice in PDF format
     *
     * @param bool $client_id
     * @param string $action
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function flagAccountForInvoice($client_id = false, $action = "I")
    {
        //I = Insert client_id in DB, D = Delete client_id from DB
        return ApiController::cURL('flag', [
            'sid' => $client_id ?: auth()->user()->codes[0]['client_id'],
            'actiune' => $action
        ], 'PUT');
    }

    /**
     * Get sectors for user
     *
     * @param bool $client_id
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSectors($client_id = false)
    {
        $sectors = ApiController::cURL('sectoare', [
            'id_client' => $client_id ?: session('personal')['id'],
        ]);

        return $sectors['items'];
    }

    /**
     * Get payment type for invoice
     *
     * @param bool $invoice_id
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function paymentType($invoice_id)
    {
        $types = ApiController::cURL('tip_plata', [
            'iddocei' => $invoice_id,
        ]);

        $result = [];
        if (!empty($types['items'])) {
            foreach ($types['items'] as $type) {
                if (!empty($type['mod_plata'])) {
                    $result[] = $type['mod_plata'];
                }
            }
        }

        return implode(',', $result);
    }

    /**
     * Check if the paid invoice has an DV_.... created in the SVAP DB, if not, that's an issue
     *
     * @param $invoiceNumber
     * @param bool $client_id
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function checkInvoice($invoiceNumber, $client_id = false)
    {
        $dvs = ApiController::cURL('verificare_factura', [
            'id_firma' => $client_id ?: auth()->user()->codes[0]['client_id'],
            'iddocei' => $invoiceNumber,
        ]);

        return $dvs['items'];
    }

    /**
     * Send the barcode url to db
     *
     * @param $iddocei
     * @param $url
     * @return mixed
     * @throws CustomException
     * @throws GuzzleException
     */
    public static function sendBarcode($iddocei, $url)
    {
        return ApiController::cURL('flag', [
            'iddocei' => $iddocei,
            'url' => $url
        ], 'PUT');
    }

    /**
     * Get invoice strings to generate barcodes
     *
     * @return mixed
     * @throws CustomException
     * @throws GuzzleException
     */
    public static function getInvoiceStrings()
    {
        $invoices = ApiController::cURL('verificare_factura');
        return $invoices['items'];
    }

    /**
     * Basic use of Guzzle for accessing the SIVAPPS API
     *
     * @param $url
     * @param array $params
     * @param string $method
     * @return mixed
     * @throws \App\Exceptions\CustomException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cURL($url, $params = [], $method = 'GET')
    {
        $url2 = 'http://192.168.1.45:2125/ords/svap/op/'. $url;

        try {

            $http = new CURL;

            switch ($method) {
                case 'GET':
                    $response = $http->request('GET', $url2 . '?' . http_build_query($params));
                    break;
                case 'POST':
                    $response = $http->request($method, $url2, [
                        'form_params' => json_encode($params),
                        'timeout' => 100,
                    ]);
                    break;
                case 'PUT':
                    $response = $http->request($method, $url2, [
                        'timeout' => 100,
                        'body' => json_encode($params),
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ]
                    ]);
                    break;
            }

            return $response = json_decode((string)$response->getBody(), true);

        } catch (\Exception $e) {

            \Log::error('Conexiunea la API nu a functionat');
            \Log::error($e->getMessage());
            //throw new \App\Exceptions\CustomException('Something Went Wrong.');
        }
    }

}
