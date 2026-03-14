<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tanda Terima Peminjaman - {{ $peminjaman->nomor_surat }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        .kop-surat {
            display: inline-block;
            text-align: center;
            margin-bottom: 20px;
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
            margin: 30px 0 10px 0;
        }

        .judul h2 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 25px;
            font-size: 12pt;
        }

        .isi {
            text-align: justify;
            margin-bottom: 15px;
        }

        .data-tabel {
            width: 100%;
            margin-left: 40px;
        }

        .data-tabel td {
            padding: 3px 8px;
            vertical-align: top;
        }

        .data-tabel td:first-child {
            width: 180px;
        }

        .data-tabel td:nth-child(2) {
            width: 20px;
        }

        .lokasi-tanggal {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 5px;
            margin-right: 50px;
        }

        .ttd-container {
            margin-top: 20px;
            width: 100%;
        }

        .ttd-box {
            width: 48%;
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }

        .ttd-box.left {
            float: left;
        }

        .ttd-box.right {
            float: right;
        }

        .ttd-box .jabatan-penandatangan {
            margin-bottom: 3px;
        }

        .ttd-box .jabatan-detail {
            font-size: 12pt;
            margin-bottom: 70px;
        }

        .ttd-box .nama-penandatangan {
            margin-bottom: 2px;
        }

        .ttd-box .nip-penandatangan {
            font-size: 12pt;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h3>SEKRETARIAT UTAMA LEMHANNAS RI</h3>
        <h4>BIRO TELEMATIKA</h4>
        <div class="garis-kop"></div>
    </div>

    <!-- JUDUL -->
    <div class="judul">
        <h2>TANDA TERIMA PEMINJAMAN PERALATAN ELEKTRONIK</h2>
    </div>

    <!-- NOMOR SURAT -->
    <div class="nomor-surat">
        Nomor: {{ $peminjaman->nomor_surat }}
    </div>

    <!-- ISI -->
    <div class="isi">
        Penyerahan Peminjaman Peralatan Lembaga yang dipinjamkan kepada :
    </div>

    <!-- DATA TABEL -->
    <table class="data-tabel">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $peminjam->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIP/NRP</td>
            <td>:</td>
            <td>{{ $peminjam->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td>UNIT KERJA</td>
            <td>:</td>
            <td>{{ $peminjam->unitkerja->nm_unitkerja ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $peminjam->jabatan->nm_jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Barang berupa</td>
            <td>:</td>
            <td>{{ $barang->nama_barang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Merk/Type</td>
            <td>:</td>
            <td>{{ $barang->merek->nama_merek ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Seri</td>
            <td>:</td>
            <td>{{ $barang->serial_number ?? '-' }} @if($barang->label) / {{ $barang->label }}@endif</td>
        </tr>
        <tr>
            <td>Tahun Perolehan</td>
            <td>:</td>
            <td>{{ $barang->tahun ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <td>:</td>
            <td>1 Unit</td>
        </tr>
        <tr>
            <td>Kelengkapan</td>
            <td>:</td>
            <td>{{ $peminjaman->kelengkapan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kondisi</td>
            <td>:</td>
            <td>{{ ucfirst($peminjaman->kondisi_barang) }}</td>
        </tr>
    </table>

    <!-- LOKASI DAN TANGGAL -->
    <div class="lokasi-tanggal">
        Jakarta, {{ \Carbon\Carbon::parse($peminjaman->tanggal_serah_terima)->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-container clearfix">
        <div class="ttd-box left">
            <div class="jabatan-penandatangan">YANG MENYERAHKAN,</div>
            <div class="jabatan-detail">
                {{ $admin->jabatan->nm_jabatan ?? 'Kepala Bagian' }}<br>
                {{ $admin->unitkerja->nm_unitkerja ?? 'Bag Komlek' }}
            </div>
            <div class="nama-penandatangan">{{ $admin->name ?? '-' }}</div>
            <div class="nip-penandatangan">NIP. {{ $admin->nip ?? '-' }}</div>
        </div>

        <div class="ttd-box right">
            <div class="jabatan-penandatangan">YANG MENERIMA,</div>
            <div class="jabatan-detail">
                {{ $peminjam->jabatan->nm_jabatan ?? 'Supervisor' }}<br>
                {{ $peminjam->unitkerja->nm_unitkerja ?? '-' }}
            </div>
            <div class="nama-penandatangan">{{ $peminjam->name ?? '-' }}</div>
            <div class="nip-penandatangan">NIP/NRP. {{ $peminjam->nip ?? '-' }}</div>
        </div>
    </div>
</body>

</html>