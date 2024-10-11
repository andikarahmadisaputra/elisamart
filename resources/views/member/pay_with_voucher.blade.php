@extends('layouts.member')

@section('content')
    <div class="container mt-4 mb-5" style="padding-bottom: 80px;">
        
        @if(session('error'))
            <div class="alert alert-danger" role="alert"> 
                {{ session('error') }}
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
                    <h6 class="text-muted" style="color: #ccc; text-shadow: 1px 1px 1px #000;">Voucher Dimiliki</h6>
                    <h6 class="fw-bold" style="text-shadow: 1px 1px 2px #000;">{{ number_format(Auth::user()->balance, 0, ',', '.') }}</h6>
                </div>
            </div>
            <div class="card-footer text-muted" style="border-radius: 0 0 15px 15px; background: #515170; color: #FFD700;">
                {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
            </div>
        </div>

        <form class="mt-3" method="POST" action="{{ route('member.confirm_pay_with_voucher', $payment->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label for="voucher" class="col-md-4 col-form-label fw-bold">Voucher akan digunakan (max: {{ number_format(Auth::user()->balance, 0, ',', '.') }})</label>
                <div class="col-md-8">
                    <input type="text" name="voucher" class="form-control" id="voucher" placeholder="Masukkan jumlah yang akan dipakai" value="{{ old('voucher') }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="pin" class="col-md-4 col-form-label fw-bold">PIN</label>
                <div class="col-md-8">
                    <input type="password" name="pin" class="form-control" id="pin" placeholder="Masukkan PIN" required>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" style="background-color: #FFD700; color: #2f2f44; border: none; border-radius: 25px; padding: 10px 20px;">
                    Proses
                </button>
            </div>
        </form>

    </div>
@endsection
