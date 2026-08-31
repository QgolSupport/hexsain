<?php
declare(strict_types=1);

const HEXSA_INQUIRY_RECIPIENT = 'marketing.hexsa@gmail.com';
const HEXSA_INQUIRY_SENDER = 'website@hexsa.in';

function redirectToForm(string $status, string $form): never
{
    $anchor = $form === 'quote' ? 'rfq' : 'contact';
    header('Location: https://hexsa.in/?inquiry=' . rawurlencode($status) . '&form=' . rawurlencode($form) . '#' . $anchor);
    exit;
}

function cleanText(mixed $value, int $limit = 500): string
{
    $text = trim((string) $value);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';

    return mb_substr($text, 0, $limit);
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: https://hexsa.in');
    exit;
}

$isQuote = array_key_exists('company', $_POST);
$form = $isQuote ? 'quote' : 'contact';

// Keep automated submissions out without relying on an external CAPTCHA service.
if (!empty($_POST['website'])) {
    redirectToForm('sent', $form);
}

session_start();
$now = time();
$lastSubmission = (int) ($_SESSION['hexsa_last_inquiry'] ?? 0);
if ($lastSubmission > 0 && ($now - $lastSubmission) < 15) {
    redirectToForm('rate-limited', $form);
}

$company = cleanText($_POST['company'] ?? '', 150);
$name = cleanText($_POST['name'] ?? '', 120);
$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_SANITIZE_EMAIL);
$phone = cleanText($_POST['phone'] ?? '', 40);
$product = cleanText($_POST['product'] ?? '', 100);
$quantity = cleanText($_POST['quantity'] ?? '', 100);
$urgency = cleanText($_POST['urgency'] ?? '', 100);
$specs = cleanText($_POST['specs'] ?? '', 3000);
$customerMessage = cleanText($_POST['message'] ?? '', 3000);

$validEmail = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
$validCommonFields = mb_strlen($name) >= 2 && $validEmail && mb_strlen($phone) >= 7;
$validQuote = !$isQuote || ($company !== '' && $product !== '' && $quantity !== '');
$validContact = $isQuote || mb_strlen($customerMessage) >= 10;

if (!$validCommonFields || !$validQuote || !$validContact) {
    redirectToForm('invalid', $form);
}

$safeName = escapeHtml($name);
$safeEmail = escapeHtml($email);
$safePhone = escapeHtml($phone);

if ($isQuote) {
    $safeCompany = escapeHtml($company);
    $safeProduct = escapeHtml($product);
    $safeQuantity = escapeHtml($quantity);
    $safeUrgency = escapeHtml($urgency);
    $safeSpecs = nl2br(escapeHtml($specs));
    $subjectCompany = preg_replace('/[\r\n]+/', ' ', $company) ?? 'Website inquiry';
    $subject = 'New RFQ from ' . $subjectCompany;
    $message = <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Quote Request</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#222">
    <h2 style="color:#2E7D32">New Quote Request</h2>
    <p><strong>Company:</strong> {$safeCompany}</p>
    <p><strong>Contact person:</strong> {$safeName}</p>
    <p><strong>Email:</strong> {$safeEmail}</p>
    <p><strong>Phone:</strong> {$safePhone}</p>
    <p><strong>Product interest:</strong> {$safeProduct}</p>
    <p><strong>Estimated quantity:</strong> {$safeQuantity}</p>
    <p><strong>Urgency:</strong> {$safeUrgency}</p>
    <p><strong>Special requirements:</strong><br>{$safeSpecs}</p>
    <p style="margin-top:30px;color:#666">Submitted from the Hexsa website.</p>
</body>
</html>
HTML;
} else {
    $safeCustomerMessage = nl2br(escapeHtml($customerMessage));
    $subject = 'New Product Enquiry';
    $message = <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Product Enquiry</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#222">
    <h2 style="color:#2E7D32">New Product Enquiry</h2>
    <p><strong>Contact person:</strong> {$safeName}</p>
    <p><strong>Email:</strong> {$safeEmail}</p>
    <p><strong>Phone:</strong> {$safePhone}</p>
    <p><strong>Message:</strong><br>{$safeCustomerMessage}</p>
    <p style="margin-top:30px;color:#666">Submitted from the Hexsa website.</p>
</body>
</html>
HTML;
}

$replyToName = preg_replace('/[\r\n]+/', ' ', $name) ?? 'Website visitor';
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: Hexsa Website <' . HEXSA_INQUIRY_SENDER . '>',
    'Reply-To: ' . $replyToName . ' <' . $email . '>',
    'X-Mailer: PHP/' . PHP_VERSION,
];

$sent = mail(
    HEXSA_INQUIRY_RECIPIENT,
    $subject,
    $message,
    implode("\r\n", $headers),
    '-f' . HEXSA_INQUIRY_SENDER
);

if (!$sent) {
    error_log('Hexsa inquiry mail was rejected by the local mail transport.');
    redirectToForm('error', $form);
}

$_SESSION['hexsa_last_inquiry'] = $now;
redirectToForm('sent', $form);
