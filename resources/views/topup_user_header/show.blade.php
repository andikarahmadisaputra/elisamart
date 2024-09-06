@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.topup_user_header.index.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('topup_user.index') }}"><i class="fa fa-arrow-left"></i> {{ __('transaction.topup_user.button.back') }}</a>
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
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $topupUserHeader->transaction_number }}</td>
        <td>{{ $topupUserHeader->store->name }}</td>
        <td>Rp {{ number_format($topupUserHeader->total_amount, 0, ',', '.') }}</td>
        <td>{{ $topupUserHeader->note }}</td>
        <td>{{ $topupUserHeader->status }}</td>
        <td>{{ $topupUserHeader->createdBy->name }}</td>
        <td>
            @if($topupUserHeader->status === 'pending') 
                @can('topup_user.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('topup_user.edit',$topupUserHeader->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.topup_user_header.button.edit') }}</a>
                @endcan
            @endif
        </td>
    </tr>
</table>

<table class="table table-bordered">
    <tr>
        <th>{{ __('transaction.topup_user_header.table.no') }}</th>
        <th>{{ __('transaction.topup_user_header.table.nik') }}</th>
        <th>{{ __('transaction.topup_user_header.table.user') }}</th>
        <th>{{ __('transaction.topup_user_header.table.tag') }}</th>
        <th>{{ __('transaction.topup_user_header.table.amount') }}</th>
        <th width="280px">{{ __('transaction.topup_user_header.table.action') }}</th>
    </tr>
    @foreach ($topupUserDetails as $topupUserDetail)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $topupUserDetail->user->nik }}</td>
        <td>{{ $topupUserDetail->user->name }}</td>
        <td>
            @if(!empty($topupUserDetail->user->tags()))
                @foreach($topupUserDetail->user->tags as $v)
                <label class="badge bg-success">{{ $v->name }}</label>
                @endforeach
            @endif
        </td>
        <td>Rp {{ number_format($topupUserDetail->amount, 0, ',', '.') }}</td>
        <td>
            @if($topupUserHeader->status === 'pending') 
                @can('topup_user.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('topup_user.edit',$topupUserDetail->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('transaction.topup_user_header.button.edit') }}</a>
                @endcan
                @can('topup_user.cancel')
                    <form action="{{ route('topup_user.cancel',$topupUserDetail->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('transaction.topup_user_header.button.cancel') }}</button>
                    </form>
                @endcan
            @endif
        </td>
    </tr>
    @endforeach
</table>

@endsection