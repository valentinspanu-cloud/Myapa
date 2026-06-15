<?php
namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Picqer\Barcode\BarcodeGeneratorPNG;

class InvoiceController extends Controller
{
    public function __construct()
    {
        if (strpos(\Request::url(), 'inregistrare-tranzactie') === false) {
            $this->middleware(['role:consumer', 'auth']);
        }
    }

    public function index()
    {
        $sold = ApiController::getSold() ?: 0;
        return view('invoice.index', [
            'unPayedInvoices' => session('currentLocation')['unPayedInvoices'] ?? [],
            'locations'       => session('locations'),
            'currentLocation' => session('currentLocation'),
            'sold'            => $sold
        ]);
    }

    public function historyAjax(Request $request)
    {
        $page     = max(1, (int) $request->get('page', 1));
        $perPage  = 10;
        $invoices = session('currentLocation')['payedInvoices'] ?? [];

        usort($invoices, fn($a, $b) =>
            strtotime($b['datafactura']) - strtotime($a['datafactura'])
        );

        $total    = count($invoices);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);
        $items    = array_slice($invoices, ($page - 1) * $perPage, $perPage);

        $items = array_map(function($inv) {
            $inv['has_pdf'] = invoiceExists($inv['numarfactura'], $inv['datafactura']);
            return $inv;
        }, $items);

