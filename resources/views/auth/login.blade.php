@extends('layouts.auth')

@section('content')
    <div class="row h-100">
        <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
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
                    <div class="form-row d-flex justify-content-between mt-4 mb-2">
                        <div class="mb-4">
                            <div class="form-check custom-checkbox mb-3">
                                <input name="remember" type="checkbox" class="form-check-input" id="customCheckBox1"
                                    value="true">
                                <label class="form-check-label mt-1" for="customCheckBox1">Remember
                                    me</label>
                            </div>
                        </div>
                        {{-- <div class="mb-4">
                            <a href="page-forgot-password.html" class="btn-link text-primary">Forgot Password?</a>
                        </div> --}}
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
        <div class="col-xl-6 col-lg-6">
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
