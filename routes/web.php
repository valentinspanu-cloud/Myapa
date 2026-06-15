<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintTypeController;
use App\Http\Controllers\ComplaintStatusController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\SettingController;

// -------------------------------------------------------
// Auth routes (verificare email obligatorie)
// -------------------------------------------------------
Auth::routes(['verify' => true]);

// -------------------------------------------------------
// Rute publice (fără autentificare)
// -------------------------------------------------------
Route::get('/pagina/{slug}', [CmsController::class, 'view'])->name('cms.view');

// Rute apelate extern / fără middleware auth (webhook-uri, notificări automate)
Route::get('notificari/trimite-perioadaindex', [NotificationController::class, 'createIndexNotification'])->name('notification.indexnotif');
Route::get('notificari/trimite-factura',       [NotificationController::class, 'createInvoiceNotification'])->name('notification.send.invoice.notif');
Route::get('notificari/trimite',               [NotificationController::class, 'send'])->name('notification.send');
Route::get('notificari/test',                  [CmsController::class, 'getLastInvoice']);
Route::post('facturi/inregistrare-tranzactie', [InvoiceController::class, 'silentTransaction'])->name('invoice.silentTransaction');

// -------------------------------------------------------
// Rute protejate — autentificare + email verificat
// -------------------------------------------------------
Route::middleware(['auth', 'verified', 'get_locations', 'get_locationsAll', 'revalidate'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('logout', [LoginController::class, 'logout']);

    // Index (citire contor)
    Route::get('index',                          [IndexController::class, 'index'])->name('index.list');
    Route::post('index/trimite-index',           [IndexController::class, 'store'])->name('index.store');
    Route::post('index/preluare-contori',        [IndexController::class, 'getWaterMeters'])->name('index.get.waterMeters');
    Route::post('index/preluare-indecsi',        [IndexController::class, 'getIndexes'])->name('index.get.indexes');

    // Facturi
    Route::get('facturi',                                    [InvoiceController::class, 'index'])->name('invoice.list');
    Route::post('facturi/istoric-facturi',                   [InvoiceController::class, 'invoices'])->name('invoice.history');
Route::get('facturi/istoric', [App\Http\Controllers\InvoiceController::class, 'historyAjax'])->name('invoice.history.ajax')->middleware(['auth', 'role:consumer']);
    Route::post('facturi/plateste-avans', [InvoiceController::class, 'payAdvance'])->name('invoice.payAdvance');
    Route::post('facturi/plateste-avans', [InvoiceController::class, 'payAdvance'])->name('invoice.payAdvance');
    Route::post('facturi/plateste',                         [InvoiceController::class, 'pay'])->name('invoice.pay');
    Route::post('facturi/plata-finalizata',                 [InvoiceController::class, 'thankYou'])->name('invoice.thanks');
    Route::get('facturi/factura/{id}',                      [InvoiceController::class, 'show'])->name('invoice.single');
    Route::get('facturi/descarca-factura/{id}/{date}',      [InvoiceController::class, 'download'])->name('invoice.pdf');

    // Contul meu (consumer)
    Route::middleware('role:consumer')->group(function () {
        Route::get('contul-meu',                            [UserController::class, 'account'])->name('user.account');
        Route::put('contul-meu/actualizeaza/{user}',        [UserController::class, 'patch'])->name('users.patch');
        Route::put('contul-meu/adauga-locatii/{user}',      [UserController::class, 'addLocations'])->name('users.addLocations');
        Route::put('contul-meu/locatii/{user}',             [UserController::class, 'changeLoc'])->name('users.changeLoc');
    });

    // Utilizatori (admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('utilizatori',                           [UserController::class, 'index'])->name('users');
        Route::get('utilizatori/lista',                     [UserController::class, 'users'])->name('users.get');
        Route::get('utilizatori/creeaza',                   [UserController::class, 'create'])->name('users.create');
        Route::post('utilizatori/salveaza',                 [UserController::class, 'store'])->name('users.store');
        Route::get('utilizatori/modifica/{user}',           [UserController::class, 'edit'])->name('users.edit');
        Route::put('utilizatori/modifica-consumator/{user}',[UserController::class, 'updateConsumer'])->name('users.consumer.update');
        Route::put('utilizatori/actualizeaza/{user}',       [UserController::class, 'update'])->name('users.update');
    });
    Route::delete('utilizatori/sterge/{user}',             [UserController::class, 'destroy'])->name('users.delete')->middleware('role:admin|consumer');

    // Sesizări (consumer)
    Route::middleware('role:consumer')->group(function () {
        Route::match(['get','post'], 'sesizari',            [ComplaintController::class, 'index'])->name('complaints');
        Route::match(['get','post'], 'sesizari/salveaza',   [ComplaintController::class, 'store'])->name('complaints.store');
    });
    Route::match(['get','post'], 'sesizari/editeaza/{complaint}', [ComplaintController::class, 'edit'])->name('complaints.edit')->middleware('role:complaints_manager');
    Route::put('sesizari/actualizeaza/{complaint}',         [ComplaintController::class, 'update'])->name('complaints.update')->middleware('role:complaints_manager');
    Route::get('sesizari/preluare/{complaint}',             [ComplaintController::class, 'takeOver'])->name('complaints.takeover')->middleware('role:complaints_manager');
    Route::get('sesizari/vizualizare/{complaint}',          [ComplaintController::class, 'show'])->name('complaints.show');
    Route::delete('sesizari/stergere/{complaint}',          [ComplaintController::class, 'destroy'])->name('complaints.delete');
    Route::delete('sterge-sesizari',                        [ComplaintController::class, 'destroyAll'])->name('complaints.destroyAll');
    Route::get('administrare-sesizari',                     [ComplaintController::class, 'admin'])->name('complaints.admin')->middleware('role:admin|complaints_manager');
    Route::get('listare-sesizari',                          [ComplaintController::class, 'getComplaints'])->name('complaints.list')->middleware('role:admin|complaints_manager');

    // Tipuri sesizări (admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('sesizari/tipuri',                       [ComplaintTypeController::class, 'index'])->name('complaintType.list');
        Route::get('sesizari/tipuri/tip-nou',               [ComplaintTypeController::class, 'create'])->name('complaintType.create');
        Route::post('sesizari/tipuri/salveaza-tipul',       [ComplaintTypeController::class, 'store'])->name('complaintType.store');
        Route::get('sesizari/tipuri/edit/{type}',           [ComplaintTypeController::class, 'edit'])->name('complaintType.edit');
        Route::put('sesizari/tipuri/update/{type}',         [ComplaintTypeController::class, 'update'])->name('complaintType.update');

        // Statusuri sesizări
        Route::get('sesizari/statusuri',                    [ComplaintStatusController::class, 'index'])->name('complaintStatus.list');
        Route::get('sesizari/statusuri/status-nou',         [ComplaintStatusController::class, 'create'])->name('complaintStatus.create');
        Route::post('sesizari/statusuri/salveaza-statusul', [ComplaintStatusController::class, 'store'])->name('complaintStatus.store');
        Route::get('sesizari/statusuri/edit/{status}',      [ComplaintStatusController::class, 'edit'])->name('complaintStatus.edit');
        Route::put('sesizari/statusuri/update/{status}',    [ComplaintStatusController::class, 'update'])->name('complaintStatus.update');
    });

    // Tipuri notificări (admin) - TREBUIE inainte de ruta cu parametru
    Route::get('notificari/tipuri',                     [NotificationTypeController::class, 'index'])->name('notificationType.list');
    Route::get('notificari/tipuri/tip-nou',             [NotificationTypeController::class, 'create'])->name('notificationType.create');
    Route::post('notificari/tipuri/salveaza-tipul',     [NotificationTypeController::class, 'store'])->name('notificationType.store');
    Route::get('notificari/tipuri/edit/{type}',         [NotificationTypeController::class, 'edit'])->name('notificationType.edit');
    Route::put('notificari/tipuri/update/{type}',       [NotificationTypeController::class, 'update'])->name('notificationType.update');
    // Notificări (consumer)
    Route::get('notificari',                                [NotificationController::class, 'index'])->name('notification.list')->middleware('role:consumer');
    Route::get('notificari/{notification}',                 [NotificationController::class, 'show'])->name('notification.view');
    Route::delete('notificari/sterge/{id}', [NotificationController::class, 'deleteNotification'])->name('notification.delete');
        Route::post('sterge-notificare/{id}',                   [NotificationController::class, 'destroy'])->name('notification.destroy');
    Route::post('sterge-notificari',                        [NotificationController::class, 'destroyAll'])->name('notification.destroyAll');

    // Administrare notificări (admin/manager)
    Route::middleware('role:admin|notifications_manager')->group(function () {
        Route::get('administrare-notificari',                           [NotificationController::class, 'admin'])->name('notification.admin');
        Route::get('administrare-notificari/notificare-noua',           [NotificationController::class, 'create'])->name('notification.create');
        Route::post('administrare-notificari/salveaza-notificarea',     [NotificationController::class, 'store'])->name('notification.store');
        Route::get('listare-notificari',                                [NotificationController::class, 'getNotifications'])->name('notifications.json.list');
    });



    // CMS + Setări (admin)
    Route::middleware('role:admin|closingwater_manager|bulletinanalysis_manager')->group(function () {
        Route::get('pagini',                                [CmsController::class, 'index'])->name('cms');
        Route::get('pagini/edit/{page}',                    [CmsController::class, 'edit'])->name('cms.edit');
        Route::put('pagini/update/{cms}',                   [CmsController::class, 'update'])->name('cms.update');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('setari',                                [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('setari/actualizeaza',                  [SettingController::class, 'update'])->name('settings.update');
    });
});