        return response()->json([
            'data'     => $items,
            'total'    => $total,
            'page'     => $page,
            'lastPage' => $lastPage,
        ]);
    }

    public function invoices(Request $request)
    {
        ApiController::getWaterMeters($request['currentLocation']);
        $codLocatie['cod_loc'] = $request['currentLocation'];
        session(['codLocc' => $codLocatie]);
        session(['currentLocation' => $codLocatie]);
        return redirect()->back();
    }

    public function pay(Request $request)
    {
        $this->validate($request, [
            'invoicePay' => 'required'
        ]);

        $total = 0;
        $invoiceData = $invoicesToPay = [];
        $invoices = session('currentLocation')['invoices'];
        $timestamp = time();

        $hash = $this->customHash([
            'user_id'   => auth()->user()->id,
            'timestamp' => $timestamp
        ]);

        foreach ($invoices as $invoice) {
            if (in_array($invoice['numarfactura'], $request->invoicePay)) {
                $total += $invoice['rest_icpl'];

                $i = addslashes(json_encode($invoice['numarfactura']));

                $isPayed = DB::table('transactions_history')
                    ->where('invoices', 'like', '%' . $i . '%')
                    ->where('user_id', auth()->user()->id)
                    ->where('status', 'silent')
                    ->get()->toArray();

                if (!empty($isPayed)) {
                    foreach ($isPayed as $p) {
                        $response = json_decode($p->response, 1);
                        if ($response['approval']) {
                            return redirect()->back()->with('message', 'Factura ' . $invoice['numarfactura'] . ' a fost platita. Daca nu apare achitata in urmatoarele ore, va rugam sa ne contactati');
                        }
                    }
                }

                $invoicesToPay[] = $invoice['numarfactura'];
                $invoiceData[]   = $invoice;
            }
        }

        $dataAll = [
            'amount'      => number_format($total, 2, '.', ''),
            'curr'        => 'RON',
            'invoice_id'  => $hash,
            'order_desc'  => 'Plata facturi: ' . implode(', ', $invoicesToPay),
            'merch_id'    => env('EUPLATESC_MID_TEST'),
            'timestamp'   => gmdate("YmdHis"),
            'nonce'       => md5(microtime() . mt_rand())
        ];

        $dataAll['fp_hash'] = strtoupper($this->euplatesc_mac($dataAll, env('EUPLATESC_KEY_TEST')));

        $dataBill = [
            'fname'   => session('personal')['nume'] ?? auth()->user()->name,
            'country' => 'Romania',
            'company' => 'AQUASERV',
            'city'    => session('currentLocation')['addr_city_code'],
            'add'     => session('currentLocation')['addr_text'],
            'email'   => auth()->user()->email,
            'phone'   => auth()->user()->phone
        ];

        $dataBill['ExtraData'] = json_encode([
            'user_id'     => auth()->user()->id,
            'timestamp'   => $timestamp,
            'invoices'    => $invoicesToPay,
            'invoiceData' => $invoiceData,
            'userData'    => session('personal')
        ]);

        $this->addTransaction([
            'invoices' => $invoicesToPay,
            'hash'     => $hash,
            'amount'   => number_format($total, 2, '.', ''),
            'status'   => 'start',
            'response' => [
                'dataBill' => $dataBill,
                'dataAll'  => $dataAll,
            ]
        ]);

        return view('invoice.pay', compact('dataBill', 'dataAll'));
    }

    /**
     * Plata avans — cand nu exista facturi neachitate
     */
    public function payAdvance(Request $request)
    {
        $this->validate($request, [
            'advance_amount' => 'required|numeric|min:1'
        ]);

        $amount    = number_format((float) $request->advance_amount, 2, '.', '');
        $timestamp = time();

        $hash = $this->customHash([
            'user_id'   => auth()->user()->id,
            'timestamp' => $timestamp
        ]);

        $dataAll = [
            'amount'     => $amount,
            'curr'       => 'RON',
            'invoice_id' => $hash,
            'order_desc' => 'Plata avans cont client',
            'merch_id'   => env('EUPLATESC_MID_TEST'),
            'timestamp'  => gmdate("YmdHis"),
            'nonce'      => md5(microtime() . mt_rand())
        ];

        $dataAll['fp_hash'] = strtoupper($this->euplatesc_mac($dataAll, env('EUPLATESC_KEY_TEST')));

        $dataBill = [
            'fname'   => session('personal')['nume'] ?? auth()->user()->name,
            'country' => 'Romania',
            'company' => 'AQUASERV',
            'city'    => session('currentLocation')['addr_city_code'] ?? '',
            'add'     => session('currentLocation')['addr_text'] ?? '',
            'email'   => auth()->user()->email,
            'phone'   => auth()->user()->phone
        ];

        $dataBill['ExtraData'] = json_encode([
            'user_id'   => auth()->user()->id,
            'timestamp' => $timestamp,
            'invoices'  => [],
            'userData'  => session('personal')
        ]);

        $this->addTransaction([
            'invoices' => [],
            'hash'     => $hash,
            'amount'   => $amount,
            'status'   => 'start',
            'response' => [
                'dataBill' => $dataBill,
                'dataAll'  => $dataAll,
            ]
        ]);

        return view('invoice.pay', compact('dataBill', 'dataAll'));
    }

    public function show($id)
    {
        $invoice  = ApiController::getInvoice($id)['items'];
        $location = session('currentLocation');
        $data     = [];

        if (isset($location['invoices'][$id])) {
            $data = $location['invoices'][$id];
        }

        return view('invoice.single', compact('invoice', 'data', 'location'));
    }

    public function download($id, $date)
    {
        $clientCode = str_replace("/", "", auth()->user()->codes[0]["client_code"]);
        $nrFactura  = str_replace("/", "", $id);
        $pattern    = storage_path("app/invoices/") . $clientCode . "_" . $nrFactura . "_*.pdf";
        $files      = glob($pattern) ?: [];

        if (empty($files)) {
            $files = glob(storage_path("app/invoices/*/*/") . $clientCode . "_" . $nrFactura . "_*.pdf") ?: [];
        }

        if (empty($files)) {
            abort(404);
        }

        $file     = $files[0];
        $filename = basename($file);

        return Response::download($file, $filename, ["Content-Type: application/pdf"]);
    }

    public function thankYou(Request $request)
    {
        $this->addTransaction($request->all());
        $message = trans('general.pages.invoice.thankYou_p1' . (!$request['approval'] ? '_fail' : ''));
        return view('invoice.thanks', compact('message'));
    }

    public function silentTransaction(Request $request)
    {
        if ($request->has('invoice_id')) {
            $checkIfSilentExist = DB::table('transactions_history')
                ->where('hash', $request->get('invoice_id'))
                ->where('status', 'silent')
                ->get()->first();
            if ($checkIfSilentExist) {
                \Log::info('Already exists silent data: ' . print_r($request, 1));
                return 'already exists';
            }
        }

        $savedInvoice = [];
        if (!empty($request['approval'])) {
            $savedInvoice = ApiController::saveInvoice(['response' => $request->all()]);
        }

        if (!empty($savedInvoice['err_mes']) && $savedInvoice['err_mes'] == 'OK') {
            $request['status'] = 'silent';
            $this->addTransaction($request->all());
        }

        return 'success';
    }

    private function addTransaction($data)
    {
        $user_id = \Auth::check() ? auth()->user()->id : null;
        $invoices = [];
        $hash     = 'no-hash';
        \Log::info('Transaction data: ' . print_r($data, 1));

        if (isset($data['hash'])) {
            $hash = $data['hash'];
        }

        if (!isset($data['message'])) {
            $status = 'start';
        } else {
            $status = $data['approval'] ? 'success' : 'fail';
        }

        if (isset($data['status'])) {
            $status = $data['status'];
        }

        if (!empty($data['ExtraData'])) {
            $extra   = json_decode($data['ExtraData'], 1);
            $user_id = $extra['user_id'];
            $hash    = $this->customHash($extra);
            $invoices = $extra['invoices'];

            if (empty(Transaction::where('user_id', $user_id)->where('hash', $hash)->first())) {
                $status = 'suspect';
            }
        }

        if (isset($data['invoices'])) {
            $invoices = $data['invoices'];
        }

        $transaction           = new Transaction();
        $transaction->user_id  = $user_id;
        $transaction->invoices = json_encode($invoices);
        $transaction->hash     = $hash;
        $transaction->amount   = $data['amount'];
        $transaction->status   = $status;
        $transaction->response = json_encode($data);
        $transaction->save();

        return $transaction;
    }

    private function customHash($data)
    {
        return sha1($data['user_id'] . env('EUPLATESC_HASH_KEY') . $data['timestamp']);
    }

    private function hmacsha1($key, $data)
    {
        $blocksize = 64;
        $hashfunc  = 'md5';

        if (strlen($key) > $blocksize)
            $key = pack('H*', $hashfunc($key));

        $key  = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);

        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        return bin2hex($hmac);
    }

    private function euplatesc_mac($data, $key)
    {
        $str = NULL;

        foreach ($data as $d) {
            if ($d === NULL || strlen($d) == 0)
                $str .= '-';
            else
                $str .= strlen($d) . $d;
        }

        $key = pack('H*', $key);
        return $this->hmacsha1($key, $str);
    }

    public function generateBarCode()
    {
        try {
            $codes = ApiController::getInvoiceStrings();
        } catch (CustomException $e) {
            \Log::error('Preluare cod de bare : ' . $e->getMessage());
        } catch (GuzzleException $e) {
            \Log::error('Preluare cod de bare : ' . $e->getMessage());
        }

        if (!empty($codes)) {
            $generator = new BarcodeGeneratorPNG();

            foreach ($codes as $code) {
                $barcode  = $generator->getBarcode($code['string'], $generator::TYPE_CODE_128);
                $filename = 'barcodes/' . $code['iddocei'] . '.png';
                Image::read($barcode)->save(public_path($filename));

                try {
                    ApiController::sendBarcode($code['iddocei'], url($filename));
                } catch (CustomException $e) {
                    \Log::error('Salvare cod de bare : ' . $e->getMessage());
                } catch (GuzzleException $e) {
                    \Log::error('Salvare cod de bare : ' . $e->getMessage());
                }
            }
        }

        return 'success';
    }

    private function codeGenerator($iddocei, $clientID, $invoiceNumber, $invoiceDate, $totalAmount)
    {
        $code = 'SV';

        for ($i = 0; $i < (15 - strlen($iddocei)); $i++) {
            $code .= '0';
        }
        $code .= $iddocei;

        for ($i = 0; $i < (13 - strlen($clientID)); $i++) {
            $code .= '0';
        }
        $code .= $clientID;

        for ($i = 0; $i < (10 - strlen($invoiceNumber)); $i++) {
            $code .= '0';
        }
        $code .= $invoiceNumber;
        $code .= str_replace('-', '', $invoiceDate);

        $totalAmount = str_replace('.', '', $totalAmount);
        for ($i = 0; $i < (10 - strlen($totalAmount)); $i++) {
            $code .= '0';
        }
        $code .= $totalAmount;

        $sum = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $sum += intval($code[$i]);
        }

        $code .= ($sum % 9);

        return $code;
    }
}