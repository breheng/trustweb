<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/Chart.min.js') }}"></script> -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ route('welcome', [], false) }}">
                     <img src="{{ asset('img/TrustNew.png')}}" height="50px" alt="{{ config('app.name', 'Laravel') }}" />
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                      <li class="nav-item">
                          <a title="Go To Download Page" class="nav-link" href="{{ route('app.list', [], false) }}">Download App</a>
                      </li>
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a title="Click to login" class="nav-link" href="{{ route('login', [], false) }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                              <a title="Register New Account Here" class="nav-link" href="{{ route('register', [], false) }}">{{ __('Register') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                  @if (Session::get('staffdata')['role'] <= 1)
                                  <a class="dropdown-item" href="{{ route('admin', [], false) }}">Admin</a>
                                  @endif
                                  <a class="dropdown-item" href="{{ route('staff', [], false) }}">Staff Home</a>
                                  <a class="dropdown-item" href="{{ route('reports', [], false) }}">Reports</a>
                                  <a class="dropdown-item" href="#"
                                     onclick="event.preventDefault();
                                                   document.getElementById('logout-form').submit();">
                                      {{ __('Logout') }}
                                  </a>

                                  <form id="logout-form" action="{{ route('logout', [], false) }}" method="POST" style="display: none;">
                                      @csrf
                                  </form>
                                </div>
                            </li>
                        @endguest
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown2" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Help <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown2">
                              <a class="dropdown-item" href="{{ route('feedback', [], false) }}">Feedback</a>
                              <a class="dropdown-item" href="{{ route('home', [], false) }}">Quick Guide</a>
                              <a class="dropdown-item" href="{{ route('adminlist', [], false) }}">Admins</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
@yield('page-js')
</html>
