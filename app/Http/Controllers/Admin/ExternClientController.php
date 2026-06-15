<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\InvoiceExternClient;
use Illuminate\Http\Request;

class ExternClientController extends Controller
{
    /**
     * Listă clienți externi
     */
    public function index(Request $request)
    {
        $search  = $request->get('search');
        $clienti = InvoiceExternClient::orderBy('nume')
            ->when($search, function($q) use ($search) {
                $q->where('cod_client', 'like', "%{$search}%")
                  ->orWhere('nume', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(100);

        return view('admin.facturi.extern-clienti', compact('clienti'));
    }

    /**
     * Salvează client nou (verifică în Oracle și ia client_id + nume automat)
     */
    public function store(Request $request)
    {
        $request->validate([
            'cod_client'  => 'required|string|unique:invoice_extern_clients,cod_client',
            'contract_nr' => 'required|string',
            'email'       => 'required|email',
        ], [
            'cod_client.unique' => 'Acest cod de client există deja.',
            'email.required'    => 'Emailul este obligatoriu.',
            'email.email'       => 'Emailul nu este valid.',
        ]);

        // Verifică în Oracle și ia client_id + nume
        $data = ApiController::getPersonalData([
            'cod_client'  => $request->cod_client,
            'nr_contract' => $request->contract_nr,
        ]);

        if (!$data) {
            return back()->withErrors(['cod_client' => 'Clientul nu a fost găsit în sistemul Oracle. Verificați codul și contractul.'])->withInput();
        }

        InvoiceExternClient::create([
            'cod_client'  => $request->cod_client,
            'contract_nr' => $request->contract_nr,
            'client_id'   => $data['id'],
            'nume'        => $data['nume'],
            'email'       => $request->email,
        ]);

        return redirect()->route('admin.extern-clienti.index')
            ->with('success', "Client {$data['nume']} adăugat cu succes.");
    }

    /**
     * Actualizează email client extern
     */
    public function update(Request $request, InvoiceExternClient $externClient)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $externClient->update(['email' => $request->email]);

        return redirect()->route('admin.extern-clienti.index')
            ->with('success', 'Email actualizat cu succes.');
    }

    /**
     * Șterge client extern
     */
    public function destroy(InvoiceExternClient $externClient)
    {
        $nume = $externClient->nume;
        $externClient->delete();

        return redirect()->route('admin.extern-clienti.index')
            ->with('success', "Clientul {$nume} a fost șters.");
    }

    /**
     * Import CSV bulk (cod_client, contract_nr, email)
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path  = $request->file('csv_file')->getRealPath();
        $rows  = $this->parseCsv($path);

        $importat = 0;
        $erori    = [];

        foreach ($rows as $i => $row) {
            $codClient  = trim($row['cod_client'] ?? '');
            $contractNr = trim($row['contract_nr'] ?? '');
            $email      = trim($row['email'] ?? '');

            if (!$codClient || !$contractNr || !$email) {
                $erori[] = "Linia " . ($i + 2) . ": date incomplete.";
                continue;
            }

            if (InvoiceExternClient::where('cod_client', $codClient)->exists()) {
                $erori[] = "Linia " . ($i + 2) . ": codul {$codClient} există deja, skip.";
                continue;
            }

            $data = ApiController::getPersonalData([
                'cod_client'  => $codClient,
                'nr_contract' => $contractNr,
            ]);

            if (!$data) {
                $erori[] = "Linia " . ($i + 2) . ": clientul {$codClient} nu a fost găsit în Oracle.";
                continue;
            }

            InvoiceExternClient::create([
                'cod_client'  => $codClient,
                'contract_nr' => $contractNr,
                'client_id'   => $data['id'],
                'nume'        => $data['nume'],
                'email'       => $email,
            ]);

            $importat++;
        }

        return redirect()->route('admin.extern-clienti.index')
            ->with('success', "Import finalizat: {$importat} clienți adăugați.")
            ->with('import_erori', $erori);
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) === false) return $rows;

        $headers = null;
        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($line) === 1) $line = str_getcsv($line[0], ';');

            if ($headers === null) {
                $headers = array_map(fn($h) => strtolower(trim(str_replace("\xEF\xBB\xBF", '', $h))), $line);
                continue;
            }

            if (count($line) !== count($headers)) continue;
            $rows[] = array_combine($headers, $line);
        }

        fclose($handle);
        return $rows;
    }
}
