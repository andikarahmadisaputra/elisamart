@extends('layouts.member')

@section('content')
  <!-- Jumbotron saldo -->
  <div class="container mt-4">
    <div class="jumbotron bg-light jumbotron-custom">
      <h4><i class="bi bi-wallet2"></i> Saldo: Rp <span id="saldo">{{ number_format(Auth::user()->balance, 0, ',', '.') }}</span> <i class="bi bi-eye-slash" id="toggle-saldo"></i></h4>
      <div class="d-flex justify-content-around mt-4">
        <a class="btn btn-light btn-icon">
          <i class="bi bi-arrow-left-right"></i>
          <span>Transfer</span>
        </a>
        <a class="btn btn-light btn-icon" href="{{ route('member.payment') }}">
          <i class="bi bi-credit-card"></i>
          <span>Bayar</span>
        </a>
        <a class="btn btn-light btn-icon">
          <i class="bi bi-clock-history"></i>
          <span>History</span>
        </a>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-center">
    Halaman member
  </div>

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
@endsection