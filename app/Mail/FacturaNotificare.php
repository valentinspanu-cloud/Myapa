<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class FacturaNotificare extends Mailable
{
    use Queueable, SerializesModels;

    public string $numeClient;
    public string $codClient;
    public string $nrFactura;
    public string $dataEmitere;
    public string $scadenta;
    public string $sold;
    public string $luna;
    public string $pdfPath;

    public function __construct(array $date, string $pdfPath)
    {
        $this->numeClient  = $date['nume'];
        $this->codClient   = $date['cod_client'];
        $this->nrFactura   = $date['nr_factura'];
        $this->dataEmitere = $date['data_emitere'];   // dd.mm.yyyy
        $this->scadenta    = $date['scadenta'];        // dd.mm.yyyy
        $this->sold        = $date['sold'];
        $this->luna        = $date['luna'];            // ex: Mai 2026
        $this->pdfPath     = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factură Aquaserv Tulcea – ' . $this->luna,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.factura',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Factura_' . $this->nrFactura . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
