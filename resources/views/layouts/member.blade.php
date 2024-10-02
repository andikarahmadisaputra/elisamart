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
    .navbar-custom {
      justify-content: space-between;
    }

    .jumbotron-custom {
      padding: 20px;
      text-align: center;
    }

    .btn-icon {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .bottom-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: #f8f9fa;
      border-top: 1px solid #e0e0e0;
    }

    .bottom-nav .nav-item {
      flex-grow: 1;
      text-align: center;
    }

    .bottom-nav .nav-link {
      text-align: center;
      padding: 10px 0;
      color: #000;
    }

    .bottom-nav .nav-link i {
      font-size: 1.2rem;
    }

    .bottom-nav .nav-link span {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

  <!-- Navbar atas -->
  <nav class="navbar navbar-light bg-light navbar-custom">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">{{ config('app.name', 'Elisamart') }}</span>
      <a href="{{ route('member.index') }}" class="bi bi-bell" style="font-size: 24px;"></a>
    </div>
  </nav>

  <main class="py-4">
      @yield('content')
  </main>

  <!-- Navbar bawah -->
  <nav class="navbar bottom-nav navbar-light bg-light">
    <div class="container d-flex justify-content-around">
      <a class="nav-link" href="{{ route('member.index') }}"><i class="bi bi-house"></i><br>Home</a>
      <a class="nav-link" href="#"><i class="bi bi-tag"></i><br>Promo</a>
      <a class="nav-link" href="{{ route('member.profile') }}"><i class="bi bi-person"></i><br>Profile</a>
    </div>
  </nav>

</body>
</html>
