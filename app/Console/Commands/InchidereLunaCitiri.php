<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\CitireContor;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
class InchidereLunaCitiri extends Command
{
    protected $signature   = 'cititori:inchidere-luna {--luna=} {--an=}';
    protected $description = 'Marcheaza citirile neconfirmate din luna anterioara si trimite email';
    public function handle(): int
    {
        $luna = $this->option('luna') ?? now()->subMonth()->month;
        $an   = $this->option('an')   ?? now()->subMonth()->year;
        $this->info("Inchidere luna {$luna}/{$an}...");

        // Citiri neconfirmate
        $neconfirmate = CitireContor::where('luna', $luna)
            ->where('an', $an)
            ->whereIn('status', ['nou', 'eroare', 'respins'])
            ->with('cititor')
            ->get();
        $this->info("Citiri neconfirmate: " . $neconfirmate->count());

        // Marcam ca expirat
        if ($neconfirmate->count() > 0) {
            CitireContor::where('luna', $luna)
                ->where('an', $an)
                ->whereIn('status', ['nou', 'eroare', 'respins'])
                ->update(['status' => 'expirat']);
        }

        // Statistici per ruta
        $rute = CitireContor::where('luna', $luna)
            ->where('an', $an)
            ->selectRaw("ruta,
                count(*) as total,
                sum(case when status = 'confirmat' then 1 else 0 end) as confirmate,
                sum(case when status = 'expirat' then 1 else 0 end) as expirate,
                sum(case when status in ('nou','eroare','respins') then 1 else 0 end) as neconfirmate")
            ->groupBy('ruta')
            ->orderBy('ruta')
            ->get();

        $totalTotal      = $rute->sum('total');
        $totalConfirmate = $rute->sum('confirmate');
        $totalExpirate   = $rute->sum('expirate');
        $totalNecnf      = $rute->sum('neconfirmate');
        $lunaText        = Carbon::create($an, $luna, 1)->locale('ro')->isoFormat('MMMM YYYY');

        // Randuri tabel per ruta
        $randuri = '';
        foreach ($rute as $r) {
            $pct = $r->total > 0 ? round(($r->confirmate / $r->total) * 100) : 0;
            $culoare = $pct >= 90 ? '#16a34a' : ($pct >= 70 ? '#d97706' : '#dc2626');
            $randuri .= "
            <tr>
                <td style='padding:8px 12px;border-bottom:1px solid #e5e7eb;font-weight:600;color:#1e3a5f'>{$r->ruta}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #e5e7eb;text-align:center'>{$r->total}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #e5e7eb;text-align:center;color:#16a34a;font-weight:600'>{$r->confirmate}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #e5e7eb;text-align:center;color:#dc2626'>{$r->expirate}</td>
                <td style='padding:8px 12px;border-bottom:1px solid #e5e7eb;text-align:center'>
                    <span style='background:{$culoare};color:white;padding:2px 8px;border-radius:12px;font-size:12px'>{$pct}%</span>
                </td>
            </tr>";
        }

        // Detalii neconfirmate
        $detalii = '';
        foreach ($neconfirmate as $c) {
            $detalii .= "
            <tr>
                <td style='padding:6px 12px;border-bottom:1px solid #f3f4f6;font-family:monospace'>{$c->cod_abonat}</td>
                <td style='padding:6px 12px;border-bottom:1px solid #f3f4f6'>{$c->ruta}</td>
                <td style='padding:6px 12px;border-bottom:1px solid #f3f4f6'>" . ($c->index_citit ?? '—') . "</td>
                <td style='padding:6px 12px;border-bottom:1px solid #f3f4f6;color:#6b7280'>" . ($c->cititor?->name ?? '—') . "</td>
                <td style='padding:6px 12px;border-bottom:1px solid #f3f4f6;color:#d97706'>{$c->status}</td>
            </tr>";
        }

        $detaliiSection = $neconfirmate->count() > 0 ? "
        <h3 style='color:#dc2626;margin:24px 0 12px'>Citiri neconfirmate/expirate ({$neconfirmate->count()})</h3>
        <table style='width:100%;border-collapse:collapse;font-size:13px'>
            <thead>
                <tr style='background:#fef2f2'>
                    <th style='padding:8px 12px;text-align:left;color:#991b1b'>Cod abonat</th>
                    <th style='padding:8px 12px;text-align:left;color:#991b1b'>Rută</th>
                    <th style='padding:8px 12px;text-align:left;color:#991b1b'>Index citit</th>
                    <th style='padding:8px 12px;text-align:left;color:#991b1b'>Cititor</th>
                    <th style='padding:8px 12px;text-align:left;color:#991b1b'>Status</th>
                </tr>
            </thead>
            <tbody>{$detalii}</tbody>
        </table>" : "<p style='color:#16a34a;font-weight:600'>✅ Toate citirile au fost confirmate!</p>";

        $html = "
        <div style='font-family:Arial,sans-serif;max-width:700px;margin:0 auto'>
            <div style='background:#1e3a5f;color:white;padding:20px 24px;border-radius:8px 8px 0 0'>
                <h2 style='margin:0;font-size:20px'>📊 Raport închidere lună citiri</h2>
                <p style='margin:4px 0 0;opacity:0.8'>{$lunaText} — Aquaserv Tulcea</p>
            </div>
            <div style='background:#f8fafc;padding:20px 24px;border:1px solid #e2e8f0'>
                <div style='display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px'>
                    <div style='background:white;border-radius:8px;padding:12px;text-align:center;border:1px solid #e2e8f0'>
                        <div style='font-size:24px;font-weight:bold;color:#1e3a5f'>{$totalTotal}</div>
                        <div style='font-size:12px;color:#6b7280'>Total citiri</div>
                    </div>
                    <div style='background:white;border-radius:8px;padding:12px;text-align:center;border:1px solid #e2e8f0'>
                        <div style='font-size:24px;font-weight:bold;color:#16a34a'>{$totalConfirmate}</div>
                        <div style='font-size:12px;color:#6b7280'>Confirmate</div>
                    </div>
                    <div style='background:white;border-radius:8px;padding:12px;text-align:center;border:1px solid #e2e8f0'>
                        <div style='font-size:24px;font-weight:bold;color:#dc2626'>{$totalExpirate}</div>
                        <div style='font-size:12px;color:#6b7280'>Expirate</div>
                    </div>
                    <div style='background:white;border-radius:8px;padding:12px;text-align:center;border:1px solid #e2e8f0'>
                        <div style='font-size:24px;font-weight:bold;color:#d97706'>{$totalNecnf}</div>
                        <div style='font-size:12px;color:#6b7280'>Neconfirmate</div>
                    </div>
                </div>
                <h3 style='color:#1e3a5f;margin:0 0 12px'>Statistici per rută</h3>
                <table style='width:100%;border-collapse:collapse;font-size:13px;background:white;border-radius:8px;overflow:hidden'>
                    <thead>
                        <tr style='background:#1e3a5f;color:white'>
                            <th style='padding:10px 12px;text-align:left'>Rută</th>
                            <th style='padding:10px 12px;text-align:center'>Total</th>
                            <th style='padding:10px 12px;text-align:center'>Confirmate</th>
                            <th style='padding:10px 12px;text-align:center'>Expirate</th>
                            <th style='padding:10px 12px;text-align:center'>Progres</th>
                        </tr>
                    </thead>
                    <tbody>{$randuri}</tbody>
                </table>
                {$detaliiSection}
            </div>
            <div style='background:#f1f5f9;padding:12px 24px;border-radius:0 0 8px 8px;font-size:12px;color:#6b7280;border:1px solid #e2e8f0;border-top:none'>
                Generat automat de sistemul MyAPA — Aquaserv Tulcea
            </div>
        </div>";

        Mail::html($html, function($msg) use ($luna, $an) {
            $msg->to('contractare@aquaservtulcea.ro')
                ->subject("📊 Raport citiri {$luna}/{$an} — Aquaserv Tulcea");
        });
        $this->info("Email trimis la contractare@aquaservtulcea.ro");

        $confirmate = CitireContor::where('luna', $luna)->where('an', $an)->where('status', 'confirmat')->count();
        $this->info("Confirmate: {$confirmate}");
        return self::SUCCESS;
    }
}
