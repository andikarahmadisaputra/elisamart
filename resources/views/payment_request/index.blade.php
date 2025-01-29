@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.payment_request.index.title') }}</h2>
        </div>
        <div class="pull-right">
            @can('payment_request.create')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('payment_request.create') }}"><i class="fa fa-plus"></i> {{ __('transaction.payment_request.button.add') }}</a>
            @endcan
        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert"> 
        {{ $value }}
    </div>
@endsession

@session('error')
    <div class="alert alert-error" role="alert"> 
        {{ $value }}
    </div>
@endsession

<div class="table-responsive">
    <table class="table table-bordered align-middle text-nowrap table-sm">
        <thead class="text-center">
            <tr>
                <th>{{ __('transaction.payment_request.table.no') }}</th>
                <th>{{ __('transaction.payment_request.table.created_at') }}</th>
                <th>{{ __('transaction.payment_request.table.transaction_number') }}</th>
                <th>{{ __('transaction.payment_request.table.bon_number') }}</th>
                <th>{{ __('transaction.payment_request.table.store') }}</th>
                <th>{{ __('transaction.payment_request.table.user') }}</th>
                <th>{{ __('transaction.payment_request.table.amount') }}</th>
                <th>{{ __('transaction.payment_request.table.note') }}</th>
                <th>{{ __('transaction.payment_request.table.status') }}</th>
                <th>{{ __('transaction.payment_request.table.created_by') }}</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        @foreach ($paymentRequests as $paymentRequest)
        <tbody>
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $paymentRequest->created_at }}</td>
                <td>{{ $paymentRequest->transaction_number }}</td>
                <td>{{ $paymentRequest->bon_number }}</td>
                <td>{{ $paymentRequest->store->name }}</td>
                <td>{{ $paymentRequest->user->name }}</td>
                <td>Rp {{ number_format($paymentRequest->amount, 0, ',', '.') }}</td>
                <td>{{ $paymentRequest->note }}</td>
                <td>{{ $paymentRequest->status }}</td>
                <td>{{ $paymentRequest->createdBy->name }}</td>
                <td>
                    @if($paymentRequest->status === 'awaiting payment')
                        <div class="d-inline">
                        @can('payment_request.edit')
                            <a class="btn btn-warning btn-sm" href="{{ route('payment_request.edit',$paymentRequest->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.payment_request.button.edit') }}</a>
                        @endcan
                        @can('payment_request.cancel')
                            <form action="{{ route('payment_request.cancel',$paymentRequest->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.payment_request.button.cancel') }}</button>
                            </form>
                        @endcan
                        @can('payment_request.payment')
                            <button class="btn btn-primary btn-bayar" data-id="{{ $paymentRequest->id }}">Bayar</button>
                        @endcan
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
        @endforeach
    </table>
</div>

{!! $paymentRequests->links() !!}

<!-- Modal Bootstrap -->
<div class="modal fade" id="reviewPaymentModal" tabindex="-1" aria-labelledby="reviewPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewpaymentModalLabel">Detail Tagihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 class="text-muted">Nomor Transaksi</h6>
                    <h6 id="transaction_number" class="fw-bold"></h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 class="text-muted">Bon Number</h6>
                    <h6 id="bon_number" class="fw-bold"></h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 class="text-muted">Amount</h6>
                    <h6 id="amount" class="fw-bold"></h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 class="text-muted">Note</h6>
                    <h6 id="note" class="fw-bold"></h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 class="text-muted">Status</h6>
                    <h6 id="status" class="fw-bold"></h6>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <h6 id="customer" class="text-muted"></h6>
                    <h6 id="balance" class="fw-bold"></h6>
                </div>
                <form id="confirmPaymentForm">
                    @csrf
                    <div id="error-alert" class="alert alert-danger d-none" style="margin-bottom:15px;"></div> <!-- Tempat pesan error -->
                    <div class="mb-3">
                        <label for="voucher" class="form-label">Masukkan Voucher Yang Akan Digunakan</label>
                        <input type="number" id="voucher" name="voucher" class="form-control" min="0" inputmode="numeric">
                    </div>
                    <div class="mb-3">
                        <label for="pin" class="form-label">PIN Transaksi</label>
                        <input type="password" id="pin" name="pin" class="form-control" inputmode="numeric">
                    </div>
                    <input type="hidden" id="payment_request_id" name="payment_request_id">
                    <button type="submit" class="btn btn-success">{{ __('Proses Pembayaran') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.btn-bayar').on('click', function () {
            const id = $(this).data('id');
            $.ajax({
                url: '/admin/payment_request/' + id + '/review',
                type: 'GET',
                success: function (response) {
                    // Isi detail tagihan ke dalam modal
                    $('#transaction_number').text(response.transaction_number);
                    $('#bon_number').text(response.bon_number);
                    $('#amount').text(response.amount);
                    $('#note').text(response.note);
                    $('#status').text(response.status);
                    $('#balance').text(response.balance);
                    $('#customer').text(response.user);
                    $('#payment_request_id').val(response.payment_request_id);
                    // Tampilkan modal
                    $('#reviewPaymentModal').modal('show');
                },
                error: function () {
                    alert('Gagal mengambil data tagihan!');
                }
            });
        });

        // Handle submit form
        $('#confirmPaymentForm').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const errorAlert = $('#error-alert');
            errorAlert.addClass('d-none').html(''); // Reset sebelum mengirim
            $.ajax({
                url: '/admin/payment_request/confirm',
                type: 'POST',
                data: formData,
                success: function (response) {
                    alert(response.message); // Tampilkan pesan sukses
                    window.location.href = response.redirect; // Redirect ke URL yang dikirim server
                },
                error: function (xhr) {
                    if (xhr.status === 422 || xhr.status === 400) {
                        // Tangani error validasi
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = '<ul>';

                        for (const [key, value] of Object.entries(errors)) {
                            errorMessage += `<li>${value[0]}</li>`;
                        }
                        errorMessage += '</ul>';

                        errorAlert.html(errorMessage).removeClass('d-none'); // Tampilkan error
                    } else {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    }
                }
            });
        });
    });
</script>

@endsection