// Vizualizare log (doar admin)
Route::get('admin/logs', function() {
    if (!isAdmin()) abort(403);
    $log = file_get_contents(storage_path('logs/laravel.log'));
    $lines = explode("\n", $log);

    $mails = [];
    foreach ($lines as $line) {
        if (str_contains($line, 'Mail trimis catre:')) {
            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $date);
            preg_match('/Mail trimis catre: (.+)/', $line, $email);
            $emailAddr = trim($email[1] ?? '');
            // Cauta userul in baza
            $user = \App\Models\User::where('email', $emailAddr)
                ->with('codes')
                ->first();
            $mails[] = [
                'date'  => $date[1] ?? '',
                'email' => $emailAddr,
                'name'  => $user ? $user->name : '-',
                'cod'   => $user && $user->codes->count() ? $user->codes[0]['client_code'] : '-',
            ];
        }
    }
    $mails = array_reverse($mails);

    $html = '<html><head><meta charset="utf-8"><style>'
        . 'body{font-family:sans-serif;background:#f8fafc;padding:20px;margin:0;}'
        . 'table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);}'
        . 'th{background:#0f172a;color:white;padding:10px 14px;text-align:left;font-size:12px;text-transform:uppercase;}'
        . 'td{padding:10px 14px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#374151;}'
        . 'tr:last-child td{border-bottom:none;}'
        . 'tr:hover td{background:#f8fafc;}'
        . 'h2{color:#0f172a;margin-bottom:16px;font-size:18px;}'
        . '</style></head><body>'
        . '<h2>Mailuri trimise - ' . count($mails) . ' total</h2>'
        . '<table><tr><th>#</th><th>Data</th><th>Email</th><th>Nume</th><th>Cod client</th></tr>';
    foreach ($mails as $i => $m) {
        $html .= '<tr>'
            . '<td>' . ($i+1) . '</td>'
            . '<td>' . $m['date'] . '</td>'
            . '<td>' . htmlspecialchars($m['email']) . '</td>'
            . '<td>' . htmlspecialchars($m['name']) . '</td>'
            . '<td>' . htmlspecialchars($m['cod']) . '</td>'
            . '</tr>';
    }
    $html .= '</table></body></html>';
    return response($html);
})->middleware(['auth', 'verified', 'role:admin']);

