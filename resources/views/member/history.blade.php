@extends('layouts.member')

@section('content')
<div class="container mt-4 mb-5">
    <div class="table-responsive shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <table class="table table-bordered table-hover table-striped align-middle bg-white">
            <thead class="table-dark">
                <tr class="text-center">
                    <th style="width: 25%;">Tanggal</th>
                    <th style="width: 50%;">Transaksi</th>
                    <th style="width: 25%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr class="text-center">
                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                        <td class="fw-bold">{{ $transaction->transaction_name }}</td>
                        <td class="text-success fw-bold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
