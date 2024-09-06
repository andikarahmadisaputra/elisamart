@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.topup_user_header.index.title') }}</h2>
        </div>
        <div class="pull-right">
            @can('topup_user.create')
            <a class="btn btn-success btn-sm mb-2" href="{{ route('topup_user.create_by_tag') }}"><i class="fa fa-plus"></i> {{ __('transaction.topup_user_header.button.create_by_tag') }}</a>
            <a class="btn btn-success btn-sm mb-2" href="{{ route('topup_user.create_by_user') }}"><i class="fa fa-plus"></i> {{ __('transaction.topup_user_header.button.create_by_user') }}</a>
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
        <th>{{ __('transaction.topup_user_header.table.no') }}</th>
        <th>{{ __('transaction.topup_user_header.table.transaction_number') }}</th>
        <th>{{ __('transaction.topup_user_header.table.store') }}</th>
        <th>{{ __('transaction.topup_user_header.table.total_amount') }}</th>
        <th>{{ __('transaction.topup_user_header.table.note') }}</th>
        <th>{{ __('transaction.topup_user_header.table.status') }}</th>
        <th>{{ __('transaction.topup_user_header.table.created_by') }}</th>
        <th>{{ __('transaction.topup_user_header.table.action') }}</th>
    </tr>
    @foreach ($topupUserHeaders as $topupUserHeader)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $topupUserHeader->transaction_number }}</td>
        <td>{{ $topupUserHeader->store->name }}</td>
        <td>Rp {{ number_format($topupUserHeader->total_amount, 0, ',', '.') }}</td>
        <td>{{ $topupUserHeader->note }}</td>
        <td>{{ $topupUserHeader->status }}</td>
        <td>{{ $topupUserHeader->createdBy->name }}</td>
        <td>
            <a class="btn btn-info btn-sm" href="{{ route('topup_user.show',$topupUserHeader->id) }}"><i class="fa-solid fa-list"></i> {{ __('transaction.topup_user_header.button.show') }}</a>
            @if($topupUserHeader->status === 'pending') 
                @can('topup_user.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('topup_user.edit',$topupUserHeader->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.topup_user_header.button.edit') }}</a>
                @endcan
                @can('topup_user.cancel')
                    <form action="{{ route('topup_user.cancel',$topupUserHeader->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_user_header.button.cancel') }}</button>
                    </form>
                @endcan
                @can('topup_user.under_review')
                    <form action="{{ route('topup_user.under_review',$topupUserHeader->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_user_header.button.under_review') }}</button>
                    </form>
                @endcan
            @endif

            @if($topupUserHeader->status === 'under review')
                @can('topup_user.approval')
                    <form action="{{ route('topup_user.approve',$topupUserHeader->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_user_header.button.approve') }}</button>
                    </form>
                    <form action="{{ route('topup_user.reject',$topupUserHeader->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_user_header.button.reject') }}</button>
                    </form>
                @endcan
            @endif
        </td>
    </tr>
    @endforeach
</table>

{!! $topupUserHeaders->links() !!}
@endsection