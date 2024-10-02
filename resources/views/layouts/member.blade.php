<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Elisamart') }}</title>

  <!-- Scripts -->
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  <style>
    /* Palet Warna Elegan */
    :root {
      --primary-color: #1D1B31; /* Midnight Blue */
      --secondary-color: #D4AF37; /* Golden Yellow */
      --accent-color: #FFD700; /* Gold */
      --bg-start: #262335; /* Warna gradien awal */
      --bg-end: #373250; /* Warna gradien akhir */
      --neutral-color: #F0EAD6; /* Ivory */
      --text-primary: #F0EAD6; /* Ivory untuk teks */
      --button-hover: #FFD700; /* Warna hover untuk tombol */
    }

    @media (min-width: 992px) {
        body {
            padding-bottom: 120px; /* Berikan ruang lebih besar pada layar desktop */
        }
    }

    /* Body dan Background */
    body {
      background: linear-gradient(135deg, var(--bg-start), var(--bg-end)); /* Gradien background */
      color: var(--text-primary);
      font-family: 'Arial', sans-serif;
      padding-bottom: 100px; /* Memberikan ruang ekstra pada body agar konten tidak tertutup */
    }

    .navbar-custom {
      justify-content: space-between;
      background-color: var(--primary-color);
      border-bottom: 2px solid var(--accent-color);
      box-shadow: 0 4px 8px rgba(29, 27, 49, 0.3);
    }

    .jumbotron-custom {
      padding: 40px;
      text-align: center;
      background: linear-gradient(135deg, var(--neutral-color), var(--secondary-color));
      border: 2px solid var(--accent-color);
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3); /* Efek bayangan emas */
    }

    .card {
      background: var(--neutral-color);
      border: 2px solid var(--primary-color);
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(29, 27, 49, 0.2);
      padding: 30px;
      margin: 20px 0;
    }

    .bottom-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: var(--primary-color);
      border-top: 2px solid var(--accent-color);
    }

    .bottom-nav .nav-item {
      flex-grow: 1;
      text-align: center;
    }

    .bottom-nav .nav-link {
      text-align: center;
      padding: 10px 0;
      color: var(--text-primary);
      font-weight: 600;
    }

    .bottom-nav .nav-link i {
      font-size: 1.4rem;
      color: var(--accent-color);
    }

    /* Tombol Unik */
    .btn-custom {
      background: var(--primary-color);
      color: var(--neutral-color);
      border: 2px solid var(--accent-color);
      border-radius: 30px; /* Border bulat untuk tombol */
      padding: 15px 40px;
      margin-top: 20px;
      box-shadow: 0 8px 20px rgba(29, 27, 49, 0.4); /* Bayangan 3D */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-custom:hover {
      background: var(--accent-color);
      transform: translateY(-7px); /* Efek naik saat di-hover */
      color: var(--primary-color);
      box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5); /* Bayangan lebih besar saat hover */
    }

    /* Tombol Ikon Unik */
    .btn-icon {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      border-radius: 20px; /* Tombol berbentuk oval */
      padding: 10px 20px;
      background: var(--neutral-color);
      border: 2px solid var(--primary-color);
      box-shadow: 0 6px 18px rgba(212, 175, 55, 0.3);
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-icon:hover {
      background: var(--button-hover);
      box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    }
  </style>
</head>
<body>

  <!-- Navbar atas -->
  <nav class="navbar navbar-light navbar-custom">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1 text-white">{{ config('app.name', 'Elisamart') }}</span>
      <a href="{{ route('member.index') }}" class="bi bi-bell text-white" style="font-size: 24px;"></a>
    </div>
  </nav>

  <main class="py-4">
      @yield('content')
  </main>

  <!-- Navbar bawah -->
  <nav class="navbar bottom-nav">
    <div class="container d-flex justify-content-around">
      <a class="nav-link" href="{{ route('member.index') }}"><i class="bi bi-house"></i><br>Home</a>
      <a class="nav-link" href="#"><i class="bi bi-tag"></i><br>Promo</a>
      <a class="nav-link" href="{{ route('member.profile') }}"><i class="bi bi-person"></i><br>Profile</a>
    </div>
  </nav>
</body>
</html>
