@extends('layouts.member')

@section('content')
    <div class="container mt-4">

    <div class="card text-center">
        <div class="card-header">
            Ringkasan belanja
        </div>

        @foreach($payments as $payment)
        <div class="card-body">
            <h5 class="card-title fw-bold">{{ $payment->store->name }}</h5>
            <div class="d-flex justify-content-between">
                <h5 class="card-title text-muted">Nomer Transaksi</h5>
                <h5 class="card-title">{{ $payment->transaction_number }}</h5> 
            </div>
            <div class="d-flex justify-content-between">
                <h5 class="card-title text-muted">Total belanja</h5>
                <h5 class="card-title">{{ number_format($payment->amount, 0, ',', '.') }}</h5> 
            </div>
            <div class="d-flex justify-content-between">
                <h5 class="card-title text-muted">Catatan</h5>
                <h5 class="card-title">{{ $payment->note }}</h5> 
            </div>
            <div class="d-flex justify-content-between">
                <h5 class="card-title text-muted">Voucher Dimiliki</h5>
                <h5 class="card-title">{{ number_format(Auth::user()->balance, 0, ',', '.') }}</h5> 
            </div>
            <a href="{{ route('member.pay_with_voucher', $payment->id) }}" class="btn btn-primary">Gunakan voucher</a>
            <a href="#" class="btn btn-danger btn-sm">Tolak Gunakan Voucher</a>
        </div>
        <div class="card-footer text-muted">
            {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
        </div>

        @endforeach
    </div>

    </div>
@endsection