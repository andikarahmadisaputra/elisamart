@extends('layouts.member')

@section('content')
    <div class="container mt-4 mb-5" style="padding-bottom: 80px;">
        
        @if(session('error'))
            <div class="alert alert-danger" role="alert"> 
                {{ session('error') }}
            </div>
        @endif

        <form class="mt-3" method="POST" action="{{ route('member.store_transfer') }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label for="recipient" class="col-md-4 col-form-label fw-bold">Penerima ( email / nik / telepon )</label>
                <div class="col-md-8">
                    <input type="text" name="recipient" class="form-control" id="recipient" placeholder="Masukkan penerima" value="{{ old('recipient') }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="amount" class="col-md-4 col-form-label fw-bold">Jumlah (max: {{ number_format(Auth::user()->balance, 0, ',', '.') }})</label>
                <div class="col-md-8">
                    <input type="text" name="amount" class="form-control" id="amount" placeholder="Masukkan jumlah" value="{{ old('amount') }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="note" class="col-md-4 col-form-label fw-bold">Catatan</label>
                <div class="col-md-8">
                    <input type="text" name="note" class="form-control" id="note" placeholder="Masukkan catatan" value="{{ old('note') }}">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" style="background-color: #FFD700; color: #2f2f44; border: none; border-radius: 25px; padding: 10px 20px;">
                    Proses
                </button>
            </div>
        </form>

    </div>
@endsection