// Vizualizare plati (doar admin)
Route::get('admin/plati', function() {
    if (!isAdmin()) abort(403);

    $transactions = \DB::table('transactions_history')
        ->leftJoin('users', 'users.id', '=', 'transactions_history.user_id')
        ->leftJoin('client_codes', 'client_codes.user_id', '=', 'transactions_history.user_id')
        ->select(
            'transactions_history.*',
            'users.name as user_name',
            'users.email as user_email',
            'client_codes.client_code'
        )
        ->orderBy('transactions_history.created_at', 'desc')
        ->limit(200)
        ->get();

    $html = '<html><head><meta charset="utf-8"><style>'
        . 'body{font-family:sans-serif;background:#f8fafc;padding:20px;margin:0;}'
        . 'table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);}'
        . 'th{background:#0f172a;color:white;padding:10px 14px;text-align:left;font-size:12px;text-transform:uppercase;}'
        . 'td{padding:10px 14px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#374151;}'
        . 'tr:last-child td{border-bottom:none;}'
        . 'tr:hover td{background:#f8fafc;}'
        . '.success{color:#16a34a;font-weight:600;}'
        . '.fail{color:#dc2626;font-weight:600;}'
        . '.start{color:#9ca3af;}'
        . '.silent{color:#3b82f6;}'
        . 'h2{color:#0f172a;margin-bottom:16px;font-size:18px;}'
        . '</style></head><body>'
        . '<h2>Plati - ' . count($transactions) . ' total</h2>'
        . '<table><tr>'
        . '<th>#</th><th>Data</th><th>Nume</th><th>Email</th><th>Cod client</th><th>Suma</th><th>Facturi</th><th>Status</th>'
        . '</tr>';

    foreach ($transactions as $i => $t) {
        $invoices = $t->invoices ?? '-';
        $statusClass = $t->status == 'success' ? 'success' : ($t->status == 'fail' ? 'fail' : ($t->status == 'silent' ? 'silent' : 'start'));
        $html .= '<tr>'
            . '<td>' . ($i+1) . '</td>'
            . '<td>' . $t->created_at . '</td>'
            . '<td>' . htmlspecialchars($t->user_name ?? '-') . '</td>'
            . '<td>' . htmlspecialchars($t->user_email ?? '-') . '</td>'
            . '<td>' . htmlspecialchars($t->client_code ?? '-') . '</td>'
            . '<td>' . $t->amount . ' RON</td>'
            . '<td>' . htmlspecialchars($invoices) . '</td>'
            . '<td class="' . $statusClass . '">' . $t->status . '</td>'
            . '</tr>';
    }
    $html .= '</table></body></html>';
    return response($html);
})->middleware(['auth', 'verified', 'role:admin']);
// Test email index - sterge dupa test
Route::get('test-index-mail', function() {
    if (!isAdmin()) abort(403);
    $user = \App\Models\User::with('codes')->find(auth()->user()->id);
    $sold = \App\Http\Controllers\ApiController::getSoldForUser($user);
    \Mail::to($user->email)->send(new \App\Mail\IndexReminder($user, $sold));
    return 'Mail trimis la: ' . $user->email . ' | Nume: ' . $user->name . ' | Cod: ' . ($user->codes[0]['client_code'] ?? '-') . ' | Sold: ' . $sold . ' | Data: ' . now()->format('d.m.Y H:i:s');
})->middleware(['auth', 'verified']);

