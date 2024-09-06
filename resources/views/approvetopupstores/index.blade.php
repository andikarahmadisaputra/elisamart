@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.approve_topup_store.index.title') }}</h2>
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
        <th>{{ __('transaction.topup_store.table.recipient') }}</th>
        <th>{{ __('transaction.topup_store.table.amount') }}</th>
        <th>{{ __('transaction.topup_store.table.note') }}</th>
        <th>{{ __('transaction.topup_store.table.status') }}</th>
        <th>{{ __('transaction.topup_store.table.created_by') }}</th>
        <th>Action</th>
    </tr>
    @foreach ($topupstores as $topupstore)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $topupstore->transaction_number }}</td>
        <td>{{ $topupstore->created_at }}</td>
        <td>{{ $topupstore->recipient->name }}</td>
        <td>Rp {{ number_format($topupstore->amount, 0, ',', '.') }}</td>
        <td>{{ $topupstore->note }}</td>
        <td>{{ $topupstore->status }}</td>
        <td>{{ $topupstore->createdBy->name }}</td>
        <td>
            @if($topupstore->status === 'pending')
                @can('topupstore-underreview')
                    <form action="{{ route('topupstores.underreview',$topupstore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.under_review') }}</button>
                    </form>
                @endcan
            @endif

            @if($topupstore->status === 'under review')
            @can('topupstore-approve')
                    <form action="{{ route('topupstores.approve',$topupstore->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_store.button.approve') }}</button>
                    </form>
                @endcan
                @can('topupstore-reject')
                    <form action="{{ route('topupstores.reject',$topupstore->id) }}" method="POST">
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

{!! $topupstores->links() !!}
@endsection