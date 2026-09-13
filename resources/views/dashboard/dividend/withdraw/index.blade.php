@extends('layouts.dashboard')

@section('content')
    @php
        $canWithdraw = auth()->user()->balance >= 10000;
    @endphp
    @if (auth()->user()->hasRole('investor'))
        <div class="row gopay-hero-row d-block d-md-none">
            <div class="col-12">
                <div class="gopay-hero gopay-hero-compact">
                    <div class="gopay-hero-topbar">
                        <div class="gopay-hero-profile">
                            <span class="gopay-hero-avatar gopay-hero-icon-badge">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </span>
                            <div>
                                <div class="gopay-hero-welcome">Penarikan Dividen</div>
                                <div class="gopay-hero-name">
                                    <span class="pending">0</span> Penarikan Pending
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-label">Saldo Tersedia</div>
                    <div class="gopay-hero-value balance">Rp 0</div>

                    @if ($canWithdraw)
                        <button type="button" class="gopay-hero-action-btn" onclick="openAddModel()">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            Ajukan Penarikan
                        </button>
                    @else
                        <button type="button" class="gopay-hero-action-btn" disabled
                            title="Saldo tidak mencukupi (minimal Rp 10.000)" style="opacity: .5; cursor: not-allowed;">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            Saldo Tidak Mencukupi
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="row gopay-mobile-hide">
        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-hand-holding-dollar fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Saldo Investor</p>
                            <h5 class="mb-0 balance">
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-money-bill-transfer fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Penarikan Pending</p>
                            <h4 class="mb-0 pending">
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header gopay-mobile-hide">
                    <div class="row w-100">
                        <div class="col-sm-12 col-md-8">
                            <div class="btn-group mb-2 btn-group-xs">
                                @hasanyrole('investor')
                                    @if ($canWithdraw)
                                        <button type="button" class="btn btn-xs btn-primary" id="btn-add"
                                            onclick="openAddModel()">
                                            <i class="fas fa-hand-holding-dollar fa-fw"></i>
                                            <span>Ajukan Penarikan</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-primary" id="btn-add" disabled
                                            title="Saldo tidak mencukupi (minimal Rp 10.000)">
                                            <i class="fas fa-hand-holding-dollar fa-fw"></i>
                                            <span>Saldo Tidak Mencukupi</span>
                                        </button>
                                    @endif
                                @endhasanyrole
                                <button type="button" class="btn btn-xs btn-secondary"
                                    onclick="reloadDatatable(tableName)">
                                    <i class="fas fa-sync fa-fw"></i>
                                    <span>Reload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @hasanyrole('investor')
                        <div class="alert alert-warning" role="alert">
                            <i class="fa fa-info-circle"></i> <strong>Perhatian!</strong> <br>
                            <p>Untuk penarikan metode tunai, wajib mencetak/mengunduh bukti pengajuan penarikan dividen</p>
                        </div>
                    @endhasanyrole
                    <div class="table-responsive">
                        {{ $dataTable->table() }}
                    </div>
                    <div class="text-center mt-2 d-none" id="withdraw-load-more-wrap">
                        <button type="button" id="withdraw-load-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" data-bs-backdrop="static" id="formModal" tabindex="-1" aria-labelledby="formModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Ajukan Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- alert --}}
                    <div class="alert alert-warning" role="alert">
                        <i class="fa fa-info-circle"></i>
                        <strong>Perhatian!</strong>
                        <ul>
                            <li>Penarikan akan diproses dalam waktu 1x24 jam</li>
                            <li>Penarikan akan dikirimkan ke rekening yang terdaftar</li>
                            <li>Minimal penarikan Rp 10.000</li>
                        </ul>
                    </div>

                    <form id="form" enctype="multipart/form-data">
                        @csrf
                        {{-- metode pnarikan Tunai, Transfer Bank --}}
                        <div class="mb-3">
                            <span class="d-block mb-2">Metode Penarikan</span>
                            <div class="mb-3 mb-0">
                                <label class="radio-inline me-3">
                                    <input type="radio" class="form-check-input" name="transfer_type" value="cash"
                                        checked>
                                    Tunai</label>
                                <label class="radio-inline me-3">
                                    <input type="radio" class="form-check-input" name="transfer_type" value="bank">
                                    Transfer
                                    Bank</label>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="bank-selector">
                            <label for="bank_account_id" class="form-label">Pilih Akun Bank</label>
                            <select class="form-control select2" name="bank_account_id" id="bank_account_id" required
                                aria-label="Default select example">
                                <option value="null" selected disabled>- Pilih Bank -</option>
                                @foreach ($bankAccount as $bank)
                                    <option value="{{ $bank->id }}">
                                        {{ $bank->bank_name }} - {{ $bank->account_number }} a/n {{ $bank->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Nominal Penarikan</label>

                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="withdraw-amount form-control" id="amount" name="amount"
                                    maxlength="255" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btn-save" onclick="submitForm('#form', onSubmitSuccess)" type="button"
                        class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="formModalValidate" tabindex="-1"
        aria-labelledby="formModalValidateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalValidateLabel">Validasi Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- alert --}}
                    <div class="alert alert-warning" role="alert">
                        <i class="fa fa-info-circle"></i>
                        <strong>Perhatian!</strong>
                        <ul>
                            <li>Silahkan unggah bukti transfer jika penarikan disetujui</li>
                            <li>Catatan penolakan penarikan wajib diisi jika penarikan ditolak</li>
                        </ul>
                    </div>
                    <form id="formValidate" enctype="multipart/form-data">
                        @csrf
                        {{-- informasi bank --}}
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Informasi Bank</label>
                            <textarea class="form-control" name="bank_name" id="bank_name" cols="30" rows="2" disabled></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="shares" class="form-label">Status Penarikan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="withdrawStatus" id="radioApprove"
                                    value="terima" checked onchange="handleRadioChange()">
                                <label class="form-check-label" for="radioApprove">
                                    Setujui Penarikan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="withdrawStatus" id="radioReject"
                                    value="tolak" onchange="handleRadioChange()">
                                <label class="form-check-label" for="radioReject">
                                    Tolak Penarikan
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3" id="rejectWithdraw" style="display: none;">
                            <label for="email" class="form-label">Catatan Penolakan Penarikan</label>
                            <textarea class="form-control" name="noteReject" id="noteReject" cols="30" rows="3" required></textarea>
                        </div>

                        <div id="approveWithdraw" style="display: block;">
                            <div class="mb-3">
                                <label for="email" class="form-label">Unggah Bukti Transfer</label>
                                <input class="form-control" type="file" name="proof" id="proof"
                                    accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Catatan Penarikan</label>
                                <textarea class="form-control" name="noteApprove" id="noteApprove" cols="30" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btn-save-validate" onclick="submitForm('#formValidate', onSubmitValidateSuccess)"
                        type="button" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Validasi Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-3">
                            {{-- status --}}
                            <p class="mb-1">Status Penarikan</p>
                            <h5 class="detail-status mb-0">Pending</h5>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">
                            {{-- nominal --}}
                            <p class="mb-1">Nominal Penarikan</p>
                            <h5 class="detail-amount mb-0">Rp 1.000.000</h5>
                        </div>

                        <div class="col-sm-12 mb-3">
                            {{-- bank --}}
                            <p class="mb-1">Bank
                            </p>
                            <h5 class="detail-bank mb-0">BCA - 1234567890 a/n John Doe</h5>
                        </div>

                        <div class="col-sm-12 mb-3">
                            {{-- bukti transfer --}}
                            <p class="mb-1">Bukti Transfer</p>
                            <img src="https://via.placeholder.com/150" alt="Bukti Transfer"
                                class="detail-proof img-fluid" height="150">
                        </div>

                        <div class="col-sm-12 mb-3">
                            {{-- catatan --}}
                            <p class="mb-1">Catatan</p>
                            <h5 class="detail-description mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                Quisquam, quod.</h5>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">
                            {{-- waktu --}}
                            <p class="mb-1">Waktu</p>
                            <h5 class="detail-time mb-0">2021-09-01 12:00:00</h5>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-3">
                            {{-- user --}}
                            <p class="mb-1">Validasi Oleh</p>
                            <h5 class="detail-validator mb-0">Admin</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'withdraw-table';
        const user = @json(auth()->user());

        function onSubmitSuccess(response) {
            hideModal();
            reloadDatatable(tableName);
        }

        function onSubmitValidateSuccess(response) {
            hideModalValidate();
            reloadDatatable(tableName);
        }

        function onDeleteSuccess(response) {
            reloadDatatable(tableName);
        }

        let withdrawPageLength = 10;
        let withdrawLoadMoreInitialized = false;

        function renderWidget() {
            const dt = window.LaravelDataTables[tableName];
            const {
                balance,
                pending
            } = dt.ajax.json();

            $('.balance').html(idrFormat(balance ?? 0));
            $('.pending').html(pending ?? 0);

            // mobile investor view: "Tampilkan Lebih Banyak"
            if (document.body.classList.contains('is-wallet-home')) {
                const info = dt.page.info();
                const wrap = document.getElementById('withdraw-load-more-wrap');
                const btn = document.getElementById('withdraw-load-more');

                if (wrap && btn) {
                    wrap.classList.toggle('d-none', info.recordsDisplay <= info.length);

                    if (!withdrawLoadMoreInitialized) {
                        withdrawLoadMoreInitialized = true;
                        btn.addEventListener('click', function() {
                            withdrawPageLength += 10;
                            dt.page.len(withdrawPageLength).draw(false);
                        });
                    }
                }
            }
        }

        function hideModal() {
            $('#formModal').modal('hide');
        }

        function openAddModel() {
            const method = 'post';
            const action = route('withdraws.index');

            $('#formModalLabel').text('Ajukan Penarikan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('#formModal').modal('show');
        }

        function hideModalValidate() {
            $('#formModalValidate').modal('hide');
        }

        function openValidateModel(id) {
            const method = 'put';
            const action = route('withdraws.update', id);

            $('#formModalValidateLabel').text('Validasi Penarikan');
            $('#formValidate').attr('action', action);
            $('#formValidate').attr('method', method);
            $('#formValidate').trigger('reset');

            const showRoute = route('withdraws.show', id);
            const modalBody = $('#formModalValidate').find('.modal-body');
            const innerModalBody = modalBody.html();

            $.ajax({
                type: "get",
                url: showRoute,
                dataType: "json",
                beforeSend: function() {
                    modalBody.html(spinner);
                    $('#btn-save-validate').attr('disabled', true);

                    $('#formModalValidate').modal('show');
                },
                success: function(response) {
                    modalBody.html(innerModalBody);
                    $('#btn-save-validate').attr('disabled', false);

                    for (const key in response.data) {
                        if (Object.hasOwnProperty.call(response.data, key)) {
                            const element = response.data[key];
                            $(`[name="${key}"]`).val(element);
                        }
                    }

                    $('[name="password"]').val('');
                    $('[name="photo"]').val('');

                    let bankaccount = response.data.bank_account;
                    $('[name="bank_name"]').val("Penarikan Tunai");

                    if (bankaccount) {
                        $('[name="bank_name"]').val(
                            `${bankaccount.bank_name} \n${bankaccount.account_number} a/n ${bankaccount.account_name}`
                        );
                    }

                },
                error: function(xhr, status, error) {
                    hideModal();

                    const response = xhr.responseJSON;
                    toastr.error(response?.message ?? 'Terjadi kesalahan saat mengambil data');
                    modalBody.html(innerModalBody);
                    $('#btn-save').attr('disabled', false);

                }
            });
        }

        function handleRadioChange() {
            const radioTerima = document.getElementById('radioApprove');
            const radioTolak = document.getElementById('radioReject');
            const approveWithdraw = document.getElementById('approveWithdraw');
            const rejectWithdraw = document.getElementById('rejectWithdraw');

            if (radioTerima.checked) {
                approveWithdraw.style.display = 'block';
                rejectWithdraw.style.display = 'none';
            } else if (radioTolak.checked) {
                approveWithdraw.style.display = 'none';
                rejectWithdraw.style.display = 'block';
            }
        }

        function openDetail(id) {
            const showRoute = route('withdraws.show', id);
            const detailModal = $('#detailModal');
            const modalBody = detailModal.find('.modal-body');
            const innerModalBody = modalBody.html();

            $.ajax({
                type: "get",
                url: showRoute,
                dataType: "json",
                beforeSend: function() {
                    modalBody.html(spinner);
                },
                success: function(response) {
                    modalBody.html(innerModalBody);

                    const data = response.data;

                    let status = `<span class="badge ${data.status_detail.bg}">
                            ${data.status_detail.label}</span>`;
                    $('.detail-status').html(status);

                    $('.detail-amount').html(idrFormat(data.amount));

                    let bankaccount = data.bank_account;
                    $('.detail-bank').html("Penarikan Tunai");

                    if (bankaccount) {
                        $('.detail-bank').html(
                            `${bankaccount.bank_name} <br> ${bankaccount.account_number} a/n ${bankaccount.account_name}`
                        );
                    }

                    if (data.proof_url) {
                        // create an image element
                        let img = document.createElement('img');
                        img.src = data.proof_url;
                        img.alt = 'Bukti Transfer';
                        img.className = 'detail-proof img-fluid';
                        img.height = 150;

                        $('.detail-proof').replaceWith(img);
                    } else {
                        // remove the image element and replace with text
                        $('.detail-proof').replaceWith('<h5 class="detail-proof mb-0">-</h5>');
                    }

                    $('.detail-description').html(data.note ?? '-');
                    $('.detail-time').html(data.processed_at ?? '-');

                    let validator = data.processed_by;
                    $('.detail-validator').html(validator?.name ?? '-');

                    detailModal.modal('show');
                },
                error: function(xhr, status, error) {
                    modalBody.html(innerModalBody);
                    detailModal.modal('show');

                    const response = xhr.responseJSON;
                    toastr.error(response?.message ?? 'Terjadi kesalahan saat mengambil data');
                }
            });
        }

        $(".withdraw-amount").on("input", function() {
            // wait 200ms after the user stops typing
            let timeout = null;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const step = 1000;
                this.value = this.value.replace(/\D/g, "");

                if (this.value.length > 4) {
                    if (this.value % step !== 0) {
                        this.value = Math.floor(this.value / step) * step;
                    }
                }

                this.value = thousandFormat(Number(this.value));
            }, 200);
        });

        // transfer_type change
        $('input[name="transfer_type"]').on('change', function() {
            const transferType = $(this).val();
            if (transferType === 'bank') {
                $('#bank-selector').removeClass('d-none');
            } else {
                $('#bank-selector').addClass('d-none');
            }
        });
    </script>
@endpush
