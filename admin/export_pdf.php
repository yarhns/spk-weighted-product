<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/helpers.php';
require __DIR__ . '/functions/wp.php';

// Autoload Composer (Dompdf)
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// --- Ambil data & hitung ---
try {
    $kriteria   = wp_get_kriteria();
    $alternatif = wp_get_alternatif();
    $hasil      = wp_hitung($kriteria, $alternatif);
} catch (Throwable $ex) {
    die('Gagal menghitung: ' . htmlspecialchars($ex->getMessage()));
}

// --- Siapkan HTML laporan ---
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil WP</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 3px double #4f46e5;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 18px;
            color: #4f46e5;
            letter-spacing: .5px;
        }
        .header h2 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #1e293b;
            font-weight: normal;
        }
        .header .meta {
            font-size: 10px;
            color: #64748b;
        }

        h3 {
            font-size: 12px;
            margin: 20px 0 8px;
            color: #4f46e5;
            border-left: 4px solid #4f46e5;
            padding-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10px;
        }
        th {
            background: #4f46e5;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #4338ca;
        }
        td {
            padding: 6px;
            border: 1px solid #cbd5e1;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .center { text-align: center; }
        .right  { text-align: right; }

        .rank-1 {
            background: #ecfdf5 !important;
            font-weight: bold;
            color: #065f46 !important;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-cost    { background: #fee2e2; color: #b91c1c; }
        .badge-benefit { background: #dcfce7; color: #15803d; }

        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #cbd5e1;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }

        .kesimpulan {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            padding: 12px;
            margin-top: 16px;
            font-size: 11px;
        }
        .kesimpulan strong {
            color: #4338ca;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN HASIL PERHITUNGAN</h1>
    <h2>Sistem Pendukung Keputusan Pemilihan Rumah Tinggal</h2>
    <h2 style="font-size:11px;">Metode Weighted Product (WP)</h2>
    <div class="meta">
        Dicetak pada: ' . date('d F Y, H:i') . ' WIB
    </div>
</div>

<h3>1. Data Kriteria</h3>
<table>
    <thead>
        <tr>
            <th style="width:40px;" class="center">No</th>
            <th>Kode</th>
            <th>Kriteria</th>
            <th class="center">Jenis</th>
            <th class="center">Bobot</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($kriteria as $k) {
    $badgeClass = $k['jenis'] === 'cost' ? 'badge-cost' : 'badge-benefit';
    $html .= '
        <tr>
            <td class="center">' . $no++ . '</td>
            <td>' . htmlspecialchars($k['code']) . '</td>
            <td>' . htmlspecialchars($k['kriteria']) . '</td>
            <td class="center"><span class="badge ' . $badgeClass . '">' . ucwords($k['jenis']) . '</span></td>
            <td class="center">' . $k['bobot'] . '</td>
        </tr>';
}
$html .= '
    </tbody>
</table>

<h3>2. Matriks Keputusan (Nilai Alternatif)</h3>
<table>
    <thead>
        <tr>
            <th style="width:40px;" class="center">No</th>
            <th>Kode</th>
            <th>Nama Alternatif</th>';
foreach ($kriteria as $k) {
    $html .= '<th class="center">' . htmlspecialchars($k['code']) . '</th>';
}
$html .= '
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($alternatif as $a) {
    $html .= '
        <tr>
            <td class="center">' . $no++ . '</td>
            <td>' . htmlspecialchars($a['code']) . '</td>
            <td>' . htmlspecialchars($a['alternatif']) . '</td>';
    foreach ($kriteria as $k) {
        $html .= '<td class="center">' . $a['nilai'][$k['code']] . '</td>';
    }
    $html .= '</tr>';
}
$html .= '
    </tbody>
</table>

<h3>3. Normalisasi Bobot (Wj)</h3>
<table>
    <thead>
        <tr>
            <th>Keterangan</th>';
foreach ($kriteria as $k) {
    $html .= '<th class="center">' . htmlspecialchars($k['code']) . '</th>';
}
$html .= '
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Bobot</strong></td>';
foreach ($kriteria as $k) {
    $html .= '<td class="center">' . $k['bobot'] . '</td>';
}
$html .= '
        </tr>
        <tr>
            <td><strong>Wj</strong></td>';
foreach ($kriteria as $k) {
    $html .= '<td class="center">' . number_format($hasil['wj'][$k['code']], 6, '.', '') . '</td>';
}
$html .= '
        </tr>
        <tr>
            <td><strong>Wj Bertanda</strong></td>';
foreach ($kriteria as $k) {
    $val = $hasil['wj_bertanda'][$k['code']];
    $color = $val < 0 ? '#b91c1c' : '#15803d';
    $html .= '<td class="center" style="color:' . $color . ';font-weight:bold;">' . number_format($val, 6, '.', '') . '</td>';
}
$html .= '
        </tr>
    </tbody>
</table>

<h3>4. Hasil Perangkingan (Vektor V)</h3>
<table>
    <thead>
        <tr>
            <th style="width:70px;" class="center">Ranking</th>
            <th>Kode</th>
            <th>Nama Alternatif</th>
            <th class="center">Nilai Vi</th>
        </tr>
    </thead>
    <tbody>';

foreach ($hasil['ranking'] as $r) {
    $rowClass = $r['peringkat'] === 1 ? ' class="rank-1"' : '';
    $html .= '
        <tr' . $rowClass . '>
            <td class="center">' . $r['peringkat'] . '</td>
            <td>' . htmlspecialchars($r['code']) . '</td>
            <td>' . htmlspecialchars($r['alternatif']) . '</td>
            <td class="center">' . number_format($r['vi'], 6, '.', '') . '</td>
        </tr>';
}
$html .= '
    </tbody>
</table>';

// Kesimpulan
$top = $hasil['ranking'][0] ?? null;
if ($top) {
    $html .= '
    <div class="kesimpulan">
        <strong>Kesimpulan:</strong> Berdasarkan perhitungan metode Weighted Product,
        alternatif terbaik adalah <strong>' . htmlspecialchars($top['alternatif']) . '</strong>
        (kode <strong>' . htmlspecialchars($top['code']) . '</strong>)
        dengan nilai preferensi (Vi) sebesar <strong>' . number_format($top['vi'], 6, '.', '') . '</strong>.
    </div>';
}

$html .= '
<div class="footer">
    Laporan ini dihasilkan secara otomatis oleh Sistem Pendukung Keputusan Weighted Product.<br>
    &copy; ' . date('Y') . ' Dear Haens
</div>

</body>
</html>';

// --- Generate PDF ---
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output: tampil di browser (bisa langsung disimpan/dicetak)
$filename = 'Laporan_Hasil_WP_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);
exit;