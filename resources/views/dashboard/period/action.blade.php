<div class="dropdown">
    <a class="dropdown-toggle action-menu" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        {{-- jika open maka tampilkan close dan sebaliknya --}}
        @if ($status == 'open')
            <li>
                <a href="javascript:void(0)" class="text-success dropdown-item" onclick="openModalClosePeriod({{ $id }})">
                    <i class="fas fa-lock fa-fw"></i>
                    <span>Tutup Periode</span>
                </a>
            </li>
        @else
            <li>
                <a href="javascript:void(0)" class="text-success dropdown-item"
                    onclick="openPeriode({{ $id }})">
                    <i class="fas fa-lock-open fa-fw"></i>
                    <span>Buka Periode</span>
                </a>
            </li>
        @endif
        <li>
            <a href="javascript:void(0)" class="text-warning dropdown-item"
                onclick="openEditModel({{ $id }})">
                <i class="fas fa-edit fa-fw"></i>
                <span>Ubah</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="deleteForm('{{ route('periods.destroy', $id) }}', onDeleteSuccess)"
                class="text-danger dropdown-item">
                <i class="fas fa-trash fa-fw"></i>
                <span>Hapus</span>
            </a>
        </li>
    </ul>
</div>
