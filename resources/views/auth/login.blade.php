<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, inital-scale=1.0, maximum-scale=1.0, minimun-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="{{ config('app.name') }}">
    <meta name="author" content="Arthur Prates">

    <title>{{ config('app.name') }}</title>
    {!! RecaptchaV3::initJs() !!}


    <link rel="icon" type="image/png" href="{{ asset(env('APP_LOGO')) }}" />
    <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="assetsqvendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="assets/css/util.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">

</head>

<body>

    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">

                <div class="login100-form-title" style="background-image: url({{ asset(env('APP_LOGO')) }});">
                    <span class="login100-form-title-1">
                        MP DENÚNCIA
                    </span>
                </div>

                <div class="card-body">
                    <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                        @csrf

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Erro!</strong> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Email é necessário">
                            <span class="label-input100">Email</span>
                            <input id="email" placeholder="Digite o seu Email" type="email"
                                class="input100 @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <span class="focus-input100"></span>
                        </div>

                        <div class="wrap-input100 validate-input m-b-18" data-validate="Senha é Necessaria">
                            <span class="label-input100">Senha</span>
                            <input id="password" placeholder="Digite a sua senha" type="password"
                                class="input100 @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <span class="focus-input100"></span>
                        </div>

                        <br>


                        <div class="container w-full p-b-30">
                            <div class="contact100-form-checkbox">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Lembrar-Me') }}
                                </label>
                            </div>
                        </div>

                        <a href="{{ route('password.request') }}" class="mb-5">Esqueceu sua senha?</a>

                        <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            <div class="col-md-6">
                                {!! RecaptchaV3::field('register') !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="container-login100-form-btn">
                            <button type="submit" class="login100-form-btn">
                                {{ __('Entrar') }}
                            </button>
                        </div>

                    </form>

                    <footer class="sticky-footer bg-white text-center">
                        <span> &copy Copyright Arthur Prates {{ date('Y') }}</span>
                    </footer>

                </div>
            </div>
        </div>
    </div>


    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="assets/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="assets/vendor/animsition/js/animsition.min.js"></script>
    <script src="assets/vendor/bootstrap/js/popper.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/vendor/select2/select2.min.js"></script>
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/vendor/countdowntime/countdowntime.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>
