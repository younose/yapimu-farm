<!DOCTYPE html>
<html lang="id" class="h-100">

<head>
    @include('layouts.partials.head')
</head>

<body class="vh-100">
    <div class="authincation h-100"
        style="background-image: url(images/student-bg.jpg); background-repeat:no-repeat; background-size:cover;">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6 col-sm-12">
                    <div class="form-input-content  error-page">
                        <h1 class="error-text text-primary"> @yield('code', 500)</h1>
                        <h4> @yield('message', __('Server Error'))</h4>
                        {{-- <p>You do not have permission to view this resource.</p> --}}
                        <a class="btn btn-primary" href="{{ route('dashboard') }}">Back to Home</a>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12">
                    @php
                        $code = app()->view->getSections()['code'];
                        $image = asset('images/error-media.png');

                        if ($code >= 500) {
                            $image = asset('images/error.png');
                        }
                    @endphp
                    <img class="move-2 error-media" src="{{ $image }}" alt="">
                </div>
            </div>
        </div>
    </div>
</body>

<!--**********************************
 Scripts
***********************************-->
<!-- Required vendors -->
@include('layouts.partials.foot')
</body>

</html>
