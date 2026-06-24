<?php

// L11: App\Cms → App\Models\Cms etc.
use App\Models\Cms;
use App\Models\Setting;
use App\Models\UserNotification;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ApiController;

function isAdmin()
{
    if (auth()->user()) {
        return auth()->user()->hasRole(['admin', 'complaints_manager', 'notifications_manager', 'closingwater_manager', 'bulletinanalysis_manager', 'extern_manager']);
    }
    return false;
}

function apiRoute($string)
{
    return str_replace('https', 'http', route($string));
}

function getPage($id)
{
    $page = Cms::where('status', 'Activ')->where('id', $id)->first();
    return $page ?: false;
}

function getSetting($key)
{
    return Setting::where('key', $key)->pluck('value')->first();
}

function indexPeriod()
{
    $setting = getSetting('period');

    if (stristr($setting, '-')) {
        $setting = explode('-', $setting);
        $period['from'] = intval($setting[0]);
        $period['to']   = intval($setting[1]);
    } else {
        $period['from'] = intval($setting);
        $period['to']   = intval(date('t'));
    }

    return $period;
}

function getUnreadNotifications()
{
    $deletedNotifications = UserNotification::onlyTrashed()->get()->pluck('notification_id')->toArray();
    $userNotifications    = UserNotification::where('user_id', auth()->user()->id)->pluck('notification_id')->toArray();
    $ids                  = array_merge($deletedNotifications, $userNotifications);

    $userId = auth()->user()->id;
    return Notification::where('created_at', '>', (new Carbon(auth()->user()->created_at))->format('Y-m-d H:i:s'))
        ->where(function ($q) use ($userId) {
            $q->where('category', auth()->user()->category)->whereNull('sectors')
              ->orWhere('receiver_id', $userId)
              ->orWhere(function($q2) {
                  $q2->whereNull('receiver_id')->where('category', 'All')->whereNull('sectors');
              })
              ->orWhere(function($q3) use ($userId) {
                  $q3->whereNotNull('sectors')
                     ->whereRaw('JSON_CONTAINS(sectors, CAST(? AS JSON))', [$userId]);
              });
        })
        ->whereNotIn('id', $ids)
        ->orderBy('id', 'desc')
        ->with('type')
        ->get();
}

function invoiceExists($invoiceNumber, $invoiceDate, $clientCode = false)
{
    if (!auth()->user()->notify_invoice) {
        return false;
    }
    if (!$clientCode) {
        $clientCode = str_replace("/", "", auth()->user()->codes[0]["client_code"]);
    }
    $filename = $clientCode . "_" . str_replace("/", "", $invoiceNumber) . "_" . date("dmY", strtotime($invoiceDate));
    if (Storage::disk("local")->exists("invoices/" . $filename . ".pdf")) {
        return true;
    }
    $pattern = storage_path("app/invoices/") . $clientCode . "_" . str_replace("/", "", $invoiceNumber) . "_*.pdf";
    $files = glob($pattern) ?: [];
    if (empty($files)) {
        $files = glob(storage_path("app/invoices/*/*/") . $clientCode . "_" . str_replace("/", "", $invoiceNumber) . "_*.pdf") ?: [];
    }
    return !empty($files);
}

function paymentType($invoice_id)
{
    return ApiController::paymentType($invoice_id);
}
