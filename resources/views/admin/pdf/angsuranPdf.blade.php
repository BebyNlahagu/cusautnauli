<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Angsuran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 50px;
            top: -5px;
            width: 80px;
        }

        .kop-surat {
            text-align: center;
        }

        .kop-surat h3 {
            margin: 0;
            font-size: 3rem;
            color: #08b430;
            font-weight: bold;
        }

        .kop-surat p {
            margin: 0;
            font-size: 1rem;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #08b430;
            text-align: left;
            font-weight: bold;
            color: #333;
        }

        td {
            text-align: left;
            color: #555;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signature-inline {
            display: flex;
            justify-content: space-between;
            margin-top: 80px;
            padding: 0 40px;
        }

        .signature-left,
        .signature-right {
            width: 40%;
            text-align: center;
            font-weight: bold;
        }

        .signature-left p,
        .signature-right p {
            margin: 5px 0;
        }

        hr {
            border: 1px solid black;
            margin-top: 10px;
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('img/logo.png') }}" alt="Logo" style="width: 150%;">
        </div>
        <div class="kop-surat" style="padding-left: 5rem;">
            <h3>CU SAUT MAJU NAULI</h3>
            <p>Nagasaribu iii, Kecamatan Lintongnihuta, <br>Kabupaten Humbang Hasundutan</p>
        </div>
    </div>

    <hr>

    <div class="header">
        <h2 style="font-weight: bold;">Laporan Simpanan</h2>
    </div>

    <table id="basic-datatables" class="display table table-striped table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal Pinjaman</th>
                <th>Jumlah Angsuran</th>
                <th>Total Pinjaman</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @php
                $groupedAngsuran = $angsuran->groupBy('nasabah_id');
                $no = 1;
            @endphp

            @foreach ($groupedAngsuran as $nasabahId => $angsurans)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $angsurans->first()->nasabah->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($angsurans->first()->created_at)->translatedFormat('l, d F Y') }}
                    </td>
                    <td>{{ $angsurans->first()->pinjaman->lama_pinjaman }}</td>
                    <td>Rp {{ number_format($angsurans->first()->pinjaman->terima_total, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="signature-inline">
        <div class="signature-left">
            <br>
            <p>Ketua</p>
            <br><br>
            <p><u>H. Nababan</u></p>
        </div>
        <div class="signature-right" style="margin-left:400px;margin-top:-550px">
            <p>Medan, {{ date('d-m-Y') }}</p>
            <p>Penanggung Jawab,</p>
            <br><br>
            <p><u>{{ $user->name }}</u></p>
        </div>
    </div>

</body>
</html>
