<?php
declare(strict_types=1);
require __DIR__ . '/inquiry-protection.php';
hexsaStartSession();
header('X-Content-Type-Options: nosniff');

// Refresh creates a new challenge without clearing the visitor's form fields.
if (isset($_GET['form'])) {
    $form = $_GET['form'];
    if (!is_string($form) || !in_array($form, ['quote', 'contact'], true)) {
        http_response_code(400);
        exit;
    }
    $id = hexsaCreateCaptcha($form);
    header('Content-Type: application/json');
    echo json_encode(['id' => $id, 'image' => 'captcha.php?id=' . $id]);
    exit;
}

$id = $_GET['id'] ?? '';
$challenge = is_string($id) ? ($_SESSION['hexsa_captchas'][$id] ?? null) : null;
if (!is_array($challenge) || time() - $challenge['created_at'] > 600) {
    http_response_code(404);
    exit;
}
$answer = $challenge['answer'];
session_write_close();

$image = imagecreatetruecolor(220, 64);
$background = imagecolorallocate($image, 242, 247, 242);
imagefill($image, 0, 0, $background);
$noise = imagecolorallocate($image, 176, 198, 177);
for ($i = 0; $i < 8; $i++) {
    imageline($image, random_int(0, 219), random_int(0, 63), random_int(0, 219), random_int(0, 63), $noise);
}
for ($i = 0; $i < 140; $i++) {
    imagesetpixel($image, random_int(0, 219), random_int(0, 63), $noise);
}

// Render letters as pixels; the answer is never returned as text or HTML.
for ($i = 0; $i < strlen($answer); $i++) {
    $glyph = imagecreatetruecolor(18, 24);
    imagealphablending($glyph, false);
    $transparent = imagecolorallocatealpha($glyph, 0, 0, 0, 127);
    imagefill($glyph, 0, 0, $transparent);
    imagesavealpha($glyph, true);
    $ink = imagecolorallocate($glyph, 27, 94, 32);
    imagechar($glyph, 5, 4, 3, $answer[$i], $ink);
    $rotated = imagerotate($glyph, random_int(-12, 12), $transparent);
    imagecopyresampled($image, $rotated, 10 + $i * 40, random_int(7, 12), 0, 0, 36, 46, imagesx($rotated), imagesy($rotated));
    imagedestroy($rotated);
    imagedestroy($glyph);
}

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
