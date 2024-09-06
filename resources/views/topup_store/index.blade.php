@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.topup_store.index.title') }}</h2>
        </div>
        <div class="pull-right">
            @can('topup_store.create')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('topup_store.create') }}"><i class="fa fa-plus"></i> {{ __('transaction.topup_store.button.add') }}</a>
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
        <th>No</th>
        <th>{{ __('transaction.topup_store.table.transaction_number') }}</th>
        <th>{{ __('transaction.topup_store.table.created_at') }}</th>
        <th>{{ __('transaction.topup_store.table.store') }}</th>
        <th>{{ __('transaction.topup_store.table.amount') }}</th>
        <th>{{ __('transaction.topup_store.table.note') }}</th>
        <th>{{ __('transaction.topup_store.table.status') }}</th>
        <th>{{ __('transaction.topup_store.table.created_by') }}</th>
        <th width="280px">Action</th>
    </tr>
    @foreach ($topupStores as $topupStore)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $topupStore->transaction_number }}</td>
        <td>{{ $topupStore->created_at }}</td>
        <td>{{ $topupStore->store->name }}</td>
        <td>Rp {{ number_format($topupStore->amount, 0, ',', '.') }}</td>
        <td>{{ $topupStore->note }}</td>
        <td>{{ $topupStore->status }}</td>
        <td>{{ $topupStore->createdBy->name }}</td>
        <td>
            @if($topupStore->status === 'pending')
                @can('topup_store.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('topup_store.edit',$topupStore->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.topup_store.button.edit') }}</a>
                @endcan
                @can('topup_store.cancel')
                    <form action="{{ route('topup_store.cancel',$topupStore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.cancel') }}</button>
                    </form>
                @endcan
                @can('topup_store.approval')
                    <form action="{{ route('topup_store.under_review',$topupStore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.under_review') }}</button>
                    </form>
                @endcan
            @endif

            @if($topupStore->status === 'under review')
                @can('topup_store.approval')
                    <form action="{{ route('topup_store.approve',$topupStore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.approve') }}</button>
                    </form>
                    <form action="{{ route('topup_store.reject',$topupStore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.reject') }}</button>
                    </form>
                @endcan
            @endif
        </td>
    </tr>
    @endforeach
</table>

{!! $topupStores->links() !!}
@endsection