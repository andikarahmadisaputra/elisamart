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

<table class="table table-bordered">
    <tr>
        <th>{{ __('transaction.payment_request.table.no') }}</th>
        <th>{{ __('transaction.payment_request.table.created_at') }}</th>
        <th>{{ __('transaction.payment_request.table.transaction_number') }}</th>
        <th>{{ __('transaction.payment_request.table.store') }}</th>
        <th>{{ __('transaction.payment_request.table.user') }}</th>
        <th>{{ __('transaction.payment_request.table.amount') }}</th>
        <th>{{ __('transaction.payment_request.table.note') }}</th>
        <th>{{ __('transaction.payment_request.table.status') }}</th>
        <th>{{ __('transaction.payment_request.table.created_by') }}</th>
        <th width="280px">Action</th>
    </tr>
    @foreach ($paymentRequests as $paymentRequest)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $paymentRequest->created_at }}</td>
        <td>{{ $paymentRequest->transaction_number }}</td>
        <td>{{ $paymentRequest->store->name }}</td>
        <td>{{ $paymentRequest->user->name }}</td>
        <td>Rp {{ number_format($paymentRequest->amount, 0, ',', '.') }}</td>
        <td>{{ $paymentRequest->note }}</td>
        <td>{{ $paymentRequest->status }}</td>
        <td>{{ $paymentRequest->createdBy->name }}</td>
        <td>
            @if($paymentRequest->status === 'pending')
                @can('payment_request.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('payment_request.edit',$paymentRequest->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.payment_request.button.edit') }}</a>
                @endcan
                @can('payment_request.cancel')
                    <form action="{{ route('payment_request.cancel',$paymentRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.payment_request.button.cancel') }}</button>
                    </form>
                @endcan
            @endif
        </td>
    </tr>
    @endforeach
</table>

{!! $paymentRequests->links() !!}
@endsection