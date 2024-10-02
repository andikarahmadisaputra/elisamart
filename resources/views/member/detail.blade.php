@extends('layouts.member')

@section('content')
    <div class="container mt-4">

    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession

    <div class="card text-center">
            <div class="card-header">
                Ringkasan belanja
            </div>

            <div class="card-body">
                <h5 class="card-title fw-bold">{{ $payment->store->name }}</h5>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title text-muted">Nomer Transaksi</h5>
                    <h5 class="card-title">{{ $payment->transaction_number }}</h5> 
                </div>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title text-muted">Total Belanja</h5>
                    <h5 class="card-title">{{ number_format($payment->amount, 0, ',', '.') }}</h5> 
                </div>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title text-muted">Catatan</h5>
                    <h5 class="card-title">{{ $payment->note }}</h5> 
                </div>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title text-muted">Voucher Digunakan</h5>
                    <h5 class="card-title">{{ number_format(Auth::user()->balance, 0, ',', '.') }}</h5> 
                </div>
                <div class="d-flex justify-content-between">
                    <h5 class="card-title text-muted">Sisa Yang Harus Dibayar</h5>
                    <h5 class="card-title">{{ number_format($payment->amount - $payment->voucher, 0, ',', '.') }}</h5> 
                </div>
            </div>
            <div class="card-footer text-muted">
                {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
            </div>

        </div>

    </div>
@endsection