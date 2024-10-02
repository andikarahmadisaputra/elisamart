@extends('layouts.member')

@section('content')
    <div class="container mt-4 mb-5" style="padding-bottom: 80px;">
        
        @if(session('success'))
            <div class="alert alert-success" role="alert"> 
                {{ session('success') }}
            </div>
        @endif

        <div class="card text-center shadow-lg mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #3a3a5c, #4d4d73); border: none;">
            <div class="card-header fw-bold" style="border-radius: 15px 15px 0 0; background: #515170; color: #FFD700; text-shadow: 1px 1px 2px #000;">
                Ringkasan Belanja
            </div>

            <div class="card-body">
                <h5 class="card-title fw-bold" style="color: #FFD700; text-shadow: 1px 1px 3px #000;">{{ $payment->store->name }}</h5>
                <div class="d-flex justify-content-between border-bottom py-2" style="color: #f8f9fa;">
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Nomor Transaksi</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ $payment->transaction_number }}</h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2" style="color: #f8f9fa;">
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Total Belanja</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ number_format($payment->amount, 0, ',', '.') }}</h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2" style="color: #f8f9fa;">
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Catatan</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ $payment->note }}</h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2" style="color: #f8f9fa;">
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Voucher Digunakan</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ number_format($payment->voucher, 0, ',', '.') }}</h6>
                </div>
                <div class="d-flex justify-content-between py-2" style="color: #f8f9fa;">
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Sisa Yang Harus Dibayar</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ number_format($payment->amount - $payment->voucher, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="card-footer text-muted" style="background: #515170; color: #FFD700;">
                {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
            </div>
        </div>

    </div>
@endsection
