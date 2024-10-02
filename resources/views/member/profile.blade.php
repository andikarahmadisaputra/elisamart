@extends('layouts.member')

@section('content')

    @session('success')
        <div class="alert alert-success" role="alert"> 
            {{ $value }}
        </div>
    @endsession

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif
    
    <div class="container mt-4">
        <div class="row mb-3">
            <label for="name" class="col-md-4 col-form-label fw-bold">Nama</label>
            <div class="col-md-8">
                <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label for="email" class="col-md-4 col-form-label fw-bold">Email</label>
            <div class="col-md-8">
                <input type="text" class="form-control" id="email" value="{{ Auth::user()->email }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label for="nik" class="col-md-4 col-form-label fw-bold">NIK</label>
            <div class="col-md-8">
                <input type="text" class="form-control" id="nik" value="{{ Auth::user()->nik }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label for="phone" class="col-md-4 col-form-label fw-bold">Telepon</label>
            <div class="col-md-8">
                <input type="text" class="form-control" id="phone" value="{{ Auth::user()->phone }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-4 col-form-label fw-bold">Jenis Kelamin</label>
            <div class="col-md-8">
                <!-- Radio Button Inline -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="pria" {{ Auth::user()->gender === 'pria' ? 'checked' : ''}} disabled>
                    <label class="form-check-label" for="male">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="wanita" {{ Auth::user()->gender === 'wanita' ? 'checked' : ''}} disabled>
                    <label class="form-check-label" for="female">Perempuan</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="#" class="btn btn-primary">Ubah</a>
            <a href="{{ route('member.pin') }}" class="btn btn-primary ms-1">{{ empty(Auth::user()->pin) ? 'Buat PIN' : 'Ganti PIN' }}</a>
            <a href="#" class="btn btn-primary ms-1">Ganti Password</a>
            <a class="btn btn-danger ms-1" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

    </div>
@endsection