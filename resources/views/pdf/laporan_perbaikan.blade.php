<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Perbaikan Peralatan - {{ $periode }}</title>
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
        <h2>REKAP PERBAIKAN PERALATAN ELEKTRONIK</h2>
    </div>

    <div class="periode">
        {{ $periode }}
    </div>

    <!-- RINGKASAN -->
    <div class="ringkasan">
        <table>
            <tr>
                <td class="label">Total Perbaikan</td>
                <td>:</td>
                <td><strong>{{ $statistik['total'] }}</strong></td>
            </tr>
            <tr>
                <td class="label">Selesai</td>
                <td>:</td>
                <td><span class="badge badge-success">{{ $statistik['selesai'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Diproses</td>
                <td>:</td>
                <td><span class="badge badge-info">{{ $statistik['diproses'] }}</span></td>
            </tr>
            <tr>
                <td class="label">Tidak Bisa Diperbaiki</td>
                <td>:</td>
                <td><span class="badge badge-danger">{{ $statistik['tidak_bisa_diperbaiki'] }}</span></td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA -->
    @if($data->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No Surat</th>
                <th>Pemohon</th>
                <th>Unit Kerja</th>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Merek</th>
                <th>Keluhan</th>
                <th>Tgl Pengajuan</th>
                <th>Status</th>
                <th>Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="nowrap">{{ $item->no_surat_perbaikan ?? '-' }}</td>
                <td>{{ $item->pemohon?->name ?? '-' }}</td>
                <td>{{ $item->pemohon?->unitkerja?->nm_unitkerja ?? '-' }}</td>
                <td>{{ $item->nm_barang ?? '-' }}</td>
                <td>{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                <td>{{ $item->merek?->nama_merek ?? '-' }}</td>
                <td>{{ $item->keluhan ?? '-' }}</td>
                <td class="text-center nowrap">{{ $item->tgl_pengajuan ? \Carbon\Carbon::parse($item->tgl_pengajuan)->translatedFormat('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    @php
                        $statusClass = match($item->status_perbaikan) {
                            'diajukan' => 'badge-gray',
                            'diproses' => 'badge-info',
                            'selesai' => 'badge-success',
                            'tidak_bisa_diperbaiki' => 'badge-danger',
                            default => 'badge-gray',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $item->status_perbaikan)) }}
                    </span>
                </td>
                <td>{{ $item->teknisi?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-notice">Tidak ada data perbaikan untuk periode ini.</div>
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
