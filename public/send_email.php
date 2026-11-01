<?php
/**
 * Kontaktformular von baumschrat.at – nimmt multipart/form-data entgegen und versendet eine E-Mail
 * mit optionalen Foto-Anhängen.
 *
 * Schutzmaßnahmen: nur POST, Honeypot-Feld, Mindest-Ausfüllzeit, einfaches Rate-Limit pro IP,
 * keine Zeilenumbrüche in Header-Werten (verhindert Header-Injection), Prüfung der Dateitypen.
 */

declare(strict_types=1);

const RECIPIENT    = 'daniel@baumschrat.at';
const SENDER       = 'webmaster@baumschrat.at';
const MAX_FILES    = 5;
const MAX_FILE     = 8 * 1024 * 1024;   // pro Datei
const MAX_TOTAL    = 20 * 1024 * 1024;  // alle Dateien zusammen
const MIN_SECONDS  = 3;                 // schneller füllt kein Mensch das Formular aus
const RATE_LIMIT   = 5;                 // Anfragen pro IP …
const RATE_WINDOW  = 3600;              // … pro Stunde
const ALLOWED_MIME = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/heic' => 'heic',
    'image/heif' => 'heif',
];

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(bool $success, string $message, int $status = 200): void
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Einzeiliger Wert: Steuerzeichen (inkl. CR/LF) entfernen, Länge begrenzen. */
function line(string $key, int $max = 200): string
{
    $value = (string) ($_POST[$key] ?? '');
    $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
    return mb_substr(trim($value), 0, $max);
}

/** Mehrzeiliger Text: Zeilenumbrüche erlaubt, andere Steuerzeichen nicht. */
function text(string $key, int $max = 5000): string
{
    $value = str_replace("\r\n", "\n", (string) ($_POST[$key] ?? ''));
    $value = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]+/u', ' ', $value) ?? '';
    return mb_substr(trim($value), 0, $max);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(false, 'Methode nicht erlaubt.', 405);
}

// Wenn post_max_size überschritten wird, ist $_POST leer.
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    respond(false, 'Die Fotos sind zu groß. Bitte weniger oder kleinere Fotos auswählen.', 413);
}

// Honeypot und Ausfüllzeit: Bots bekommen eine Erfolgsmeldung, damit sie nicht weiter probieren.
if (line('website') !== '' || (int) ($_POST['elapsed'] ?? 0) < MIN_SECONDS) {
    respond(true, 'Danke!');
}

// Rate-Limit pro IP (gehashte IP in einer temporären Datei, keine Speicherung im Klartext).
$ip       = $_SERVER['REMOTE_ADDR'] ?? 'unbekannt';
$rateFile = sys_get_temp_dir() . '/baumschrat_rate_' . hash('sha256', $ip . __FILE__);
$now      = time();
$hits     = array_filter(
    is_file($rateFile) ? (array) json_decode((string) file_get_contents($rateFile), true) : [],
    static fn ($t) => is_int($t) && $t > $now - RATE_WINDOW
);
if (count($hits) >= RATE_LIMIT) {
    respond(false, 'Zu viele Anfragen. Bitte versuchen Sie es später erneut oder rufen Sie an.', 429);
}

$name     = line('name');
$email    = line('email');
$phone    = line('phone', 50);
$location = line('location');
$service  = line('service', 100);
$message  = text('message');

if ($name === '' || $message === '') {
    respond(false, 'Bitte Name und Beschreibung ausfüllen.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Bitte eine gültige E-Mail-Adresse angeben.', 422);
}

// Anhänge prüfen
$attachments = [];
$files       = $_FILES['photos'] ?? null;
if (is_array($files) && is_array($files['name'] ?? null)) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $total = 0;
    foreach ($files['name'] as $i => $original) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (count($attachments) >= MAX_FILES) {
            respond(false, 'Bitte höchstens ' . MAX_FILES . ' Fotos anhängen.', 422);
        }
        if ($files['error'][$i] !== UPLOAD_ERR_OK || $files['size'][$i] > MAX_FILE) {
            respond(false, 'Ein Foto ist zu groß oder konnte nicht hochgeladen werden.', 422);
        }
        $mime = $finfo->file($files['tmp_name'][$i]) ?: '';
        if (!isset(ALLOWED_MIME[$mime])) {
            respond(false, 'Es können nur Fotos (JPG, PNG, WebP, HEIC) angehängt werden.', 422);
        }
        $total += $files['size'][$i];
        if ($total > MAX_TOTAL) {
            respond(false, 'Die Fotos sind zusammen zu groß.', 422);
        }
        $attachments[] = [
            'name' => sprintf('foto-%d.%s', count($attachments) + 1, ALLOWED_MIME[$mime]),
            'mime' => $mime,
            'data' => (string) file_get_contents($files['tmp_name'][$i]),
        ];
    }
}

$body  = "Neue Anfrage über baumschrat.at\n\n";
$body .= "Name:      $name\n";
$body .= "E-Mail:    $email\n";
$body .= "Telefon:   $phone\n";
$body .= "Ort:       $location\n";
$body .= "Leistung:  $service\n";
$body .= 'Fotos:     ' . count($attachments) . "\n\n";
$body .= "Beschreibung:\n$message\n";

$subject  = '=?UTF-8?B?' . base64_encode('Anfrage über baumschrat.at: ' . $name) . '?=';
$boundary = 'bs_' . bin2hex(random_bytes(12));

$headers  = 'From: Baumschrat Website <' . SENDER . ">\r\n";
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";

$mime  = "--$boundary\r\n";
$mime .= "Content-Type: text/plain; charset=UTF-8\r\n";
$mime .= "Content-Transfer-Encoding: base64\r\n\r\n";
$mime .= chunk_split(base64_encode($body)) . "\r\n";
foreach ($attachments as $a) {
    $mime .= "--$boundary\r\n";
    $mime .= "Content-Type: {$a['mime']}; name=\"{$a['name']}\"\r\n";
    $mime .= "Content-Transfer-Encoding: base64\r\n";
    $mime .= "Content-Disposition: attachment; filename=\"{$a['name']}\"\r\n\r\n";
    $mime .= chunk_split(base64_encode($a['data'])) . "\r\n";
}
$mime .= "--$boundary--\r\n";

if (!mail(RECIPIENT, $subject, $mime, $headers)) {
    respond(false, 'Die Nachricht konnte nicht gesendet werden. Bitte rufen Sie mich an.', 500);
}

$hits[] = $now;
@file_put_contents($rateFile, json_encode(array_values($hits)), LOCK_EX);

respond(true, 'Danke für Ihre Anfrage!');
