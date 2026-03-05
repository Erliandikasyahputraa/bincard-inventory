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
    @php
        $company = \App\Models\CompanySetting::first();
    @endphp
    
    <div class="header">
        <table style="width: 100%; border: none; margin-top: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 80px; text-align: left; padding: 0;">
                    @if($company && $company->logo_path)
                        <img src="{{ public_path('storage/' . $company->logo_path) }}" style="width: 70px; height: auto;">
                    @else
                        <div style="width: 70px; height: 70px; background: #eee; text-align: center; line-height: 70px; font-size: 10px; color: #999;">No Logo</div>
                    @endif
                </td>
                <td style="border: none; text-align: left; padding: 0 10px;">
                    <h1 style="margin: 0; font-size: 20px;">{{ $company ? $company->nama_perusahaan : 'Nama Perusahaan' }}</h1>
                    <p style="margin: 4px 0 0 0; font-size: 10px; color: #555;">{{ $company ? $company->alamat : 'Alamat Perusahaan' }}<br>Telp: {{ $company ? $company->telepon : '-' }} | Email: {{ $company ? $company->email : '-' }}</p>
                </td>
                <td style="border: none; text-align: right; padding: 0;">
                    <h3 style="margin: 0; font-size: 16px;">LAPORAN TRANSAKSI STOK</h3>
                    <p style="margin: 4px 0 0 0; font-size: 10px; font-weight: bold;">Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
                    @if($tipeTransaksi)
                        <p style="margin: 2px 0 0 0; font-size: 10px; font-weight: bold;">Tipe: {{ $tipeTransaksi }}</p>
                    @endif
                </td>
            </tr>
        </table>
        <hr style="margin-top: 15px; border: 1px solid #333;">
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

