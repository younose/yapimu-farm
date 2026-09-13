@extends('layouts.dashboard')
@push('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                    <hr class="my-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
@endsection
