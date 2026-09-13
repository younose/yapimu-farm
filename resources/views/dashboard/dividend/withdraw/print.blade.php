@extends('layouts.print')

@section('content')
    <div class="p-4">
        <div class="text-center mb-4">
            <h5 class="text-center">Bukti Penarikan Dividen</h5>
            <h5 class="text-center">{{ config('app.name') }}</h5>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <p class="p-0 m-0">Investor: {{ $withdraw->user->name }}</p>
                <p class="p-0 m-0">Username/ Email: {{ $withdraw->user->username ?? '-' }} / {{ $withdraw->user->email }}</p>
                <p class="p-0 m-0">Metode Penarikan: {{ $withdraw->transfer_type == 'bank' ? 'Transfer Bank' : 'Tunai' }}</p>
            </div>

            <div class="col-12">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nominal Penarikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{ $withdraw->created_at->format('d F Y') }}</td>
                            <td>Rp {{ thousandFormat($withdraw->amount) }}</td>
                        </tr>

                        <tr>
                            <td colspan="2" class="text-right">Total</td>
                            <td>Rp {{ thousandFormat($withdraw->amount) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <p class="p-0 m-0">Terbilang: {{ terbilang($withdraw->amount) }} rupiah</p>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            @if ($withdraw->bankAccount)
                <div class="col-12">
                    <p class="p-0 m-0">Nama Bank: {{ $withdraw->bankAccount->bank_name }}</p>
                    <p class="p-0 m-0">Nomor Rekening: {{ $withdraw->bankAccount->account_number }}</p>
                    <p class="p-0 m-0">Atas Nama: {{ $withdraw->bankAccount->account_name }}</p>
                </div>
            @endif

            <div class="col-12 px-4 mt-4">
                <div class="text-end">
                    <p class="text-end mb-5"><strong>Investor</strong></p>
                    <p class="text-end"><u><strong>( {{ $withdraw->user->name }} )</strong></u></p>
                </div>
            </div>

            <div class="col-12 mt-4">
                <p class="p-0 m-0 fh-1 text-center">Terima kasih telah berinvestasi di {{ config('app.name') }}</p>
                <p class="p-0 m-0 fh-1 text-center">Semoga investasi Anda berkembang dan memberikan keuntungan yang maksimal
                </p>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
@endpush
