@extends('layouts.pdf')
@section('content')
    <table class="hidden-table">
        <tr>
            <td colspan="3">
                <img class="logo" src="{{ asset('img/aqua.png') }}"/>
            </td>
        </tr>
        <tr>
            <td rowspan="2" class="company-td">
                <h1 class="left-title">Aquaserv S.A</h1>
                <p><span class="left-span">Nr.ord.reg.com./an:</span><span class="right-span">J36/348/2004</span></p>
                <p><span class="left-span">CIF:</span><span class="right-span">RO 16775941</span></p>
                <p>
                    <span class="left-span">Sediu social:</span><br/>
                    <span>str. Rezervorului , nr. 2,Tulcea, Romania</span>
                </p>
                <p><span class="left-span">Web:</span><span class="right-span">www.aquaservtulcea.ro</span>
                <p><span class="left-span">Tel:</span><span class="right-span">0240524310</span>
                <p>Banca BCR Tulcea</p>
                <p>RO46RNCB4600000178560001</p>
               
            </td>
            <td colspan="2" class="barcode-td" height="30">
                <img src="data:image/png;base64,{{ $pdfData['invoiceData']['barcode'] }}" alt="barcode"/>
                <p>{{ $pdfData['invoiceData']['code'] }}</p>
            </td>
        </tr>
        <tr>
            <td class="middle-td">
                <h1 class="left-title">Factura fiscala</h1>
                <div class="bordered-container">
                    <p>
                        <span class="left-span">Numar factura</span>
                        <span class="right-span">{{  $pdfData['invoiceData']['number'] }}</span>
                    </p>
                    <p>
                        <span class="left-span">Data facturii</span>
                        <span class="right-span">{{ $pdfData['invoiceData']['date'] }}</span>
                    </p>
                    <p>
                        <span class="left-span">Numar AIM/PV</span>
                        <span class="right-span">PV/000046211</span>
                    </p>
                    <p>
                        <span class="left-span">Numar contract</span>
                        <span class="right-span">1206 - 01/01/2000</span>
                    </p>
                    <p>
                        <span class="left-span">Cod client</span>
                        <span class="right-span">{{ $pdfData['clientData']['client_code'] }}</span>
                    </p>
                    <p>
                        <span class="left-span">Perioada facturare</span>
                        <span class="right-span pay-date-span">{{ $pdfData['invoiceData']['billedDate'] }}</span>
                    </p>
                    <p>
                        <span class="left-span">Data scadenta</span>
                        <span class="right-span">{{ $pdfData['invoiceData']['dueDate'] }}</span>
                    </p>
                    <p>
                        <span class="left-span">Numar pozitii</span>
                        <span class="right-span">{{ count($pdfData['invoiceData']['positions']) }}</span>
                    </p>
                </div>
            </td>
            <td class="buyer-td">
                <p>
                    <span class="left-span">Cumparator</span>
                    <span class="right-span">{{ $pdfData['clientData']['name'] }}</span>
                </p>
                <p>
                    <span class="left-span">Nr.ord.reg.com./an</span>
                    <span class="right-span">J33/134/1991</span>
                </p>
                <p><span class="left-span">C.I.F./C.N.P.</span><span class="right-span">RO717596</span></p>
                <p><span class="left-span">Strada</span><span class="right-span">GRIGORE URECHE</span></p>
                <p>
                    <span class="left-span">Numarul</span><span class="exception-span">19</span>
                    <span class="left-span">Bl.</span><span class="exception-span">19</span></p>
                <p>
                    <span class="left-span">Sc.</span><span class="exception-span">19</span>
                    <span class="left-span">Et.</span><span class="exception-span">19</span>
                    <span class="left-span">Ap.</span><span class="exception-span">19</span>
                </p>
                <p><span class="left-span">Localitatea</span><span class="right-span">Suceava</span></p>
                <p><span class="left-span">Judetul</span><span class="right-span">Suceava</span></p>
                <p>
                    <span class="left-span">Cod IBAN</span>
                    <span class="right-span iban-span">RO79.BRDE.340S.V029.7836.3400</span>
                </p>
                <p>
                    <span class="left-span">Banca</span>
                    <span class="right-span bank-span">BRD GROUP SOCIETE GENERALE SUCEAVA</span>
                </p>
            </td>
        </tr>
    </table>
    <p class="temei">
        Temei legal pret: Aviz ANRSC nr. 105737/13.06.2013; H.AJAC Suceava nr. 7/01.07.2013
        <span>Cota TVA 9%, 19%</span>
    </p>
    <table class="invoice-table">
        <thead>
        <tr style="text-align: center;">
            <th>Nr. Crt.</th>
            <th>Denumirea produselor sau a serviciilor</th>
            <th>U.M.</th>
            <th>Cantitatea</th>
            <th>Pret unitar<br/>(fara TVA) lei</th>
            <th>Valoarea<br/>- lei -</th>
            <th>Valoarea<br/>TVA - lei -</th>
        </tr>
        </thead>
        <tbody>
        <tr style="text-align: center; font-weight:bold;">
            @for($i=0; $i<7; $i++)
                <td>{{ $i }}</td>
            @endfor
        </tr>
        @foreach($pdfData['invoiceData']['positions'] as $position)
            <tr>
                <td style="text-align: center;">{{ $position['pozitie'] }}</td>
                <td style="text-align: center;">{{ $position['codprestatie'] }}</td>
                <td style="text-align: center;">MC</td>
                <td style="text-align: right;">{{ $position['cant'] }}</td>
                <td style="text-align: right;">{{ $position['pret'] }}</td>
                <td style="text-align: right;">{{ $position['valtotal'] }}</td>
                <td style="text-align: right;">{{ $position['tva'] }}</td>
            </tr>
        @endforeach
        <tfoot>
        <tr>
            <td rowspan="2" colspan="3"></td>
            <td colspan="2">Total</td>
            <td style="text-align: right;">{{ $pdfData['invoiceData']['totalWithoutVAT'] }}</td>
            <td style="text-align: right;">{{ $pdfData['invoiceData']['totalVAT'] }}</td>
        </tr>
        <tr>
            <td colspan="2">Total factura</td>
            <td colspan="2" style="text-align: right;">{{ $pdfData['invoiceData']['total'] }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Achitat in avans</td>
            <td colspan="2"></td>
            <td colspan="2" style="text-align: right;">{{ $pdfData['invoiceData']['payedBefore'] }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">Rest de plata</td>
            <td colspan="2"></td>
            <td colspan="2" style="text-align: right;">{{ $pdfData['invoiceData']['sumToPay'] }}</td>
        </tr>
        </tfoot>
        </tbody>
    </table>
    <p class="pay-instructions">FACTURA SE POATE PLATI UTILIZAND CODUL DE BARE INSCRIS PE FACTURA, LA PUNCTELE DE PLATA
        PAYPOINT,COMISIONUL
        PERCEPUT FIIND ZERO. VA RECOMANDAM SA PASTRATI CHITANTA.<br/>
        Va rugam sa achitati suma de {{ $pdfData['invoiceData']['total'] }} lei (total de plata) pana la data
        de {{ $pdfData['invoiceData']['dueDate'] }} (data scadentei). Factura
        constituie preaviz in
        caz de neplata.</p>
    <p class="disclaimer">
        Va informam ca in conformitate cu prevederile contractuale/legale neachitarea facturii in termen de 30 de zile
        calendaristice de la data scadentei
        atrage dupa sine calcularea de dobanzi de intarziere in cuantum de 0,02% pe zi, incepand cu data
        de {{ $pdfData['invoiceData']['dueDate'] }}
        (data scadentei) pana la
        achitarea integrala a facturii.<br/>
        Plata serviciilor se va efectua numai in baza facturilor emise, factura reprezentand singurul document legal de
        decontare intre furnizor si client,
        conform Codului Fiscal (Legea nr. 227/2015).<br/>
        Nu se poate efectua plata in baza altor documente, precum Procesul Verbal de stabilire a consumurilor, care nu
        contine (cel putin) urmatoarele
        informatii: c/val. serviciilor prestate, pret unitar, valoarea TVA, temeiul legal al preturilor practicate.<br/>
        La data emiterii facturii inregistrati un sold neachitat in suma de 11346.04 (total tabel 2 din Anexa) .<br/>
        Daca ati achitat deja facturile restante nu luati in considerare sumele specificate in tabelul 2 din Anexa.<br/>
    </p>
    <div class="footer-barcode">
        <img src="data:image/png;base64,{{ $pdfData['invoiceData']['barcode'] }}" alt="barcode"/>
        <p>{{ $pdfData['invoiceData']['code'] }}</p>
    </div>
    <div class="page-break"></div>
    <img class="logo" src="{{ asset('img/acetlogo.png') }}"/>
    <h1 class="left-title">ACET SA<br/>AGENTIA SUCEAVA</h1>
    <h1 class="left-title" style="text-align: center;">ANEXA</h1>
    <p style="text-align: center; margin-bottom: 30px;">la factura seria SV nr {{ $pdfData['invoiceData']['number'] }}
        din data {{ $pdfData['invoiceData']['date'] }}</p>
    <p class="small-title">1. Servicii furnizate in luna {{ $pdfData['invoiceData']['month'] }}
        anul {{ $pdfData['invoiceData']['year'] }}</p>
    <table class="anex-table1">
        <thead>
        <tr>
            <th>Nr. crt.</th>
            <th>Adresa locului de consum</th>
            <th>Denumirea produselor sau a serviciilor</th>
            <th>Index vechi</th>
            <th>Index nou</th>
            <th>U.M.</th>
            <th>Cantitatea</th>
            <th>Pret unitar (fara TVA) lei</th>
            <th>Valoarea - lei -</th>
            <th>Valoarea TVA - lei -</th>
        </tr>
        </thead>
        <tbody style="text-align: right;">
        <tr style="text-align: center; font-weight: bold;">
            @for($i=0; $i<10; $i++)
                <td>{{ $i }}</td>
            @endfor
        </tr>
        <tr style="text-align: left;">
            <td colspan="10">1 Strada Dornelor, Nr. FN, Suceava, Suceava, Romania, Cod postal: SV157\1</td>
        </tr>
        @foreach($pdfData['invoiceData']['positions'] as $position)
            <tr>
                @if($position['pozitie'] == 1)
                    <td colspan="2" rowspan="2"></td>
                @endif
                <td style="text-align: center;">{{ $position['codprestatie'] }}</td>
                <td>{{ $position['pozitie'] == 1 ? $pdfData['invoiceData']['oldIndex'] : null }}</td>
                <td>{{ $position['pozitie'] == 1 ? $pdfData['invoiceData']['newIndex'] : null }}</td>
                <td style="text-align: center;">MC</td>
                <td style="text-align: right;">{{ $position['cant'] }}</td>
                <td style="text-align: right;">{{ $position['pret'] }}</td>
                <td style="text-align: right;">{{ $position['valtotal'] }}</td>
                <td style="text-align: right;">{{ $position['tva'] }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td rowspan="2" colspan="6"></td>
            <td colspan="2">Total</td>
            <td style="text-align: right;">{{ $pdfData['invoiceData']['totalWithoutVAT'] }}</td>
            <td style="text-align: right;">{{ $pdfData['invoiceData']['totalVAT'] }}</td>
        </tr>
        <tr>
            <td colspan="2">Total de plata(col.8 + col.9)</td>
            <td style="text-align: center;" colspan="2">{{ $pdfData['invoiceData']['total'] }}</td>
        </tr>
        </tfoot>
    </table>
    <p class="small-title">2. Descifrare sold restant</p>
    <table class="invoice-table anex-table2" style="text-align: center">
        <thead style="font-size: 12px;">
        <tr>
            <th>Nr. crt.</th>
            <th>Numar factura</th>
            <th>Data facturii</th>
            <th>Valoare</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>2</td>
            <td>SV/170861</td>
            <td>31/01/2013</td>
            <td>699.00</td>
        </tr>
        <tr>
            <td>3</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>799.00</td>
        </tr>
        <tr>
            <td>4</td>
            <td>SV/170862</td>
            <td>31/12/2013</td>
            <td>799.00</td>
        </tr>
        <tr>
            <td>5</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>6</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>7</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>8</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>9</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>10</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>11</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>12</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>13</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>14</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>15</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>16</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>17</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>18</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>19</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>20</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        <tr>
            <td>21</td>
            <td>SV/170860</td>
            <td>31/12/2012</td>
            <td>599.00</td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td>11,346.04</td>
        </tr>
        </tfoot>
    </table>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <script type="text/php">
        $text = "Pagina {PAGE_NUM} / {PAGE_COUNT}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);




    </script>
@endsection
