<?php
/** Helper umum. */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function fmt(float $n, int $dec = 6): string
{
    return number_format($n, $dec, '.', '');
}

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function flash(string $key, ?string $msg = null)
{
    if ($msg === null) {
        $v = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $v;
    }
    $_SESSION['flash'][$key] = $msg;
}