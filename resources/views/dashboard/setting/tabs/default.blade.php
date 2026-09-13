<div class="mt-4">
    <form id="default-form" action="{{ route('settings.default.update') }}" method="put">
        @csrf
        @php
            $currSettings = json_decode($settings['default']);
        @endphp

        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->default_share_count->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->default_share_count->name }}</label>
                    <input required min="0" type="text" id="{{ $currSettings->default_share_count->key }}"
                        name="{{ $currSettings->default_share_count->key }}"
                        value="{{ $currSettings->default_share_count->value }}" class="form-control w-full number">
                </div>
            </div>
            <div class="col-sm-12 col-md-8">
                <label for="{{ $currSettings->default_share_price->key }}"
                    class="block font-medium text-gray-700">{{ $currSettings->default_share_price->name }}</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Rp</span>
                    <input required min="0" type="text" id="{{ $currSettings->default_share_price->key }}"
                        name="{{ $currSettings->default_share_price->key }}"
                        value="{{ $currSettings->default_share_price->value }}" class="form-control w-full number">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <label for="{{ $currSettings->default_zakat_percentage->key }}"
                    class="block font-medium text-gray-700">{{ $currSettings->default_zakat_percentage->name }}</label>
                <div class="input-group mb-3">
                    <input required min="0" type="number" min="0" max="100" step="0.1"
                        id="{{ $currSettings->default_zakat_percentage->key }}"
                        name="{{ $currSettings->default_zakat_percentage->key }}"
                        value="{{ $currSettings->default_zakat_percentage->value }}" class="form-control w-full">
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <label for="{{ $currSettings->default_cooperative_percentage->key }}"
                    class="block font-medium text-gray-700">{{ $currSettings->default_cooperative_percentage->name }}</label>
                <div class="input-group mb-3">
                    <input required min="0" type="number" min="0" max="100" step="0.1"
                        id="{{ $currSettings->default_cooperative_percentage->key }}"
                        name="{{ $currSettings->default_cooperative_percentage->key }}"
                        value="{{ $currSettings->default_cooperative_percentage->value }}" class="form-control w-full">
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <label for="{{ $currSettings->default_management_fee->key }}"
                    class="block font-medium text-gray-700">{{ $currSettings->default_management_fee->name }}</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Rp</span>
                    <input required min="0" type="text" id="{{ $currSettings->default_management_fee->key }}"
                        name="{{ $currSettings->default_management_fee->key }}"
                        value="{{ $currSettings->default_management_fee->value }}" class="form-control w-full number">
                </div>
            </div>

        </div>
        <button type="button" class="btn btn-primary" onclick="submitForm('#default-form')">Simpan</button>
    </form>
</div>
