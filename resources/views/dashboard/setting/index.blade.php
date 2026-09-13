@extends('layouts.dashboard')
@push('js')
    <style>
        .full-width {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach ($groups as $i => $item)
                                @php
                                    $isActive = $i == 0;
                                @endphp

                                @if ($isActive)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#{{ $item }}"
                                            aria-selected="true" role="tab"><i class="la la-home me-2"></i>
                                            {{ Str::ucfirst($item) }}</a>
                                    </li>
                                @else
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#{{ $item }}"
                                            aria-selected="false" role="tab" tabindex="-1"><i
                                                class="la la-home me-2"></i>
                                            {{ Str::ucfirst($item) }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach ($groups as $i => $item)
                                @php
                                    $isActive = $i == 0;
                                @endphp

                                @if ($isActive)
                                    <div class="tab-pane fade active show" id="{{ $item }}" role="tabpanel">
                                        @include('dashboard.setting.tabs.' . $item)
                                    </div>
                                @else
                                    <div class="tab-pane fade" id="{{ $item }}" role="tabpanel">
                                        @include('dashboard.setting.tabs.' . $item)
                                    </div>
                                @endif
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function testDatabase(formContainer) {
            let url = route('settings.database.test');
            const method = 'post';

            $(formContainer).attr('action', url);
            $(formContainer).attr('method', method);

            submitForm(formContainer, function(response) {});

            $(formContainer).attr('action', route('settings.database.update'));
            $(formContainer).attr('method', 'put');
        }
    </script>
@endpush
