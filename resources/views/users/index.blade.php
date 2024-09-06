@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('master.user.index.title') }}</h2>
        </div>
        @can('user.create')
        <div class="pull-right">
            <a class="btn btn-success mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> {{ __('master.user.button.add') }}</a>
        </div>
        @endcan
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
                <th>{{ __('master.user.table.no') }}</th>
                <th>{{ __('master.user.table.name') }}</th>
                <th>{{ __('master.user.table.email') }}</th>
                <th>{{ __('master.user.table.role') }}</th>
                <th>{{ __('master.user.table.tag') }}</th>
                <th>{{ __('master.user.table.nik') }}</th>
                <th>{{ __('master.user.table.phone') }}</th>
                <th>{{ __('master.user.table.balance') }}</th>
                <th width="280px">{{ __('master.user.table.action') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                @if(!empty($user->getRoleNames()))
                    @foreach($user->getRoleNames() as $v)
                    <label class="badge bg-success">{{ $v }}</label>
                    @endforeach
                @endif
                </td>
                <td>
                @if(!empty($user->tags()))
                    @foreach($user->tags as $v)
                    <label class="badge bg-success">{{ $v->name }}</label>
                    @endforeach
                @endif
                </td>
                <td>
                @if(!empty($user->nik))
                    {{ $user->nik }}
                @endif
                </td>
                <td>
                @if(!empty($user->nik))
                    {{ $user->phone }}
                @endif
                </td>
                <td>Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                <td>
                    @can('user.show')
                    <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}"><i class="fa-solid fa-list"></i> {{ __('master.user.button.show') }}</a>
                    @endcan
                    @can('user.edit')
                    <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa-solid fa-pen-to-square"></i> {{ __('master.user.button.edit') }}</a>
                    @endcan
                    @can('user.delete')
                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> {{ __('master.user.button.delete') }}</button>
                    </form>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{!! $data->links('pagination::bootstrap-5') !!}
@endsection