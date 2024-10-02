@extends('layouts.member')

@section('content')
<div class="container mt-4 mb-5"> <!-- Tambahkan padding bottom -->
    <div class="profile-card mx-auto p-4">
        <!-- Section Judul -->
        <div class="text-center mb-4">
            <h2 class="section-title">Profil Pengguna</h2>
            <p class="text-muted">Informasi pengguna terdaftar</p>
        </div>

        <!-- Pesan Success -->
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Informasi Profil -->
        <div class="card-body">
            <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label fw-bold text-light">Nama</label>
                <div class="col-md-8">
                    <input type="text" class="form-control profile-input" id="name" value="{{ Auth::user()->name }}" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <label for="email" class="col-md-4 col-form-label fw-bold text-light">Email</label>
                <div class="col-md-8">
                    <input type="text" class="form-control profile-input" id="email" value="{{ Auth::user()->email }}" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <label for="nik" class="col-md-4 col-form-label fw-bold text-light">NIK</label>
                <div class="col-md-8">
                    <input type="text" class="form-control profile-input" id="nik" value="{{ Auth::user()->nik }}" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <label for="phone" class="col-md-4 col-form-label fw-bold text-light">Telepon</label>
                <div class="col-md-8">
                    <input type="text" class="form-control profile-input" id="phone" value="{{ Auth::user()->phone }}" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-4 col-form-label fw-bold text-light">Jenis Kelamin</label>
                <div class="col-md-8">
                    <!-- Radio Button Inline -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="pria" {{ Auth::user()->gender === 'pria' ? 'checked' : ''}} disabled>
                        <label class="form-check-label text-light" for="male">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="wanita" {{ Auth::user()->gender === 'wanita' ? 'checked' : ''}} disabled>
                        <label class="form-check-label text-light" for="female">Perempuan</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-end flex-wrap">
            <a href="#" class="btn btn-custom">Ubah</a>
            <a href="{{ route('member.pin') }}" class="btn btn-custom ms-1">{{ empty(Auth::user()->pin) ? 'Buat PIN' : 'Ganti PIN' }}</a>
            <a href="#" class="btn btn-custom ms-1">Ganti Password</a>
            <a class="btn btn-danger ms-1" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #262335, #373250);
        padding-bottom: 100px; /* Memberikan ruang ekstra pada body agar konten tidak tertutup */
    }

    .profile-card {
        background: rgba(40, 44, 52, 0.8);
        border: 1px solid #FFD700;
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        padding: 40px;
        max-width: 700px;
        color: #F0EAD6;
    }

    .section-title {
        color: #FFD700;
        font-weight: 700;
    }

    /* Styling khusus untuk input di dalam form profil */
    .profile-input {
        background: rgba(255, 255, 255, 0.1); /* Warna background semi-transparan */
        border: 1px solid #FFD700; /* Border berwarna emas */
        color: #F0EAD6; /* Warna teks cerah */
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2); /* Box-shadow halus */
    }

    /* Styling input saat disabled */
    .profile-input:disabled {
        background: rgba(255, 255, 255, 0.05); /* Ubah background lebih gelap */
        color: #e0e0e0; /* Warna teks untuk input yang di-disable */
        border: 1px solid #9e9e9e; /* Border abu-abu */
        opacity: 0.8;
    }

    /* Styling Tombol */
    .btn-custom {
        background: #FFD700; /* Emas */
        color: #373250; /* Warna teks kontras */
        border: 2px solid #FFD700;
        border-radius: 30px;
        padding: 8px 20px;
        font-size: 14px;
        box-shadow: 0 8px 20px rgba(29, 27, 49, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-custom:hover {
        background: #fff; /* Background putih saat hover */
        color: #FFD700; /* Teks emas */
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 215, 0, 0.5);
    }

    .alert-success {
        background-color: rgba(72, 201, 176, 0.8);
        color: white;
        border-radius: 10px;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        .profile-card {
            padding: 20px;
        }

        .btn-custom {
            padding: 6px 15px;
            font-size: 12px;
        }

        .d-flex.flex-wrap .btn-custom {
            width: 100%;
            margin-top: 5px;
        }

        .d-flex.flex-wrap {
            justify-content: center;
        }

        .container.mb-5 {
            padding-bottom: 80px;
        }
    }
</style>

@endsection
