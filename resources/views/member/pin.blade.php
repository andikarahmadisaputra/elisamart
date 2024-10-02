@extends('layouts.member')

@section('content')
    <div class="container mt-4">

        @session('error')
            <div class="alert alert-danger" role="alert"> 
                {{ $value }}
            </div>
        @endsession

        @if(empty(Auth::user()->pin))

            <form method="POST" action="{{ route('member.update_pin') }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <label for="pin" class="col-md-4 col-form-label fw-bold">PIN Baru</label>
                    <div class="col-md-8">
                        <input type="password" name="pin" class="form-control" id="pin">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="confirm-pin" class="col-md-4 col-form-label fw-bold">Konfirmasi PIN Baru</label>
                    <div class="col-md-8">
                        <input type="password" name="confirm_pin" class="form-control" id="confirm_pin">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        @else

            <form method="POST" action="{{ route('member.update_pin') }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <label for="old-pin" class="col-md-4 col-form-label fw-bold">PIN Lama</label>
                    <div class="col-md-8">
                        <input type="password" name="old_pin" class="form-control" id="old_pin">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="pin" class="col-md-4 col-form-label fw-bold">PIN Baru</label>
                    <div class="col-md-8">
                        <input type="password" name="pin" class="form-control" id="pin">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="confirm_pin" class="col-md-4 col-form-label fw-bold">Konfirmasi PIN Baru</label>
                    <div class="col-md-8">
                        <input type="password" name="confirm_pin" class="form-control" id="confirm_pin">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        @endif

    </div>
@endsection