   <!-- All Meta -->
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="author" content="DexignLab">
   <meta name="robots" content="">
   <meta name="keywords"
       content="investasi, ayam broiler, ayam petelur, ternak, ternak ayam, ternak ayam broiler, ternak ayam petelur, ternak ayam kampung, ternak ayam potong, ternak ayam pedaging, ternak ayam organik">
   <meta name="description" content="{{ config('app.description') }}">
   <meta property="og:title" content="{{ config('app.name') }}">
   <meta property="og:description" content="{{ config('app.description') }}">
   {{-- <meta property="og:image" content="https://dompet.dexignlab.com/xhtml/social-image.png">
   <meta name="format-detection" content="telephone=no"> --}}

   <!-- Mobile Specific -->
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- favicon -->
   <link rel="shortcut icon" type="image/png" href="{{ Storage::url(config('app.favicon')) }}">

   <!-- PWA -->
   <link rel="manifest" href="{{ route('pwa.manifest') }}">
   <meta name="theme-color" content="#2f39a9">
   <link rel="apple-touch-icon" href="{{ asset('images/pwa/icon-192.png') }}">

   <!-- Page Title Here -->
   <title> @yield('title', $title ?? 'Selamat Datang') - {{ config('app.name') }}</title>

   <meta name="csrf-token" content="{{ csrf_token() }}">

   <!-- Style css -->
   <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
   <link rel="stylesheet" href="{{ asset('/vendor/toastr/css/toastr.min.css') }}">
   <link href="{{ asset('/vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
   <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
   <link href="{{ asset('/css/gopay-theme.css') }}" rel="stylesheet">
   <style>
       .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
       .dataTables_wrapper .dataTables_paginate .paginate_button.next {
           width: max-content;
       }

       .avatar-image {
           object-fit: cover;
       }

       .avatar-image.avatar-xs {
           width: 30px;
           height: 30px;
       }

       .avatar-image.avatar-sm {
           width: 40px;
           height: 40px;
       }

       .avatar-image.avatar-md {
           width: 50px;
           height: 50px;
       }

       .avatar-image.avatar-lg {
           width: 60px;
           height: 60px;
       }

       .avatar-image.avatar-xl {
           width: 70px;
           height: 70px;
       }

       #main-wrapper.menu-toggle .nav-header a>img.brand-abbr {
           display: block !important;
       }

       #main-wrapper .nav-header a>img.brand-abbr {
           display: none !important;
       }

       .active.end-date.in-range.available {
           color: white !important;
       }

       .active.start-date.in-range.available {
           color: white !important;
       }

       .dropdown-toggle.hide-arrow:after {
           display: none;
       }

       @media (max-width: 1023px) {
           #main-wrapper .nav-header a>img.brand-abbr {
               display: block !important;
           }

           #main-wrapper.menu-toggle .nav-header a>img.brand-abbr {
               display: block !important;
           }

           #main-wrapper .nav-header a>img.brand-title {
               display: none !important;
           }
       }
   </style>

   @stack('css')
