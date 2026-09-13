<div class="pt-4">
    <div class="alert alert-warning">
        <div class="alert-title">Peringatan!</div>
        <div class="alert-message">
            <p>Pastikan anda telah memahami konfigurasi database sebelum melakukan perubahan. Kesalahan konfigurasi
                database dapat menyebabkan aplikasi tidak berjalan dengan semestinya. <strong>Pastikan anda telah
                    melakukan tes koneksi database sebelum menyimpan perubahan.</strong>
            </p>
        </div>
    </div>
</div>
<div class="mt-4">
    <form id="database-form" action="{{ route('settings.database.update') }}" method="put">
        @csrf
        @php
            $currSettings = json_decode($settings['database']);
        @endphp
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_driver->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_driver->name }}</label>
                    <select class="form-control wide mb-3" id="{{ $currSettings->database_driver->key }}"
                        name="{{ $currSettings->database_driver->key }}">
                        @foreach ($databaseConnections as $key => $item)
                            <option value="{{ $key }}"
                                {{ $currSettings->database_driver->value == $key ? 'selected' : '' }}>
                                {{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_host->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_host->name }}</label>
                    <input required type="text" id="{{ $currSettings->database_host->key }}" placeholder="localhost"
                        name="{{ $currSettings->database_host->key }}"
                        value="{{ $currSettings->database_host->value }}" class="form-control w-full">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_port->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_port->name }}</label>
                    <input required type="text" id="{{ $currSettings->database_port->key }}" placeholder="3306"
                        name="{{ $currSettings->database_port->key }}"
                        value="{{ $currSettings->database_port->value }}" class="form-control w-full">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_database->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_database->name }}</label>
                    <input required type="text" id="{{ $currSettings->database_database->key }}"
                        name="{{ $currSettings->database_database->key }}"
                        value="{{ $currSettings->database_database->value }}" class="form-control w-full">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_username->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_username->name }}</label>
                    <input required type="text" id="{{ $currSettings->database_username->key }}"
                        name="{{ $currSettings->database_username->key }}"
                        value="{{ $currSettings->database_username->value }}" class="form-control w-full">
                </div>
            </div>

            <div class="col-sm-12 col-md-4">
                <div class="mb-3">
                    <label for="{{ $currSettings->database_password->key }}"
                        class="block font-medium text-gray-700">{{ $currSettings->database_password->name }}</label>
                    <input type="text" id="{{ $currSettings->database_password->key }}"
                        name="{{ $currSettings->database_password->key }}"
                        value="{{ $currSettings->database_password->value }}" class="form-control w-full">
                </div>
            </div>
        </div>

        <button type="button" onclick="testDatabase('#database-form')" class="btn btn-secondary">Tes Koneksi
            Database</button>
        <button type="button" onclick="submitForm('#database-form')" class="btn btn-primary">Simpan</button>
    </form>
</div>
