<!DOCTYPE html>
<html lang="id" class="h-100">

<head>
    @include('layouts.partials.head')
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            @yield('content')
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    @include('layouts.partials.foot')
</body>

</html>
