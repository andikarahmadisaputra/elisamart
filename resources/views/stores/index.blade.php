@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('master.store.index.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-success mb-2" href="{{ route('stores.create') }}"><i class="fa fa-plus"></i> {{ __('master.store.button.add') }}</a>
        </div>
    </div>
</div>

@session('success')
    <div class="alert alert-success" role="alert"> 
        {{ $value }}
    </div>
@endsession

<div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
        <thead>
            <tr>
                <th>{{ __('master.store.table.no') }}</th>
                <th>{{ __('master.store.table.name') }}</th>
                <th>{{ __('master.store.table.detail') }}</th>
                <th>{{ __('master.store.table.balance') }}</th>
                <th width="280px">{{ __('master.store.table.action') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $key => $store)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $store->name }}</td>
                <td>{{ $store->detail }}</td>
                <td>{{ sprintf("%.2f", $store->balance) }}</td>
                <td>
                    <a class="btn btn-info btn-sm" href="{{ route('stores.show',$store->id) }}"><i class="fa-solid fa-list"></i> {{ __('master.store.button.show') }}</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('stores.edit',$store->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('master.store.button.edit') }}</a>
                    <form method="POST" action="{{ route('stores.destroy', $store->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> {{ __('master.store.button.delete') }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{!! $data->links('pagination::bootstrap-5') !!}
@endsection