// Test email index client specific - sterge dupa test
Route::get('test-index-mail/{id}', function($id) {
    if (!isAdmin()) abort(403);
    try {
        $user = \App\Models\User::with('codes')->find($id);
        if (!$user) return 'User negasit';
        $sold = \App\Http\Controllers\ApiController::getSoldForUser($user);
        \Mail::to($user->email)->send(new \App\Mail\IndexReminder($user, $sold));
        \Log::info('INDEX Mail trimis catre: ' . $user->email . ' | ' . $user->name . ' | Cod: ' . ($user->codes[0]['client_code'] ?? '-') . ' | Sold: ' . $sold);
        return 'Mail trimis la: ' . $user->email . ' | Nume: ' . $user->name . ' | Cod: ' . ($user->codes[0]['client_code'] ?? '-') . ' | Sold: ' . $sold . ' | Data: ' . now()->format('d.m.Y H:i:s');
    } catch (\Exception $e) {
        return 'Eroare: ' . $e->getMessage() . ' | Linia: ' . $e->getLine() . ' | Fisier: ' . $e->getFile();
    }
})->middleware(['auth', 'verified', 'role:admin']);

// Vizualizare log index (doar admin)
Route::get('admin/logs-index', function() {
    if (!isAdmin()) abort(403);
    $log = file_get_contents(storage_path('logs/laravel.log'));
    $lines = explode("\n", $log);
    $index_logs = [];
    $index_mails = [];
    foreach ($lines as $line) {
        if (str_contains($line, 'INDEX Mail trimis catre:')) {
            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $date);
            preg_match('/INDEX Mail trimis catre: ([^\|]+)\|\s*([^\|]+)\|\s*Cod: ([^\|]+)\|\s*Sold: ([^\|]+)/', $line, $m);
            $index_mails[] = [
                'date'  => $date[1] ?? '',
                'email' => trim($m[1] ?? ''),
                'name'  => trim($m[2] ?? '-'),
                'cod'   => trim($m[3] ?? '-'),
                'sold'  => trim($m[4] ?? '-'),
            ];
        }
        if (str_contains($line, 'CRON INDEX:')) {
            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $date);
            preg_match('/CRON INDEX: (.+)/', $line, $msg);
            $index_logs[] = [
                'date' => $date[1] ?? '',
                'msg'  => trim($msg[1] ?? ''),
                'type' => str_contains($line, 'Eroare') ? 'err' : (str_contains($line, 'Finalizat') ? 'ok' : 'info'),
            ];
        }
    }
    $index_mails = array_reverse($index_mails);
    $index_logs  = array_reverse($index_logs);
    $html = '<html><head><meta charset="utf-8"><style>'
        . 'body{font-family:sans-serif;background:#f8fafc;padding:20px;margin:0;}'
        . 'table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:30px;}'
        . 'th{background:#0C2340;color:white;padding:10px 14px;text-align:left;font-size:12px;text-transform:uppercase;}'
        . 'td{padding:10px 14px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#374151;}'
        . 'tr:last-child td{border-bottom:none;} tr:hover td{background:#f8fafc;}'
        . '.ok td{color:#16a34a;font-weight:600;} .err td{color:#dc2626;font-weight:600;}'
        . 'h2{color:#0C2340;margin-bottom:16px;font-size:18px;border-left:4px solid #0C2340;padding-left:10px;}'
        . '</style></head><body>'
        . '<h2>Mailuri index trimise - ' . count($index_mails) . ' total</h2>'
        . '<table><tr><th>#</th><th>Data</th><th>Email</th><th>Nume</th><th>Cod client</th><th>Sold</th></tr>';
    foreach ($index_mails as $i => $m) {
        $html .= '<tr>'
            . '<td>' . ($i+1) . '</td>'
            . '<td>' . $m['date'] . '</td>'
            . '<td>' . htmlspecialchars($m['email']) . '</td>'
            . '<td>' . htmlspecialchars($m['name']) . '</td>'
            . '<td>' . htmlspecialchars($m['cod']) . '</td>'
            . '<td>' . htmlspecialchars($m['sold']) . '</td>'
            . '</tr>';
    }
    $html .= '</table>';
    $html .= '<h2>Log activitate index - ' . count($index_logs) . ' inregistrari</h2>';
    $html .= '<table><tr><th>#</th><th>Data</th><th>Mesaj</th></tr>';
    foreach ($index_logs as $i => $l) {
        $html .= '<tr class="' . $l['type'] . '">' 
            . '<td>' . ($i+1) . '</td>'
            . '<td>' . $l['date'] . '</td>'
            . '<td>' . htmlspecialchars($l['msg']) . '</td>'
            . '</tr>';
    }
    $html .= '</table></body></html>';
    return response($html);
})->middleware(['auth', 'verified']);

