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
    <div id="app2">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('admin') }}">
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
                        @if(Gate::any(['user.list', 'store.list', 'role.list', 'tag.list']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.master') }}</a>
                            <ul class="dropdown-menu">
                                @if(Gate::any(['user.list', 'user.create', 'user.edit', 'user.delete']))
                                <li><a class="dropdown-item" href="{{ route('users.index') }}">{{ __('navbar.master.user') }}</a></li>
                                @endif
                                @if(Gate::any(['store.list', 'store.create', 'store.edit', 'store.delete']))
                                <li><a class="dropdown-item" href="{{ route('stores.index') }}">{{ __('navbar.master.store') }}</a></li>
                                @endif
                                @if(gate::any(['role.list', 'role.create', 'role.edit', 'role.delete']))
                                <li><a class="dropdown-item" href="{{ route('roles.index') }}">{{ __('navbar.master.role') }}</a></li>
                                @endif
                                @if(Gate::any(['tag.list', 'tag.create', 'tag.edit', 'tag.delete']))
                                <li><a class="dropdown-item" href="{{ route('tags.index') }}">{{ __('navbar.master.tag') }}</a></li>
                                @endif
                                @if(Gate::any(['promo.list', 'promo.create', 'promo.edit', 'promo.delete']))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.master.promo') }}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(Gate::any(['topup_store.list', 'topup_store.approval', 'topup_user.list', 'payment_request.list']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.transaction') }}</a>
                            <ul class="dropdown-menu">
                                @if(Gate::any(['topup_store.list', 'topup_store.create', 'topup_store.edit', 'topup_store.cancel']))
                                <li><a class="dropdown-item" href="{{ route('topup_store.index') }}">{{ __('navbar.transaction.topup_store') }}</a></li>
                                @endif
                                @if(Gate::any(['topup_store.approval']))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.approve_store') }}</a></li>
                                @endif
                                @if(Gate::any(['topup_user.list', 'topup_user.create', 'topup_user.edit', 'topup_user.cancel']))
                                <li><a class="dropdown-item" href="{{ route('topup_user.index') }}">{{ __('navbar.transaction.topup_user') }}</a></li>
                                @endif
                                @if(Gate::any(['topup_user.approval']))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.transaction.approve_user' )}}</a></li>
                                @endif
                                @if(Gate::any(['payment_request.list', 'payment_request.create', 'payment_request.edit']))
                                <li><a class="dropdown-item" href="{{ route('payment_request.index') }}">{{ __('navbar.transaction.payment_request' )}}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(Gate::any(['report_user.list', 'report_topup_store.list', 'report_topup_user.list', 'report_received_payment.list', 'report_mutation.list']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.report') }}</a>
                            <ul class="dropdown-menu">
                                @if(Gate::any('report_user.list'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.report.user_summary') }}</a></li>
                                @endif
                                @if(Gate::any('report_topup_store.list'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.report.topup_store') }}</a></li>
                                @endif
                                @if(Gate::any('report_topup_user.list'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.report.topup_user') }}</a></li>
                                @endif
                                @if(Gate::any('report_received_payment.list'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.report.received_payment') }}</a></li>
                                @endif
                                @if(Gate::any('report_mutation.list'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.report.mutation') }}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(Gate::any(['process.dailyclose', 'process.monthlyclose', 'process.dailybackup', 'process.monthlybackup']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ __('navbar.menu.process') }}</a>
                            <ul class="dropdown-menu">
                                @if(Gate::any('process.dailyclose'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.process.daily_close') }}</a></li>
                                @endif
                                @if(Gate::any('process.monthlyclose'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.process.monthly_close') }}</a></li>
                                @endif
                                @if(Gate::any('process.dailybackup'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.process.daily_backup') }}</a></li>
                                @endif
                                @if(Gate::any('process.monthlybackup'))
                                <li><a class="dropdown-item" href="#">{{ __('navbar.process.monthly_backup') }}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
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
                        <p>&copy; 2024 Aneka Jaya Smart. All Rights Reserved.</p>
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
