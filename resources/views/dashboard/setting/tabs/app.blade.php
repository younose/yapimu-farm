<div class="mt-4">
    <form id="app-form" action="{{ route('settings.app.update') }}" method="put" enctype="multipart/form-data">
        @csrf
        @php
            $currSettings = json_decode($settings['app']);
        @endphp
        <div class="mb-3">
            <label for="{{ $currSettings->app_name->key }}"
                class="block font-medium text-gray-700">{{ $currSettings->app_name->name }}</label>
            <input minlength="3" maxlength="200" required type="text" id="{{ $currSettings->app_name->key }}"
                name="{{ $currSettings->app_name->key }}" value="{{ $currSettings->app_name->value }}"
                class="form-control w-full">
        </div>

        <div class="mb-3">
            <label for="{{ $currSettings->app_description->key }}"
                class="block font-medium text-gray-700">{{ $currSettings->app_description->name }}</label>
            <textarea required id="{{ $currSettings->app_description->key }}" name="{{ $currSettings->app_description->key }}"
                class="form-control w-full">{{ $currSettings->app_description->value }}</textarea>
        </div>


        <div class="row">
            <div class="col-sm-12 col-md-7">
                <div class="mb-3">
                    <label for="{{ $currSettings->app_url->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->app_url->name }}</label>
                    <input minlength="3" maxlength="200" required type="url"
                        id="{{ $currSettings->app_url->key }}" name="{{ $currSettings->app_url->key }}"
                        value="{{ $currSettings->app_url->value }}" class="form-control w-full">
                </div>
            </div>
            <div class="col-sm-12 col-md-5">
                <div class="row align-items-center">
                    <div class="col-sm-8 col-md-8">
                        <div class="mb-3">
                            <label for="{{ $currSettings->app_favicon->key }}"
                                class="block font-medium text-gray-700">{{ $currSettings->app_favicon->name }}</label>
                            <input accept="image/*" type="file" id="{{ $currSettings->app_favicon->key }}"
                                name="{{ $currSettings->app_favicon->key }}" class="form-control mb-3">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <a class="mt-3" href="{{ Storage::url($currSettings->app_favicon->value) }}" target="_blank">
                            <img src="{{ Storage::url($currSettings->app_favicon->value) }}" alt="favicon"
                                class="avatar-image avatar-xl">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="row align-items-center">
                    <div class="col-sm-8 col-md-8">
                        <div class="mb-3">
                            <label for="{{ $currSettings->app_logo->key }}"
                                class="block font-medium text-gray-700">{{ $currSettings->app_logo->name }}</label>
                            <input accept="image/*" type="file" id="{{ $currSettings->app_logo->key }}"
                                name="{{ $currSettings->app_logo->key }}" class="form-control mb-3">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <a class="mt-3" href="{{ Storage::url($currSettings->app_logo->value) }}"
                            target="_blank">
                            <img src="{{ Storage::url($currSettings->app_logo->value) }}" alt="favicon"
                                class="avatar-image avatar-xl">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="row align-items-center">
                    <div class="col-sm-8 col-md-8">
                        <div class="mb-3">
                            <label for="{{ $currSettings->app_logo_full->key }}"
                                class="block font-medium text-gray-700">{{ $currSettings->app_logo_full->name }}</label>
                            <input accept="image/*" type="file" id="{{ $currSettings->app_logo_full->key }}"
                                name="{{ $currSettings->app_logo_full->key }}" class="form-control mb-3">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <a class="mt-3" href="{{ Storage::url($currSettings->app_logo_full->value) }}"
                            target="_blank">
                            <img src="{{ Storage::url($currSettings->app_logo_full->value) }}" alt="favicon"
                                class="avatar-image avatar-xl">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="submitForm('#app-form')">Simpan</button>
    </form>
</div>
