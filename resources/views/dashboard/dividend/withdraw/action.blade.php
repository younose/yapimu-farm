       @if ($status == 'pending')
           {{-- validasi --}}
           <a href='javascript:void(0)' onclick="openValidateModel('{{ $id }}')" class='btn btn-xs btn-warning'><i class='fa fa-check'></i>
               Validasi</a>
       @else
           <a href='javascript:void(0)' onclick='openDetail("{{ $id }}")' class='btn btn-xs btn-primary'><i class='fa fa-eye'></i>
               Detail</a>
       @endif
