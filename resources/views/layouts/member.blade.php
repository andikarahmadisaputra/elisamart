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
      <a href="#" class="bi bi-bell" style="font-size: 24px;"></a>
    </div>
  </nav>

  <!-- Jumbotron saldo -->
  <div class="container mt-4">
    <div class="jumbotron bg-light jumbotron-custom">
      <h4><i class="bi bi-wallet2"></i> Saldo: <span id="saldo">Rp 1.000.000</span> <i class="bi bi-eye-slash" id="toggle-saldo"></i></h4>
      <div class="d-flex justify-content-around mt-4">
        <button class="btn btn-light btn-icon">
          <i class="bi bi-arrow-left-right"></i>
          <span>Transfer</span>
        </button>
        <button class="btn btn-light btn-icon">
          <i class="bi bi-credit-card"></i>
          <span>Bayar</span>
        </button>
        <button class="btn btn-light btn-icon">
          <i class="bi bi-clock-history"></i>
          <span>History</span>
        </button>
      </div>
    </div>
  </div>

  <main class="py-4">
      @yield('content')
  </main>

  <!-- Navbar bawah -->
  <nav class="navbar bottom-nav navbar-light bg-light">
    <div class="container d-flex justify-content-around">
      <a class="nav-link" href="#"><i class="bi bi-house"></i><br>Home</a>
      <a class="nav-link" href="#"><i class="bi bi-tag"></i><br>Promo</a>
      <a class="nav-link" href="#"><i class="bi bi-person"></i><br>Profile</a>
    </div>
  </nav>

  <!-- Script untuk fitur hide/unhide saldo -->
  <script>
    document.getElementById('toggle-saldo').addEventListener('click', function() {
      var saldo = document.getElementById('saldo');
      var icon = this;

      if (saldo.style.display === 'none') {
        saldo.style.display = 'inline';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        saldo.style.display = 'none';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  </script>
</body>
</html>
