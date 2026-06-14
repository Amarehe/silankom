<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Inventory Barang - {{ $periode }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            size: landscape;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .kop-surat {
            display: block;
            text-align: center;
            margin-bottom: 10px;
            width: 50%;
        }

        .kop-surat h3 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat h4 {
            margin: 2px 0 8px 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .garis-kop {
            border-bottom: 2px solid #000;
            margin: 0;
            width: 100%;
        }

        .judul {
            text-align: center;
            margin: 20px 0 5px 0;
        }

        .judul h2 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .periode {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        /* Ringkasan Statistik */
        .ringkasan {
            margin-bottom: 15px;
        }

        .ringkasan table {
            width: 60%;
            border-collapse: collapse;
        }

        .ringkasan td {
            padding: 3px 10px;
            font-size: 9pt;
        }

        .ringkasan td:first-child {
            width: 200px;
        }

        .ringkasan td:nth-child(2) {
            width: 15px;
        }

        .ringkasan .label {
            font-weight: bold;
        }

        /* Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #1F4E79;
            color: #fff;
            padding: 6px 4px;
            font-size: 8pt;
            text-align: center;
            border: 1px solid #1F4E79;
        }

        .data-table td {
            padding: 4px;
            font-size: 8pt;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background-color: #f2f7fc;
        }

        .data-table .text-center {
            text-align: center;
        }

        .data-table .nowrap {
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-gray { background-color: #e9ecef; color: #495057; }

        /* Footer & Page Numbers */
        #footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            font-size: 8pt;
            color: #666;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .page-number:after {
            content: "Halaman " counter(page);
        }

        .empty-notice {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        /* Signature */
        .signature {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            font-size: 10pt;
        }

        .signature-box.left {
            float: left;
            margin-left: 5%;
        }

        .signature-box.right {
            float: right;
            margin-right: 5%;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <!-- FOOTER ABSOLUTE -->
    <div id="footer">
        Dicetak oleh: {{ $dicetak_oleh }} &nbsp;|&nbsp; 
        Pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d M Y H:i') }} WIB &nbsp;|&nbsp; 
        <span class="page-number"></span>
    </div>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h3>SEKRETARIAT UTAMA LEMHANNAS RI</h3>
        <h4>BIRO TELEMATIKA</h4>
        <div class="garis-kop"></div>
    </div>

    <!-- JUDUL -->
    <div class="judul">
        <h2>REKAP INVENTORY BARANG</h2>
    </div>

    <div class="periode">
        {{ $periode }}
    </div>

    <!-- RINGKASAN -->
    <div class="ringkasan">
        <table>
            <tr>
                <td class="label">Total Barang</td>
                <td>:</td>
                <td><strong>{{ $statistik['total'] }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kondisi Baik</td>
                <td>:</td>
                <td><span class="badge badge-success">{{ $statistik['kondisi_baik'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Perlu Perbaikan</td>
                <td>:</td>
                <td><span class="badge badge-warning">{{ $statistik['perlu_perbaikan'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Rusak</td>
                <td>:</td>
                <td><span class="badge badge-danger">{{ $statistik['rusak'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Tersedia</td>
                <td>:</td>
                <td><span class="badge badge-info">{{ $statistik['tersedia'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Sedang Dipinjam</td>
                <td>:</td>
                <td><span class="badge badge-gray">{{ $statistik['dipinjam'] }}</span></td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA -->
    @if($data->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Merek</th>
                <th>Serial Number</th>
                <th>Label</th>
                <th>Kondisi</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Diinput Oleh</th>
                <th>Keterangan</th>
                <th>Tgl Input</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_barang ?? '-' }}</td>
                <td>{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                <td>{{ $item->merek?->nama_merek ?? '-' }}</td>
                <td class="nowrap">{{ $item->serial_number ?? '-' }}</td>
                <td>{{ $item->label ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ match($item->kondisi) { 'baik' => 'badge-success', 'perlu_perbaikan' => 'badge-warning', 'rusak' => 'badge-danger', default => 'badge-gray' } }}">
                        {{ ucfirst(str_replace('_', ' ', $item->kondisi)) }}
                    </span>
                </td>
                <td class="text-center">{{ $item->tahun ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $item->status === 'tersedia' ? 'badge-success' : 'badge-info' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>{{ $item->user?->name ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="text-center nowrap">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-notice">Tidak ada data inventory untuk periode ini.</div>
    @endif

    <!-- TANDA TANGAN -->
    <div class="signature">
        <div class="signature-box left">
            <p>Dicetak Oleh,</p>
            <br><br><br><br>
            <p><strong>{{ $dicetak_oleh }}</strong></p>
        </div>
        <div class="signature-box right">
            <p>Mengetahui,</p>
            <br><br><br><br>
            <p><strong>................................................</strong></p>
            <p>Pimpinan / Koordinator</p>
        </div>
        <div class="clear"></div>
    </div>

    @if($is_print ?? false)
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    @endif
</body>

</html>
