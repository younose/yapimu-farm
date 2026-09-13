@extends('layouts.auth')

@section('content')
    <div class="row h-100">
        {{-- Mobile: clean, minimal sign-in --}}
        <div class="col-12 d-flex d-md-none flex-column justify-content-center p-0 gopay-login-mobile">
            <div class="gopay-login-clean">
                <h2 class="gopay-login-clean-title">Login</h2>

                <form id="loginFormMobile" action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <div class="gopay-login-clean-field">
                            <i class="fa-solid fa-user"></i>
                            <input name="email" type="text" class="form-control gopay-login-clean-input"
                                placeholder="Username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="gopay-login-clean-field">
                            <i class="fa-solid fa-lock"></i>
                            <input name="password" type="password" id="dlab-password-mobile"
                                class="form-control gopay-login-clean-input" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="text-center mb-4">
                        <span class="show-pass-mobile gopay-login-clean-toggle">Show Password</span>
                    </div>
                    <button onclick="submitForm('#loginFormMobile')" type="button"
                        class="btn btn-primary w-100 gopay-login-clean-btn">Login</button>
                </form>
            </div>
        </div>

        {{-- Desktop: original split layout --}}
        <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center d-none d-md-block">
            <div class="login-form">
                <div class="text-center">
                    <h3 class="title">Sign In</h3>
                    <p>Sign in to your account to start using Dompact</p>
                </div>
                <form id="loginForm" action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-1 text-dark">Email/ Username</label>
                        <input name="email" type="text" class="form-control form-control"
                            placeholder="Masukkan email/username" required>
                    </div>
                    <div class="mb-4 position-relative">
                        <label class="mb-1 text-dark">Password</label>
                        <input name="password" type="password" id="dlab-password" class="form-control form-control"
                            placeholder="Masukkan password" required>
                        <span class="show-pass eye">

                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye"></i>

                        </span>
                    </div>
                    <div class="text-center mb-4">
                        <button onclick="submitForm('#loginForm')" type="button" class="btn btn-primary btn-block">Sign
                            In</button>
                    </div>
                    {{-- <p class="text-center">Not registered?
                        <a class="btn-link text-primary" href="page-register.html">Register</a>
                    </p> --}}
                </form>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 d-none d-md-block">
            <div class="pages-left h-100">
                <div class="login-content">
                    <a href="{{url('')}}">
                        <img src="{{ Storage::url(config('app.logo_full')) }}" class="mb-3" alt=""
                            style="
                            max-width: 180px;
                            max-height: 80px;
                            "></a>
                    <p>{{ config('app.description') }}</p>
                </div>
                <div class="login-media text-center">
                    <img src="images/login.png" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.show-pass-mobile').forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    const input = document.getElementById('dlab-password-mobile');
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    toggle.textContent = isHidden ? 'Hide Password' : 'Show Password';
                });
            });
        });
    </script>
@endpush