// ── Facturi Bulk Mail (doar admin) ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('facturi/trimite',               [\App\Http\Controllers\Admin\FacturaBulkController::class,   'trimite'])       ->name('facturi.trimite');
    Route::get ('portal-clienti',                [\App\Http\Controllers\Admin\PortalClientiController::class, 'index'])         ->name('portal-clienti.index');
    Route::get ('portal-clienti/export',         [\App\Http\Controllers\Admin\PortalClientiController::class, 'export'])        ->name('portal-clienti.export');
    Route::get ('portal-clienti/export-externi', [\App\Http\Controllers\Admin\PortalClientiController::class, 'exportExterni']) ->name('portal-clienti.export-externi');
    Route::get ('portal-clienti/export-toti',    [\App\Http\Controllers\Admin\PortalClientiController::class, 'exportToti'])    ->name('portal-clienti.export-toti');
});

// ── Facturi + Clienți externi (admin + extern_manager) ────────────────────
Route::middleware(['auth', 'verified', 'role:admin|extern_manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get ('facturi',              [\App\Http\Controllers\Admin\FacturaBulkController::class, 'index'])      ->name('facturi.index');
    Route::get ('facturi/export',       [\App\Http\Controllers\Admin\FacturaBulkController::class, 'export'])     ->name('facturi.export');
    Route::get ('facturi/batch-status', [\App\Http\Controllers\Admin\FacturaBulkController::class, 'batchStatus'])->name('facturi.batch-status');
    Route::get ('facturi/log',          [\App\Http\Controllers\Admin\FacturaBulkController::class, 'log'])        ->name('facturi.log');
    Route::get   ('extern-clienti',                [\App\Http\Controllers\Admin\ExternClientController::class, 'index'])  ->name('extern-clienti.index');
    Route::post  ('extern-clienti',                [\App\Http\Controllers\Admin\ExternClientController::class, 'store'])  ->name('extern-clienti.store');
    Route::put   ('extern-clienti/{externClient}', [\App\Http\Controllers\Admin\ExternClientController::class, 'update']) ->name('extern-clienti.update');
    Route::delete('extern-clienti/{externClient}', [\App\Http\Controllers\Admin\ExternClientController::class, 'destroy'])->name('extern-clienti.destroy');
    Route::post  ('extern-clienti/import',         [\App\Http\Controllers\Admin\ExternClientController::class, 'import']) ->name('extern-clienti.import');
});
