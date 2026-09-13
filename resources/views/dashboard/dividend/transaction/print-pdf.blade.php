<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Saldo Investor - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1d2939;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 8px;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #667085;
            margin-bottom: 16px;
        }

        .info-table td {
            padding: 2px 6px;
            border: none;
        }

        .info-table .label {
            width: 130px;
            color: #475467;
        }

        .summary-table {
            margin-bottom: 18px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #98a2b3;
            text-align: right;
        }

        .summary-table th {
            background: #f2f4f7;
            text-align: center;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #98a2b3;
        }

        .detail-table th {
            background: #1d2939;
            color: #fff;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-masuk {
            color: #12b76a;
        }

        .text-keluar {
            color: #f04438;
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #667085;
            text-align: right;
        }
    </style>
</head>

<body>
    @include('exports.partials.pdf-kop', ['kop' => $kop, 'logoBase64' => $logoBase64])

    <div class="title">Laporan Saldo Investor</div>
    <div class="subtitle">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</div>

    <table class="info-table" style="margin-bottom: 14px;">
        <tr>
            <td class="label">Nama Investor</td>
            <td>: <strong>{{ $user->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Lembar Saham</td>
            <td>: {{ $user->shares }}</td>
        </tr>
    </table>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-masuk">Rp {{ number_format($debit, 0, ',', '.') }}</td>
                <td class="text-keluar">Rp {{ number_format($credit, 0, ',', '.') }}</td>
                <td><strong>Rp {{ number_format($balance, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Deskripsi</th>
                <th style="width: 65px;">Tipe</th>
                <th style="width: 110px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $i => $transaction)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $transaction->created_at->format('d/m/Y') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td class="text-center">{{ $transaction->type === 'debit' ? 'Masuk' : 'Keluar' }}</td>
                    <td class="text-end {{ $transaction->type === 'debit' ? 'text-masuk' : 'text-keluar' }}">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">{{ $kop->name }}</div>
</body>

</html>
