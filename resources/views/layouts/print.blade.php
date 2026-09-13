<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.head')

    <style>
        /* set page 14 */
        @page {
            size: A4;
            margin: 0;
        }

        .print {
            padding: 0;
            margin: 0;
            border: 1px solid #000;
            width: 210mm;
        }

        .sidebar-right {
            display: none;
        }

        @media print {
            .print {
                width: 100%;
                margin: 0;
                padding: 0;
                border: 0;
            }

            .sidebar-right {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div id="main-wrapper">
        <div class="print mx-auto">
            @yield('content')
        </div>
    </div>

    @include('layouts.partials.foot')
</body>

</html>
