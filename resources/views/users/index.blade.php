@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="d-flex justify-content-start">
        <h2>{{ __('master.user.index.title') }}</h2>
    </div>
    @can('user.create')
    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-primary me-2" href="{{ route('users.create') }}">
            <i class="bi-plus"></i> {{ __('master.user.button.add') }}
        </a>
    </div>
    @endcan
    @can('user.import')
    <div class="d-flex justify-content-end mb-2">
        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="d-flex">
            @csrf
            <div class="input-group">
                <input type="file" name="file" class="form-control">
                <button class="btn btn-success"><i class="bi-upload"></i> {{ __('master.user.button.import') }}</button>
            </div>
        </form>
    </div>
    @endcan
    @can('user.export')
    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-warning me-2" href="{{ route('users.export') }}">
            <i class="bi-download"></i> {{ __('master.user.button.export') }}
        </a>
    </div>
    @endcan
</div>

@session('success')
    <div class="alert alert-success" role="alert">
        {{ $value }}
    </div>
@endsession

@session('failure')
    <div class="alert alert-danger" role="alert">
        <strong>{{ __('Whoops! Something went wrong.') }}</strong>
        <ul>
            @foreach ($failures as $failure)
                <li>{{ $failure->row() }}</li>
                <li>{{ $failure->attribute() }}</li>
                <li>{{ $failure->errors() }}</li>
                <li>{{ $failure->values() }}</li>
            @endforeach
        </ul>
    </div>
@endsession

{{-- Display Error Messages --}}
@if ($errors->any())
    <div class="alert alert-danger">
      <strong>{{ __('Whoops! Something went wrong.') }}</strong>
      <ul>
        @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
@endif

<div class="row table-responsive">
    <table class="table table-striped table-hover table-bordered table-sm align-middle text-nowrap">
        <thead class="text-center table-light">
            <tr>
                <th>{{ __('master.user.table.no') }}</th>
                <th>{{ __('master.user.table.name') }}</th>
                <th>{{ __('master.user.table.email') }}</th>
                <th>{{ __('master.user.table.role') }}</th>
                <th>{{ __('master.user.table.tag') }}</th>
                <th>{{ __('master.user.table.username') }}</th>
                <th>{{ __('master.user.table.nik') }}</th>
                <th>{{ __('master.user.table.gender') }}</th>
                <th>{{ __('master.user.table.phone') }}</th>
                <th>{{ __('master.user.table.balance') }}</th>
                <th>{{ __('master.user.table.created_at') }}</th>
                <th>{{ __('master.user.table.updated_at') }}</th>
                <th>{{ __('master.user.table.deleted_at') }}</th>
                <th width="280px">{{ __('master.user.table.action') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $key => $user)
            <tr>
                <th class="text-center">{{ ++$i }}</th>
                <td>
                    <div class="text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                </td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <label class="badge bg-success text-truncate" title="{{ $v }}">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    @if($user->tags->isNotEmpty())
                        @foreach($user->tags as $v)
                            <label class="badge bg-success text-truncate" title="{{ $v->name }}">{{ $v->name }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->username }}">{{ $user->username }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->nik }}">{{ $user->nik }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->gender }}">{{ $user->gender }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->phone }}">{{ $user->phone }}</div>
                </td>
                <td>Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                <td>
                    <div class="text-truncate" title="{{ $user->created_at }}">{{ $user->created_at }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->updated_at }}">{{ $user->updated_at }}</div>
                </td>
                <td>
                    <div class="text-truncate" title="{{ $user->deleted_at }}">{{ $user->deleted_at }}</div>
                </td>
                <td>
                    @can('user.show')
                    <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}">
                        <i class="bi-eye"></i> {{ __('master.user.button.show') }}
                    </a>
                    @endcan
                    @can('user.edit')
                    <a class="btn btn-warning btn-sm" href="{{ route('users.edit',$user->id) }}">
                        <i class="bi-pencil-square"></i> {{ __('master.user.button.edit') }}
                    </a>
                    @if ($user->trashed())
                        @can('user.restore')
                        <form method="POST" action="{{ route('users.restore', $user->id) }}" style="display:inline" id="restore-form-{{ $user->id }}">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmRestore({{ $user->id }})">
                                <i class="bi-arrow-counterclockwise"></i> {{ __('master.user.button.restore') }}
                            </button>
                        </form>
                        @endcan
                    @else
                        @can('user.delete')
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline" id="delete-form-{{ $user->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">
                                <i class="bi-trash"></i> {{ __('master.user.button.delete') }}
                            </button>
                        </form>
                        @endcan
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{!! $data->links('pagination::bootstrap-5') !!}

<script>
    function confirmDelete(userId) {
        // Tampilkan konfirmasi sebelum menghapus
        if (confirm("Yakin akan hapus?")) {
            // Jika ya, kirim form
            document.getElementById('delete-form-' + userId).submit();
        }
    }

    function confirmRestore(userId) {
        // Tampilkan konfirmasi sebelum merestore
        if (confirm("Yakin akan dipulihkan?")) {
            // Jika ya, kirim form
            document.getElementById('restore-form-' + userId).submit();
        }
    }
</script>

@endsection
