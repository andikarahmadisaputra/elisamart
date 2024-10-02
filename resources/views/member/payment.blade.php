@extends('layouts.member')

@section('content')
<div class="container mt-4 mb-5" style="padding-bottom: 80px;">
    @foreach($payments as $payment)
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

                <!-- Tombol aksi dengan gaya premium dan bayangan -->
                <div class="d-flex justify-content-center mt-4">
                    <a href="{{ route('member.pay_with_voucher', $payment->id) }}" class="btn btn-primary mx-2" 
                        style="background-color: #FFD700; color: #2f2f44; border: none; border-radius: 25px; padding: 10px 20px; box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);">
                        Gunakan Voucher
                    </a>
                    <a href="#" class="btn btn-danger btn-sm mx-2" 
                        style="border-radius: 25px; padding: 8px 20px; color: #fff; background: #e74c3c; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);">
                        Tolak Gunakan Voucher
                    </a>
                </div>
            </div>

            <!-- Footer dengan desain yang lebih halus dan kontras -->
            <div class="card-footer text-muted" style="border-radius: 0 0 15px 15px; background: #515170; color: #FFD700;">
                {{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}
            </div>
        </div>
    @endforeach
</div>
@endsection
