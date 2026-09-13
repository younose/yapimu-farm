@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-calendar"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Periode</p>
                            <h4 class="mb-0">
                                <div class="d-flex flex-column">
                                    @if ($openPeriod)
                                        @php
                                            $status = $openPeriod->getStatuse($openPeriod->status);
                                        @endphp
                                        <h4>{{ $openPeriod->name }}
                                        </h4>
                                        <small class="text-muted fs-6">
                                            <small
                                                class="badge {{ $status->bg }} {{ $status->color }}">{{ $status->label }}</small>
                                            ({{ $openPeriod->start_date }} -
                                            {{ $openPeriod->end_date }})
                                        </small>
                                    @else
                                        <h6>
                                            <span class="badge bg-danger">Tidak ada periode dibuka</span>
                                        </h6>
                                    @endif

                                </div>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-user-shield fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Admin</p>
                            <h4 class="mb-0"><span class="saldo">{{ $adminCount }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-3">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-users fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Investor</p>
                            <h4 class="mb-0"><span class="saldo">{{ $investorCount }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-file fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Lembar Saham</p>
                            <h4 class="mb-0"><span class="saldo">{{ $stockCount }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-user-check fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saham Diakuisi</p>
                            <h4 class="mb-0"><span class="saldo">{{ $stockActive }}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Harga Perlembar</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($stockPrice) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-hand-holding-dollar fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Saldo Investor</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($investorBalance) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-wallet fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Saldo Yayasan</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($yayasanBalance) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-money-bill-transfer fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Penarikan Pending</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($withdrawPending) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Manajemen Belum Dibayar</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($manajemenUnpaidAmount) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Koperasi Belum Dibayar</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($koperasiUnpaidAmount) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Zakat Belum Dibayar</p>
                            <h5 class="mb-0"><span class="saldo">{{ idrFormat($zakatUnpaidAmount) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Laporan Tutup Periode Terakhir</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="closing-period-table" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Pendapatan</th>
                                    <th>Pengeluaran</th>
                                    <th>Laba Bersih</th>
                                    <th>Dividen/Lembar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($closingPeriods as $closingPeriod)
                                    <tr>
                                        <td>{{ $closingPeriod->period->name }}</td>
                                        <td>{{ idrFormat($closingPeriod->revenue) }}</td>
                                        <td>{{ idrFormat($closingPeriod->expenses) }}</td>
                                        <td>{{ idrFormat($closingPeriod->net_profit) }}</td>
                                        <td>{{ idrFormat($closingPeriod->dividend_nominal) }}</td>
                                        <td>
                                            @if ($closingPeriod->net_profit >= 0)
                                                <span class="badge bg-success text-white">Untung</span>
                                            @else
                                                <span class="badge bg-danger text-white">Rugi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada periode yang ditutup</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Investor dengan Saham Terbesar
                        @if ($latestClosingPeriod)
                            ({{ $latestClosingPeriod->period->name }})
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="top-investor-table" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Investor</th>
                                    <th>Lembar</th>
                                    <th>Modal</th>
                                    <th>Dividen Periode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topInvestors as $topInvestor)
                                    <tr>
                                        <td>{{ $topInvestor->user->name }}</td>
                                        <td>{{ $topInvestor->shares }}</td>
                                        <td>{{ idrFormat($topInvestor->fund) }}</td>
                                        <td>
                                            <span class="badge bg-success text-white">{{ idrFormat($topInvestor->total_dividend) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data investor</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
    <script>
        let periods = @json($periods);

        const periodNames = periods.map(period => period.name);
        const debits = periods.map(period => period.debit);
        const credits = periods.map(period => period.credit);

        $('#closing-period-table').DataTable({
            ordering: false,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, 'Semua']],
        });

        $('#top-investor-table').DataTable({
            ordering: false,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, 'Semua']],
        });
    </script>
@endpush
