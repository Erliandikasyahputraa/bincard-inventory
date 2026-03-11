<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Surat Jalan Harian</title>
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
                    <h3 style="margin: 0; font-size: 16px;">REKAP SURAT JALAN HARIAN</h3>
                    <p style="margin: 4px 0 0 0; font-size: 10px; font-weight: bold;">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
                </td>
            </tr>
        </table>
        <hr style="margin-top: 15px; border: 1px solid #333;">
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Keluaran</th>
                <th>No. Surat Jalan</th>
                <th>Penerima Barang</th>
                <th>Nama Produk</th>
                <th>SKU/Barcode</th>
                <th>Jml Keluar</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $i => $t)
                @php
                    $nomorSj = $t->suratJalan ? $t->suratJalan->nomor_surat_jalan : $t->reference_id;
                    $penerima = ($t->suratJalan && $t->suratJalan->customer) ? $t->suratJalan->customer->nama : '-';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">{{ $t->created_at->format('H:i') }}</td>
                    <td>{{ $nomorSj ?? '-' }}</td>
                    <td>{{ $penerima }}</td>
                    <td>{{ $t->product->name ?? '-' }}</td>
                    <td>{{ $t->product->barcode ?? '-' }}</td>
                    <td style="text-align: center; color: red; font-weight: bold;">-{{ $t->quantity }}</td>
                    <td>{{ $t->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Belum ada surat jalan keluar pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
