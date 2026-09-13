<div class="dropdown">
    <a class="dropdown-toggle hide-arrow" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a href="javascript:void(0)" class="text-warning dropdown-item" onclick="openEditModel({{ $id }})">
                <i class="fas fa-edit fa-fw"></i>
                <span>Ubah</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="deleteForm('{{ route('investors.destroy', $id) }}', onDeleteSuccess)"
                class="text-danger dropdown-item">
                <i class="fas fa-trash fa-fw"></i>
                <span>Hapus</span>
            </a>
        </li>
    </ul>
</div>
