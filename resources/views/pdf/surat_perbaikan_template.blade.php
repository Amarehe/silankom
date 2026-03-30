<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Perbaikan - {{ $perbaikan->no_surat }}</title>
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
            display: block;
            text-align: center;
            margin-bottom: 20px;
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
        <h2>SURAT KETERANGAN PERBAIKAN PERALATAN ELEKTRONIK</h2>
    </div>

    <!-- NOMOR SURAT -->
    <div class="nomor-surat">
        Nomor: {{ $perbaikan->no_surat }}
    </div>

    <!-- ISI -->
    <div class="isi">
        Perbaikan Peralatan Elektronik Lembaga yang telah diserahkan oleh :
    </div>

    <!-- DATA TABEL -->
    <table class="data-tabel">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $perbaikan->pemohon->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIP/NRP</td>
            <td>:</td>
            <td>{{ $perbaikan->pemohon->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td>UNIT KERJA</td>
            <td>:</td>
            <td>{{ $perbaikan->pemohon->unitkerja->nm_unitkerja ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $perbaikan->pemohon->jabatan->nm_jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Barang berupa</td>
            <td>:</td>
            <td>{{ $perbaikan->nama_barang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Merk/Type</td>
            <td>:</td>
            <td>{{ $perbaikan->merek->nama_merek ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Seri</td>
            <td>:</td>
            <td>{{ $perbaikan->serial_number ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kategori</td>
            <td>:</td>
            <td>{{ $perbaikan->kategori->nama_kategori ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <td>:</td>
            <td>{{ $perbaikan->jumlah }} Unit</td>
        </tr>
        <tr>
            <td>Tanggal Pengajuan</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($perbaikan->tgl_pengajuan)->locale('id')->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td>Keluhan/Kerusakan</td>
            <td>:</td>
            <td>{{ $perbaikan->keluhan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Hasil Perbaikan</td>
            <td>:</td>
            <td>{{ $perbaikan->tindakan ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Nota Dinas</td>
            <td>:</td>
            <td>{{ $perbaikan->nodis ?? '-' }}</td>
        </tr>
    </table>

    <!-- LOKASI DAN TANGGAL -->
    <div class="lokasi-tanggal">
        Jakarta, {{ \Carbon\Carbon::parse($perbaikan->tgl_perbaikan ?? $perbaikan->updated_at)->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-container clearfix">
        <div class="ttd-box left">
            <div class="jabatan-penandatangan">YANG MEMPERBAIKI,</div>
            <div class="jabatan-detail">
                @if($perbaikan->teknisi)
                    {{ $perbaikan->teknisi->jabatan->nm_jabatan ?? 'Teknisi' }}<br>
                    {{ $perbaikan->teknisi->unitkerja->nm_unitkerja ?? 'Bag Komlek' }}
                @else
                    Teknisi<br>
                    Bag Komlek
                @endif
            </div>
            <div class="nama-penandatangan">{{ $perbaikan->teknisi->name ?? '-' }}</div>
            <div class="nip-penandatangan">NIP. {{ $perbaikan->teknisi->nip ?? '-' }}</div>
        </div>

        <div class="ttd-box right">
            <div class="jabatan-penandatangan">PEMILIK BARANG,</div>
            <div class="jabatan-detail">
                {{ $perbaikan->pemohon->jabatan->nm_jabatan ?? 'Supervisor' }}<br>
                {{ $perbaikan->pemohon->unitkerja->nm_unitkerja ?? '-' }}
            </div>
            <div class="nama-penandatangan">{{ $perbaikan->pemohon->name ?? '-' }}</div>
            <div class="nip-penandatangan">NIP/NRP. {{ $perbaikan->pemohon->nip ?? '-' }}</div>
        </div>
    </div>
</body>

</html>
