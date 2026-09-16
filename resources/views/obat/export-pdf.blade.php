<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h2 { margin: 0 0 4px 0; font-size: 18px; }
        .meta { margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .footer { margin-top: 10px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h2>Laporan Stok Obat — {{ $generatedAt }}</h2>
    <div class="meta">
        Tanggal/Waktu: {{ $generatedAt }}<br>
        Jumlah Data: {{ number_format($total, 0, ',', '.') }} data
        @if (!empty($activeFilters))
            <br>Filter aktif: {{ $activeFilters }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Obat</th>
                <th>Nama Obat</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Min. Stok</th>
                <th>Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($obats as $i => $obat)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $obat->code }}</td>
                    <td>{{ $obat->name }}</td>
                    <td>{{ $obat->category }}</td>
                    <td>{{ $obat->unit }}</td>
                    <td class="text-right">{{ $obat->stock }}</td>
                    <td class="text-right">{{ $obat->minimum_stock }}</td>
                    <td>{{ $obat->expired_date ? \Carbon\Carbon::parse($obat->expired_date)->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak otomatis dari aplikasi Stok Obat.</div>
</body>
</html>
