@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{ __('transaction.payment_request.create.title') }}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('payment_request.index') }}"><i class="fa fa-arrow-left"></i> {{ __('transaction.payment_request.button.cancel') }}</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('payment_request.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.payment_request.form.store') }}:</strong>
                <select class="form-select @error('store_id') is-invalid @enderror" name="store_id">
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.payment_request.form.user') }}:</strong>
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="selectedUser" name="user" class="form-control col-md-8 @error('user') is-invalid @enderror" value="{{ old('user') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary ms-2 form-control col-md-4" id="btnChooseUser">Pilih User</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.payment_request.form.bon_number') }}:</strong>
                <input type="text" name="bon_number" class="form-control @error('bon_number') is-invalid @enderror" placeholder="Bon Number" value="{{ old('bon_number') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.payment_request.form.amount') }}:</strong>
                <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Amount" value="{{ old('amount') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ __('transaction.payment_request.form.note') }}:</strong>
                <input type="text" name="note" class="form-control" placeholder="Note" value="{{ old('note') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mb-3 mt-2"><i class="fa-solid fa-floppy-disk"></i> {{ __('transaction.payment_request.button.submit') }}</button>
        </div>
    </div>
</form>

<!-- Modal Bootstrap -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Pilih User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Kolom Pencarian -->
                <input type="text" id="searchUser" class="form-control mb-3" placeholder="Cari nama, username, atau email">

                <!-- Tabel Daftar User -->
                <div class="table-responseive">
                    <table class="table table-bordered align-middle text-nowrap table-sm">
                        <thead class="text-center">
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#btnChooseUser').on('click', function () {
            $('#userModal').modal('show');
            fetchUsers('');
        });

        function fetchUsers(query) {
            $.ajax({
                url: '/api/users',
                method: 'GET',
                data: { query: query },
                success: function (data) {
                    let rows = '';
                    if (data.length > 0) {
                        data.forEach(function (user) {
                            rows += `
                                <tr>
                                    <td>${user.name}</td>
                                    <td>${user.username}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btnSelectUser" data-name="${user.name}" data-email="${user.email}">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        rows = '<tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>';
                    }
                    $('#userTableBody').html(rows);
                },
                error: function () {
                    alert('Gagal memuat data user.');
                }
            });
        }

        let debounceTimeout;

        $('#searchUser').on('keyup', function () {
            const query = $(this).val();

            if (query.length > 2) {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    fetchUsers(query);
                }, 300);
            }
        });

        $(document).on('click', '.btnSelectUser', function () {
            const email = $(this).data('email');
            $('#selectedUser').val(email); 
            $('#userModal').modal('hide');
        });
    });
</script>
@endsection