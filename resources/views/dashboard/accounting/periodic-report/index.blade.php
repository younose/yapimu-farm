@extends('layouts.dashboard')

@section('content')
    @if (auth()->user()->hasRole('investor'))
        @php
            $periodBadgeColors = ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF', '#9B5DE5', '#F15BB5', '#00BBF9', '#FF9F1C', '#2EC4B6'];
            $periodNumber = $latestClosing ? (explode(' ', $latestClosing->period->name)[1] ?? '?') : '-';
            $periodBadgeColor = $latestClosing ? $periodBadgeColors[$latestClosing->period_id % count($periodBadgeColors)] : '#98A2B3';
            $isProfit = $latestClosing && $latestClosing->net_profit >= 0;
        @endphp
        <div class="row gopay-hero-row d-block d-md-none">
            <div class="col-12">
                <div class="gopay-hero gopay-hero-compact">
                    <div class="gopay-hero-topbar">
                        <div class="gopay-hero-profile">
                            <span class="gopay-hero-avatar gopay-hero-period-badge" style="background: {{ $periodBadgeColor }};">
                                {{ $periodNumber }}
                            </span>
                            <div>
                                <div class="gopay-hero-welcome">Laporan Tutup Periode</div>
                                <div class="gopay-hero-name">
                                    {{ $latestClosing->period->name ?? 'Belum ada periode ditutup' }}
                                    @if ($latestClosing)
                                        <span class="badge {{ $isProfit ? 'bg-success' : 'bg-danger' }} text-white">
                                            {{ $isProfit ? 'Untung' : 'Rugi' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-label">Laba Bersih Periode Terakhir</div>
                    <div class="gopay-hero-value">{{ $latestClosing ? idrFormat($latestClosing->net_profit) : 'Rp 0' }}</div>

                    <div class="gopay-hero-stats">
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-coins"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Dividen/Lembar</div>
                                <div class="gopay-hero-stat-value">{{ $latestClosing ? idrFormat($latestClosing->dividend_nominal) : '-' }}</div>
                            </div>
                        </div>
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-list-check"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Total Periode Ditutup</div>
                                <div class="gopay-hero-stat-value">{{ $totalClosed }} Periode</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header gopay-mobile-hide">
                    <div class="btn-group mb-2 btn-group-xs">
                        <button type="button" class="btn btn-xs btn-secondary" onclick="reloadDatatable(tableName)">
                            <i class="fas fa-sync fa-fw"></i>
                            <span>Reload</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{ $dataTable->table() }}
                    </div>
                    <div class="text-center mt-2 d-none" id="closing-load-more-wrap">
                        <button type="button" id="closing-load-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Detail Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped" id="preview-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="row mt-4 align-items-center justify-content-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">SISA HASIL PANEN</p>
                                <p class="lh-sm m-0 fs-4"><span class="net_profit">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">MANAJEMEN</p>
                                <p class="lh-sm m-0 fs-4"><span class="management_fee">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">KOPERASI</p>
                                <p class="lh-sm m-0 fs-4"><span class="cooperative">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">ZAKAT MAL</p>
                                <p class="lh-sm m-0 fs-4"><span class="zakat">0</span></p>
                            </div>
                            <hr>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4"><b>SISA HASIL PANEN</b></p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend">0</span></b></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center justify-content-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4"><b>DIBAGI 2 KANDANG (YAYASAN DAN INVESTASI)</b></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">Kandang Yayasan</p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend_foundations">0</span></b></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">Kandang Investasi</p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend_investors">0</span></b></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center justify-content-center" id="rhpp-documents-row">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0">Dokumen RHPP <span class="text-muted rhpp-nominal-text"></span></p>
                                <a href="javascript:void(0);" class="btn btn-xs btn-outline-primary rhpp-document-link"
                                    target="_blank" rel="noopener">
                                    <i class="fas fa-file-arrow-down fa-fw"></i> Lihat Dokumen
                                </a>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between mt-2">
                                <p class="lh-sm m-0">Bukti Transfer Masuk RHPP <span class="text-muted rhpp-transfer-nominal-text"></span></p>
                                <a href="javascript:void(0);" class="btn btn-xs btn-outline-primary rhpp-transfer-proof-link"
                                    target="_blank" rel="noopener">
                                    <i class="fas fa-file-arrow-down fa-fw"></i> Lihat Dokumen
                                </a>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between mt-2 rhpp-selisih-row"
                                hidden>
                                <p class="lh-sm m-0"><b>Selisih (Jaminan)</b></p>
                                <p class="lh-sm m-0"><b><span class="rhpp-selisih-text"></span></b></p>
                            </div>
                        </div>
                    </div>

                    @hasanyrole('admin|teller')
                        <hr>
                        <form id="rhpp-documents-form" method="put">
                            @csrf
                            <p class="text-muted mb-2">Lengkapi atau ganti dokumen dan nominal RHPP untuk periode ini
                                (berguna untuk periode lama yang belum memiliki data). Selisih Nominal RHPP dan Nominal
                                Transfer Aktual otomatis tercatat sebagai Jaminan.</p>
                            <div class="row align-items-end">
                                <div class="col-sm-12 col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label">Dokumen RHPP</label>
                                        <input type="file" class="form-control form-control-sm" name="rhpp_document"
                                            accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label">Nominal RHPP</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="number form-control form-control-sm"
                                                name="rhpp_nominal" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label">Bukti Transfer RHPP</label>
                                        <input type="file" class="form-control form-control-sm"
                                            name="rhpp_transfer_proof" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="mb-2">
                                        <label class="form-label">Nominal Transfer Aktual</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="number form-control form-control-sm"
                                                name="rhpp_transfer_nominal" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-sm btn-primary mb-2"
                                        onclick="submitForm('#rhpp-documents-form', onDocumentsUploadSuccess)">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endhasanyrole

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-primary print-report"><i class="fas fa-print fa-fw"></i>
                        Cetak Laporan</button>
                    <button type="button" class="btn btn-sm btn-primary print-proof"><i class="fas fa-print fa-fw"></i>
                        Cetak Bukti Nota</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        let preview = null;
        const tableName = 'closing-table';
        let closingPageLength = 10;
        let closingLoadMoreInitialized = false;

        function onClosingTableDraw() {
            if (!document.body.classList.contains('is-wallet-home')) {
                return;
            }

            const dt = window.LaravelDataTables[tableName];
            const info = dt.page.info();
            const wrap = document.getElementById('closing-load-more-wrap');
            const btn = document.getElementById('closing-load-more');

            if (!wrap || !btn) {
                return;
            }

            wrap.classList.toggle('d-none', info.recordsDisplay <= info.length);

            if (!closingLoadMoreInitialized) {
                closingLoadMoreInitialized = true;
                btn.addEventListener('click', function() {
                    closingPageLength += 10;
                    dt.page.len(closingPageLength).draw(false);
                });
            }
        }

        function detail(id) {
            let button = $(`#closing-${id}`);
            let innerButton = button.html();

            let printButton = $('#previewModal').find('.print-report');
            printButton.off('click');

            let printProofButton = $('#previewModal').find('.print-proof');
            printProofButton.off('click');

            $('#rhpp-documents-form')
                .attr('action', route('closings.documents', id))
                .trigger('reset');

            $.ajax({
                type: "get",
                url: route('closings.show', id),
                beforeSend: function() {
                    button.html(spinner);
                    button.prop('disabled', true);
                },
                success: function(response) {
                    const data = response.data;
                    preview = data;
                    const journals = data.journals;
                    const modal = $('#previewModal');
                    const modalBody = modal.find('.modal-body');
                    const table = modalBody.find('table tbody');

                    table.html('');
                    journals.forEach((journal, index) => {
                        const row = `
                    <tr>
                        <td>${journal.no}</td>
                        <td>${journal.date}</td>
                        <td>${journal.description}</td>
                        <td>${idrFormat(journal.debit)}</td>
                        <td>${idrFormat(journal.credit)}</td>
                        <td>${idrFormat(journal.balance)}</td>
                    </tr>
                `;

                        table.append(row);
                    });

                    const netProfit = data.net_profit ?? 0;
                    const managementFee = data.management_fee ?? 0;
                    const cooperative = data.cooperative ?? 0;
                    const zakat = data.zakat ?? 0;
                    const dividend = data.dividend ?? 0;
                    const dividendFoundations = data.dividend_foundations ?? 0;
                    const dividendInvestors = data.dividend_investors ?? 0;

                    // add on click event to print report
                    printButton.on('click', function() {
                        printReport(id);
                    });

                    printProofButton.on('click', function() {
                        printProof(id);
                    });

                    modal.find('.net_profit').text(idrFormat(netProfit));
                    modal.find('.management_fee').text(idrFormat(managementFee));
                    modal.find('.cooperative').text(idrFormat(cooperative));
                    modal.find('.zakat').text(idrFormat(zakat));
                    modal.find('.dividend').text(idrFormat(dividend));

                    modal.find('.dividend_foundations').text(idrFormat(dividendFoundations));
                    modal.find('.dividend_investors').text(idrFormat(dividendInvestors));

                    updateRhppView(data);

                    modal.modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseJSON);

                    const message = xhr.responseJSON.message;
                    showErrorToast(message ?? 'Terjadi kesalahan saat memuat data');
                },
                complete: function() {
                    button.html(innerButton);
                    button.prop('disabled', false);
                }
            });
        }

        function printReport(id) {
            // download excel file from server
            window.open(route('closings.print', id), '_blank');
        }

        function printProof(id){
            window.open(route('closings.print-proof', id), '_blank');
        }

        function updateRhppView(data) {
            const modal = $('#previewModal');

            modal.find('.rhpp-document-link')
                .attr('href', data.rhpp_document_url ?? 'javascript:void(0);')
                .toggleClass('disabled', !data.rhpp_document_url);
            modal.find('.rhpp-transfer-proof-link')
                .attr('href', data.rhpp_transfer_proof_url ?? 'javascript:void(0);')
                .toggleClass('disabled', !data.rhpp_transfer_proof_url);

            modal.find('.rhpp-nominal-text').text(data.rhpp_nominal != null ? `(${idrFormat(data.rhpp_nominal)})` : '');
            modal.find('.rhpp-transfer-nominal-text').text(data.rhpp_transfer_nominal != null ? `(${idrFormat(data.rhpp_transfer_nominal)})` : '');

            const selisih = data.rhpp_selisih;
            modal.find('.rhpp-selisih-row').prop('hidden', selisih == null);
            if (selisih != null) {
                modal.find('.rhpp-selisih-text').text(idrFormat(selisih));
            }

            // pre-fill the upload form so re-saving one field doesn't blank out the other
            $('#rhpp-documents-form [name="rhpp_nominal"]').val(data.rhpp_nominal ?? '');
            $('#rhpp-documents-form [name="rhpp_transfer_nominal"]').val(data.rhpp_transfer_nominal ?? '');
            numberInput();
        }

        function onDocumentsUploadSuccess(response) {
            updateRhppView(response.data);

            // keep the file inputs cleared, nominal fields already reflect the saved values
            $('#rhpp-documents-form input[type="file"]').val('');
        }
    </script>
@endpush
