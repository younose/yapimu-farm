<div class="row page-titles">
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $i => $item)
            <li class="breadcrumb-item {{ $i == count($breadcrumbs) - 1 ? 'active' : '' }}">
                <a href="{{ $item->url }}">{{ $item->name }}</a>
            </li>
        @endforeach
    </ol>
</div>
