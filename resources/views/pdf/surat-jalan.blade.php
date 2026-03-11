<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan {{ $suratJalan->nomor_surat_jalan }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #eee; }
        .header { text-align: center; margin-bottom: 24px; }
        .info { margin-bottom: 16px; }
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
                    <h2 style="margin: 0; font-size: 18px;">SURAT JALAN</h2>
                    <p style="margin: 4px 0 0 0; font-size: 12px; font-weight: bold;">{{ $suratJalan->nomor_surat_jalan }}</p>
                </td>
            </tr>
        </table>
        <hr style="margin-top: 15px; border: 1px solid #333;">
    </div>
    <div class="info">
        <p>Tanggal: {{ $suratJalan->tanggal->format('d/m/Y') }}</p>
        @if($suratJalan->customer)
        <p>Pelanggan: {{ $suratJalan->customer->nama }}</p>
        <p>Alamat: {{ $suratJalan->customer->alamat }}</p>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Barcode</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suratJalan->details as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->product->name }}</td>
                <td>{{ $d->product->barcode }}</td>
                <td>{{ $d->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <table style="width: 100%; border: none; margin-top: 50px;">
        <tr style="border: none;">
            <td style="border: none; width: 33%; text-align: center;">
                <p>Penerima Barang</p>
                <br><br><br><br>
                <p style="text-decoration: underline;">{{ $suratJalan->customer ? $suratJalan->customer->nama : '( ................................... )' }}</p>
            </td>
            <td style="border: none; width: 33%; text-align: center;">
                <p>Pengemudi</p>
                <br><br><br><br>
                <p>( ................................... )</p>
            </td>
            <td style="border: none; width: 33%; text-align: center;">
                <p>Yang Mengeluarkan</p>
                <br><br><br><br>
                <p style="text-decoration: underline;">{{ $suratJalan->createdBy ? $suratJalan->createdBy->name : '( ................................... )' }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
