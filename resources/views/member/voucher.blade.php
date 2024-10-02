@extends('layouts.member')

@section('content')
    <div class="container mt-4">

        @session('error')
            <div class="alert alert-danger" role="alert"> 
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
            </div>
            <div class="card-footer text-muted">
                {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
            </div>

        </div>

        <form class="mt-3" method="POST" action="{{ route('member.pay', $payment->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label for="pin" class="col-md-4 col-form-label fw-bold">Voucher akan digunakan ( max : {{ number_format(Auth::user()->balance, 0, ',', '.') }} )</label>
                <div class="col-md-8">
                    <input type="text" name="voucher" class="form-control" id="voucher">
                </div>
            </div>
            <div class="row mb-3">
                <label for="pin" class="col-md-4 col-form-label fw-bold">PIN</label>
                <div class="col-md-8">
                    <input type="password" name="pin" class="form-control" id="pin">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Proses</button>
            </div>
        </form>

    </div>

@endsection