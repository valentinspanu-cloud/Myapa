<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PortalClientiExport;
use App\Exports\ExternClientiExport;
use App\Exports\TotiClientiExport;
use App\Models\InvoiceExternClient;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PortalClientiController extends Controller
{
    /**
     * Pagina cu statistici și buton export
     */
    public function index()
    {
        $totalUseri      = DB::table('users')->count();
        $totalCoduri     = DB::table('client_codes')->count();
        $cuEmail         = DB::table('users')->whereNotNull('email')->where('email', '!=', '')->count();
        $verificati      = DB::table('users')->whereNotNull('email_verified_at')->count();
        $notifFactura    = DB::table('users')->where('notify_invoice', 1)->count();
        $faraContPlatit  = DB::table('users')->where('status', 0)->orWhereNull('status')->count();
        $totalExterni    = DB::table('invoice_extern_clients')->count();
        $totalToti       = $totalCoduri + $totalExterni;

        return view('admin.portal-clienti.index', compact(
            'totalUseri', 'totalCoduri', 'cuEmail',
            'verificati', 'notifFactura', 'faraContPlatit',
            'totalExterni', 'totalToti'
        ));
    }

    /**
     * Export clienți portal
     */
    public function export()
    {
        $fileName = 'clienti_portal_' . date('d_m_Y') . '.xlsx';
        return Excel::download(new PortalClientiExport(), $fileName);
    }

    /**
     * Export clienți externi
     */
    public function exportExterni()
    {
        $fileName = 'clienti_externi_' . date('d_m_Y') . '.xlsx';
        return Excel::download(new ExternClientiExport(), $fileName);
    }

    /**
     * Export toți clienții (portal + externi)
     */
    public function exportToti()
    {
        $fileName = 'toti_clientii_' . date('d_m_Y') . '.xlsx';
        return Excel::download(new TotiClientiExport(), $fileName);
    }
}
