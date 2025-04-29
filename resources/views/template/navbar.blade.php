<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ config('app.name') }}">
    <meta name="author" content="Arthur Prates">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset(env('APP_LOGO')) }}" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/mascara/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/mascara/MascaraMoeda.js') }}"></script>
    <script src="{{ asset('js/mascara/Mascara.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.autocomplete.min.js') }}"></script>
    <link href="{{ asset('assets/css/autocomplete.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    @stack('styles')
</head>

<style>
       body {
      margin: 0;
      font-family: 'Helvetica', 'Arial', sans-serif;
      background-color: #f9f9f9;
    }

    header {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      border-bottom: 1px solid #e0e0e0;
      font-size: 20px;
    }

    header .logo {
      font-weight: bold;
    }

    header .logo span {
      font-weight: normal;
    }

    header .home {
      margin-left: 10px;
      color: red;
      font-weight: bold;
      font-size: 14px;
      text-transform: lowercase;
      border-bottom: 2px solid red;
      padding-bottom: 2px;
    }

    .container {
      display: flex;
    }

    .sidebar {
      width: 300px;
      background: white;
      padding: 20px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .sidebar h3 {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .sidebar label {
      display: block;
      margin-top: 20px;
      font-size: 14px;
      color: #555;
    }

    .sidebar input[type="text"] {
      width: 100%;
      padding: 8px;
      margin-top: 8px;
      box-sizing: border-box;
    }

   

    .content {
      flex-grow: 1;
      padding: 20px;
    }
</style>

<body id="page-top">

    <div id="wrapper">

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <header>
                        <div class="logo">MP<span>Denúncia</span></div>
                        <a href="{{ route('home') }}">
                            <span class="home">home</span>
                        </a>
                        <a href="{{ route('processos.index') }}">
                            <span class="home">Processos</span>
                        </a>
                    </header>


                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    @if(Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        Visitante
                                    @endif
                                </span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">


                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>


                @yield('content')




            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@if(Auth::check()) {{ Auth::user()->name }} @else Visitante @endif está Pronto para
                            Partir?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Se você clicar em Sair irá encerrar a sessão. Tem certeza?</div>
                    <div class="modal-footer">
                        <a class="btn btn-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">
                            {{ __('Sair') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/sb-admin-2.min.js"></script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
