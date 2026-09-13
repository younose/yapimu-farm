<div class="pt-4">
    <div class="alert alert-warning">
        <div class="alert-title">Peringatan!</div>
        <div class="alert-message">
            <p>Pastikan anda telah memahami konfigurasi Email sebelum melakukan perubahan. Kesalahan konfigurasi Email
                dapat menyebabkan aplikasi tidak berjalan dengan semestinya.</p>
        </div>
    </div>
    <form action="{{ route('mail-setting.update', ['mail_setting' => 1]) }}" method="post">
        @csrf
        @method('PUT')
        @foreach ($settings['mail'] as $item)
            <div class="mb-3">
                <label for="{{ $item->key }}" class="block font-medium text-gray-700">{{ $item->name }}</label>
                <input type="text" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}"
                    class="form-control w-full">
            </div>
        @endforeach
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
