<div class="dropdown">
    <a class="dropdown-toggle hide-arrow" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a target="_blank" href="{{route('database-backups.show', $name)}}" class="text-info dropdown-item" href="#">
                <i class="fas fa-download fa-fw"></i>
                <span>Unduh</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="deleteForm('{{route('database-backups.destroy', $name)}}', onDeleteSuccess)
            "
                class="text-danger dropdown-item">
                <i class="fas fa-trash fa-fw"></i>
                <span>Hapus</span>
            </a>
        </li>
    </ul>
</div>
