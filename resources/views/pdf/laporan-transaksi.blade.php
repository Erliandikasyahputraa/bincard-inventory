<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Stok</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #444; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .header { text-align: center; margin-bottom: 10px; }
        .info { font-size: 10px; margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>LAPORAN TRANSAKSI STOK</h3>
        <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
        @if($tipeTransaksi)
            <p>Tipe: {{ $tipeTransaksi }}</p>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Barcode</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>User</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $i => $t)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->product->name ?? '-' }}</td>
                    <td>{{ $t->product->barcode ?? '-' }}</td>
                    <td>{{ $t->type }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>{{ $t->user->name ?? '-' }}</td>
                    <td>{{ $t->note ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

