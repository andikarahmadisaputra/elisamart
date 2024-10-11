@extends('layouts.member')

@section('content')
  <!-- Jumbotron saldo -->
  <div class="container mt-4 mb-5" style="padding-bottom: 80px;">
    <div class="jumbotron jumbotron-custom text-center" style="border-radius: 15px; background: linear-gradient(135deg, #4e7bff, #d9d9d9); color: #ffffff; padding: 20px;">
      <h4 class="fw-bold"><i class="bi bi-wallet2"></i> Saldo: Rp <span id="saldo" style="text-shadow: 1px 1px 2px #000;">{{ number_format(Auth::user()->balance, 0, ',', '.') }}</span> <i class="bi bi-eye-slash" id="toggle-saldo" style="cursor: pointer;"></i></h4>
      <div class="d-flex justify-content-around mt-4">
        <a class="btn btn-light btn-icon shadow-sm" style="border-radius: 10px;" href="{{ route('member.transfer') }}">
          <i class="bi bi-arrow-left-right"></i>
          <span>Transfer</span>
        </a>
        <a class="btn btn-light btn-icon shadow-sm" style="border-radius: 10px;" href="{{ route('member.payment') }}">
          <i class="bi bi-credit-card"></i>
          <span>Bayar</span>
        </a>
        <a class="btn btn-light btn-icon shadow-sm" style="border-radius: 10px;" href="{{ route('member.history') }}">
          <i class="bi bi-clock-history"></i>
          <span>History</span>
        </a>
      </div>
    </div>
  </div>

  <div class="text-center mb-5" style="font-size: 18px;">
    
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
