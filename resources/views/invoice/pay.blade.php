@extends('layouts.app')
@section('title', trans('general.pages.invoice.thankYou_title'))
@section('content')
    <div id="content-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('general.pages.invoice.thankYou_title')</h1>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="bg-white shadow-sm box-container">
                        <form ACTION="https://secure.euplatesc.ro/tdsprocess/tranzactd.php" METHOD="POST"
                              name="gateway" target="_self">
                            <p class="tx_red_mic">Transferring to EuPlatesc.ro gateway</p>
                            <p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif"
                                    alt="" title="" onload="javascript:document.gateway.submit()"></p>
                            <input name="fname" type="hidden" value="{{ $dataBill['fname'] }}"/>
                            {{--<input name="lname" type="hidden" value="{{ $dataBill['lname'] }}"/>--}}
                            <input name="country" type="hidden" value="{{ $dataBill['country'] }}"/>
                            <input name="company" type="hidden" value="{{ $dataBill['company'] }}"/>
                            <input name="city" type="hidden" value="{{ $dataBill['city'] }}"/>
                            <input name="add" type="hidden" value="{{ $dataBill['add'] }}"/>
                            <input name="email" type="hidden" value="{{ $dataBill['email'] }}"/>
                            <input name="phone" type="hidden" value="{{ $dataBill['phone'] }}"/>
                            <input name="ExtraData" type="hidden" value="{{ $dataBill['ExtraData'] }}"/>

                            <input type="hidden" NAME="amount" VALUE="{{ $dataAll['amount']}}" SIZE="12"
                                   MAXLENGTH="12"/>
                            <input TYPE="hidden" NAME="curr" VALUE="{{ $dataAll['curr'] }}" SIZE="5"
                                   MAXLENGTH="3"/>
                            <input type="hidden" NAME="invoice_id" VALUE="{{ $dataAll['invoice_id'] }}"
                                   SIZE="32" MAXLENGTH="32"/>
                            <input type="hidden" NAME="order_desc" VALUE="{{ $dataAll['order_desc'] }}"
                                   SIZE="32" MAXLENGTH="50"/>
                            <input TYPE="hidden" NAME="merch_id" SIZE="15" VALUE="{{ $dataAll['merch_id'] }}"/>
                            <input TYPE="hidden" NAME="timestamp" SIZE="15" VALUE="{{ $dataAll['timestamp'] }}"/>
                            <input TYPE="hidden" NAME="nonce" SIZE="35" VALUE="{{ $dataAll['nonce'] }}"/>
                            <input TYPE="hidden" NAME="fp_hash" SIZE="40"
                                   VALUE="{{ $dataAll['fp_hash'] }}"/>
                            <p><a href="javascript:gateway.submit();" class="txtCheckout">Go Now!</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
