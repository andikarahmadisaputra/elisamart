<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Elisamart') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Elisamart') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('navbar.login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('navbar.register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.master') }}</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}">{{ __('navbar.master.user') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('stores.index') }}">{{ __('navbar.master.store') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('roles.index') }}">{{ __('navbar.master.role') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('tags.index') }}">{{ __('navbar.master.tag') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.master.promo') }}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.transaction') }}</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.topup_store') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.approve_store') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.topup_member') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.approve_member' )}}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.payment_request' )}}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.report') }}</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.report.user_summary') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.report.topup_store') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.report.topup_member') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.report.received_payment') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.report.mutation') }}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.process') }}</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.process.daily_close') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.process.monthly_close') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.process.daily_backup') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.process.monthly_backup') }}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.utilities') }}</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.utilities.change_password') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('navbar.utilities.change_pin') }}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        {{ __('navbar.logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <footer class="bg-light mt-5">
            <div class="container py-4">
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p>&copy; 2024 ElisaMart. All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="me-2">Facebook</a>
                        <a href="#" class="me-2">Twitter</a>
                        <a href="#" class="me-2">Instagram</a>
                        <a href="#" class="">LinkedIn</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</body>
</html>
