@extends('layouts.print')
@push('css')
    <style>
        .proof {
            max-width: 60%;
            height: auto;
        }
    </style>
@endpush

@section('content')
    <div class="py-4 pe-4" style="margin-left: 8%;">
        <div class="text-center mb-4">
            <h5 class="text-center">Bukti Nota</h5>
            <p class="text-center">Periode {{ $closing->period->name }} ({{ $closing->period->start_date }} s/d
                {{ $closing->period->end_date }})</p>
        </div>

        @foreach ($journals as $i => $item)
            <div class="my-4">
                <p class="p-0 mb-2 lh-1"><strong>{{ $i + 1 }}. {{ $item->date }}:</strong> {{ $item->description }}
                </p>
                <img src="{{ $item->proof_url }}" alt="Bukti Transaksi" class="proof">
            </div>
        @endforeach
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
