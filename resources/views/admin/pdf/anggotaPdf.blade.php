<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
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

        /* .kop-surat h3 {
            margin: 0;
            font-size: 2rem;
            color: #08b430;
            font-weight: bold;
        } */

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
            <p>Nagasaribu III, Kecamatan Lintongnihuta, <br>Kabupaten Humbang Hasundutan</p>
        </div>
    </div>

    <hr>

    <div class="header">
        <h2 style="font-weight: bold;">Laporan Anggota</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Anggota</th>
                <th>Nama Anggota</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($user as $s)
            <tr>
                @if ($s->role == "User")
                    <td>{{ $no++ }}</td>
                    <td>{{ $s->nm_koperasi }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->created_at)->translatedFormat('l, d F Y') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-inline">
        <div class="signature-left">
            <br>
            <p>Ketua</p>
            <br><br>
            <p><u>H.Nababan</u></p>
        </div>
        <div class="signature-right" style="margin-left:400px;margin-top:-550px">
            <p>Nagasaribu, {{ date('d-m-Y') }}</p>
            <p>Penanggung Jawab,</p>
            <br><br>
            @php
                $admin = $user->firstWhere('role', 'Admin');
            @endphp

            @if ($admin)
                <p><u>{{ $admin->name }}</u></p>
            @endif
        </div>
    </div>

</body>
</html>
