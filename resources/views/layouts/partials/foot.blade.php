@routes
<!-- Required vendors -->
<script src="{{asset('/vendor/global/global.min.js')}}"></script>
<script src="{{asset('/vendor/jquery-nice-select/js/jquery.nice-select.min.js')}}"></script>

<!-- Apex Chart -->
<script src="{{asset('/vendor/wnumb/wNumb.js')}}"></script>

<script src="{{asset('/js/custom.min.js')}}"></script>
<script src="{{asset('/js/dlabnav-init.js')}}"></script>
<script src="{{asset('/js/styleSwitcher.js')}}"></script>

<script src="{{asset('/vendor/toastr/js/toastr.min.js')}}"></script>
<script src="{{asset('/vendor/fontawesome-free-6.5.2-web/js/fontawesome.min.js')}}"></script>
<script src="{{asset('/vendor/fontawesome-free-6.5.2-web/js/solid.min.js')}}"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script src="{{asset('/js/helper.js')}}"></script>
<script src="{{ asset('/js/pwa-install.js') }}?v={{ filemtime(public_path('js/pwa-install.js')) }}"></script>

@stack('js')
