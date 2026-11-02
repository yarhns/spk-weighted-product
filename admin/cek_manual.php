<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/wp.php';

// Ambil data langsung
$kriteria   = wp_get_kriteria();
$alternatif = wp_get_alternatif();

// Tampilkan data yang TERBACA oleh PHP
echo "<h3>Data Kriteria (yang dibaca PHP):</h3><pre>";
foreach ($kriteria as $k) {
    echo "{$k['code']} | jenis={$k['jenis']} | bobot={$k['bobot']}\n";
}
echo "</pre>";

echo "<h3>Data R1 & R2 (yang dibaca PHP):</h3><pre>";
foreach ($alternatif as $a) {
    if (in_array($a['code'], ['R1','R2'])) {
        echo "{$a['code']} ({$a['alternatif']}): ";
        foreach ($a['nilai'] as $kode => $v) echo "{$kode}={$v} ";
        echo "\n";
    }
}
echo "</pre>";

// Hitung
$hasil = wp_hitung($kriteria, $alternatif);

echo "<h3>Hasil Perhitungan:</h3><pre>";
echo "ΣSi = {$hasil['total_si']}\n";
echo "Si R1 = " . ($hasil['si']['R1'] ?? 'N/A') . "\n";
echo "Si R2 = " . ($hasil['si']['R2'] ?? 'N/A') . "\n";
echo "Vi R1 = " . ($hasil['vi']['R1'] ?? 'N/A') . "\n";
echo "Vi R2 = " . ($hasil['vi']['R2'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h3>Perhitungan Manual R1 (langkah demi langkah):</h3><pre>";
$r1 = null;
foreach ($alternatif as $a) if ($a['code'] === 'R1') $r1 = $a;
if ($r1) {
    $s = 1.0;
    foreach ($kriteria as $k) {
        $kode = $k['code'];
        $nilai = $r1['nilai'][$kode] ?? 0;
        $w = $hasil['wj_bertanda'][$kode];
        $pow = pow($nilai, $w);
        $s *= $pow;
        printf("%s: %d ^ %+.12f = %.12f  (akumulasi: %.12f)\n", $kode, $nilai, $w, $pow, $s);
    }
    echo "\nSi R1 manual = {$s}\n";
}
echo "</pre>";