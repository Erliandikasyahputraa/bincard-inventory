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
    <div class="header">
        <h2>SURAT JALAN</h2>
        <p><strong>{{ $suratJalan->nomor_surat_jalan }}</strong></p>
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
</body>
</html>
