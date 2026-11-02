<?php
/**
 * Logika Weighted Product (WP).
 * Semua fungsi di sini TIDAK menyentuh HTML.
 */

/**
 * Ambil daftar kriteria terurut berdasarkan kode (C1, C2, ...).
 * @return array<int, array{id:int, code:string, kriteria:string, jenis:string, bobot:int}>
 */
function wp_get_kriteria(): array
{
    $sql = "SELECT id, code, kriteria, jenis, bobot
            FROM kriteria
            ORDER BY CAST(SUBSTRING(code, 2) AS UNSIGNED), code";
    return db()->query($sql)->fetchAll();
}

/**
 * Ambil daftar alternatif + nilai matriks keputusan (c1..c8).
 * @return array<int, array{id_alter:int, code:string, alternatif:string, nilai:array<string,int>}>
 */
function wp_get_alternatif(): array
{
    $sql = "SELECT a.id_alter, a.code, a.alternatif,
                   b.c1, b.c2, b.c3, b.c4, b.c5, b.c6, b.c7, b.c8
            FROM alternatif a
            JOIN bobot b ON b.id_alter = a.id_alter
            ORDER BY CAST(SUBSTRING(a.code, 2) AS UNSIGNED), a.code";
    $rows = db()->query($sql)->fetchAll();

    $result = [];
    foreach ($rows as $r) {
        $nilai = [];
        for ($i = 1; $i <= 8; $i++) {
            $nilai['C' . $i] = (float) $r['c' . $i];
        }
        $result[] = [
            'id_alter'   => (int) $r['id_alter'],
            'code'       => $r['code'],
            'alternatif' => $r['alternatif'],
            'nilai'      => $nilai,
        ];
    }
    return $result;
}

/**
 * Hitung WP lengkap.
 *
 * @param array $kriteria   hasil wp_get_kriteria()
 * @param array $alternatif hasil wp_get_alternatif()
 * @return array{
 *   total_bobot: float,
 *   wj: array<string,float>,
 *   wj_bertanda: array<string,float>,
 *   si: array<string,float>,
 *   total_si: float,
 *   vi: array<string,float>,
 *   ranking: array<int, array{peringkat:int, code:string, alternatif:string, vi:float}>
 * }
 */
function wp_hitung(array $kriteria, array $alternatif): array
{
    // --- Validasi dasar ---
    if (empty($kriteria))   throw new RuntimeException('Data kriteria kosong.');
    if (empty($alternatif)) throw new RuntimeException('Data alternatif kosong.');

    // --- 1. Total bobot & normalisasi (Wj) ---
    $totalBobot = 0.0;
    foreach ($kriteria as $k) {
        $totalBobot += (float) $k['bobot'];
    }
    if ($totalBobot <= 0) {
        throw new RuntimeException('Total bobot kriteria harus > 0.');
    }

    $wj        = [];  // bobot ternormalisasi (selalu positif)
    $wjTanda   = [];  // bobot bertanda (cost = negatif)
    foreach ($kriteria as $k) {
        $kode = $k['code'];
        $w    = (float) $k['bobot'] / $totalBobot;
        $wj[$kode]      = $w;
        $wjTanda[$kode] = ($k['jenis'] === 'cost') ? -$w : $w;
    }

    // --- 2. Vektor S (Si) ---
    $si = [];
    foreach ($alternatif as $a) {
        $s = 1.0;
        foreach ($kriteria as $k) {
            $kode   = $k['code'];
            $nilai  = $a['nilai'][$kode] ?? 0;
            // jika nilai 0 dan pangkat negatif → tidak terdefinisi; beri epsilon
            if ($nilai <= 0) {
                $nilai = 0.0001;
            }
            $s *= pow($nilai, $wjTanda[$kode]);
        }
        $si[$a['code']] = $s;
    }

    // --- 3. Vektor V (Vi) ---
    $totalSi = array_sum($si);
    if ($totalSi <= 0) {
        throw new RuntimeException('Total Si = 0, perhitungan tidak valid.');
    }
    $vi = [];
    foreach ($si as $kode => $s) {
        $vi[$kode] = $s / $totalSi;
    }

    // --- 4. Ranking (arsort aman untuk nilai kembar) ---
    arsort($vi, SORT_NUMERIC);

    // Petakan code → nama alternatif
    $namaByCode = [];
    foreach ($alternatif as $a) {
        $namaByCode[$a['code']] = $a['alternatif'];
    }

    $ranking = [];
    $peringkat = 1;
    foreach ($vi as $kode => $nilai) {
        $ranking[] = [
            'peringkat'   => $peringkat++,
            'code'        => $kode,
            'alternatif'  => $namaByCode[$kode] ?? $kode,
            'vi'          => $nilai,
        ];
    }

    return [
        'total_bobot' => $totalBobot,
        'wj'          => $wj,
        'wj_bertanda' => $wjTanda,
        'si'          => $si,
        'total_si'    => $totalSi,
        'vi'          => $vi,
        'ranking'     => $ranking,
    ];
}