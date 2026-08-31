<?php
declare(strict_types=1);

const HEXSA_INQUIRY_RECIPIENT = 'marketing.hexsa@gmail.com';
const HEXSA_INQUIRY_SENDER = 'website@hexsa.in';
const HEXSA_SMTP_HELO = 'ec2-52-66-33-126.ap-south-1.compute.amazonaws.com';
const HEXSA_MAIL_LOG = '/home/forge/hexsa.in/mail-delivery.log';

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

function logMailFailure(string $message): void
{
    $line = sprintf("[%s] %s\n", date(DATE_ATOM), $message);
    error_log(trim($line));
    @error_log($line, 3, HEXSA_MAIL_LOG);
}

/** @return array{0:int,1:string} */
function readSmtpResponse($socket): array
{
    $response = '';
    $code = 0;

    while (($line = fgets($socket, 2048)) !== false) {
        $response .= $line;
        if (strlen($line) >= 3 && ctype_digit(substr($line, 0, 3))) {
            $code = (int) substr($line, 0, 3);
        }
        if (strlen($line) < 4 || $line[3] !== '-') {
            break;
        }
    }

    return [$code, trim($response)];
}

/** @return array{0:int,1:string} */
function smtpCommand($socket, string $command): array
{
    if (fwrite($socket, $command . "\r\n") === false) {
        return [0, 'Unable to write to the SMTP connection.'];
    }

    return readSmtpResponse($socket);
}

function buildRawEmail(string $recipient, string $subject, string $html, string $replyToName, string $replyToEmail): string
{
    $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
    $messageId = '<' . bin2hex(random_bytes(16)) . '@hexsa.in>';
    $safeReplyToName = preg_replace('/[\r\n]+/', ' ', $replyToName) ?? 'Website visitor';

    return implode("\r\n", [
        'Date: ' . date(DATE_RFC2822),
        'Message-ID: ' . $messageId,
        'From: Hexsa Website <' . HEXSA_INQUIRY_SENDER . '>',
        'To: ' . $recipient,
        'Reply-To: ' . $safeReplyToName . ' <' . $replyToEmail . '>',
        'Subject: ' . $encodedSubject,
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
        $html,
        '',
    ]);
}

function sendViaDirectSmtp(string $recipient, string $subject, string $html, string $replyToName, string $replyToEmail): bool
{
    $recipientParts = explode('@', $recipient);
    $recipientDomain = strtolower((string) end($recipientParts));
    $mxHosts = [];
    $mxWeights = [];

    if ($recipientDomain === '' || !getmxrr($recipientDomain, $mxHosts, $mxWeights) || $mxHosts === []) {
        logMailFailure('No MX records were found for the inquiry recipient domain.');
        return false;
    }

    array_multisort($mxWeights, SORT_ASC, SORT_NUMERIC, $mxHosts, SORT_ASC, SORT_STRING);
    $rawMessage = buildRawEmail($recipient, $subject, $html, $replyToName, $replyToEmail);
    $normalizedMessage = preg_replace("/\r\n|\r|\n/", "\r\n", $rawMessage) ?? $rawMessage;
    $dotStuffedMessage = preg_replace('/^\./m', '..', $normalizedMessage) ?? $normalizedMessage;
    $failures = [];

    foreach (array_slice($mxHosts, 0, 3) as $mxHost) {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'peer_name' => $mxHost,
                'SNI_enabled' => true,
            ],
        ]);
        $errorNumber = 0;
        $errorMessage = '';
        $socket = @stream_socket_client(
            'tcp://' . $mxHost . ':25',
            $errorNumber,
            $errorMessage,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ($socket === false) {
            $failures[] = $mxHost . ': connection failed (' . $errorNumber . ').';
            continue;
        }

        stream_set_timeout($socket, 12);
        [$code, $response] = readSmtpResponse($socket);
        if ($code !== 220) {
            $failures[] = $mxHost . ': invalid greeting (' . $code . ').';
            fclose($socket);
            continue;
        }

        [$code, $response] = smtpCommand($socket, 'EHLO ' . HEXSA_SMTP_HELO);
        if ($code !== 250 || stripos($response, 'STARTTLS') === false) {
            $failures[] = $mxHost . ': STARTTLS is unavailable (' . $code . ').';
            fclose($socket);
            continue;
        }

        [$code] = smtpCommand($socket, 'STARTTLS');
        if ($code !== 220 || !stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $failures[] = $mxHost . ': TLS negotiation failed.';
            fclose($socket);
            continue;
        }

        [$code] = smtpCommand($socket, 'EHLO ' . HEXSA_SMTP_HELO);
        if ($code !== 250) {
            $failures[] = $mxHost . ': EHLO after TLS failed (' . $code . ').';
            fclose($socket);
            continue;
        }

        [$code, $response] = smtpCommand($socket, 'MAIL FROM:<' . HEXSA_INQUIRY_SENDER . '>');
        if ($code !== 250) {
            $failures[] = $mxHost . ': sender rejected (' . $code . ' ' . $response . ').';
            fclose($socket);
            continue;
        }

        [$code, $response] = smtpCommand($socket, 'RCPT TO:<' . $recipient . '>');
        if (!in_array($code, [250, 251], true)) {
            $failures[] = $mxHost . ': recipient rejected (' . $code . ' ' . $response . ').';
            fclose($socket);
            continue;
        }

        [$code] = smtpCommand($socket, 'DATA');
        if ($code !== 354) {
            $failures[] = $mxHost . ': DATA command rejected (' . $code . ').';
            fclose($socket);
            continue;
        }

        if (fwrite($socket, $dotStuffedMessage . "\r\n.\r\n") === false) {
            $failures[] = $mxHost . ': message body write failed.';
            fclose($socket);
            continue;
        }

        [$code, $response] = readSmtpResponse($socket);
        smtpCommand($socket, 'QUIT');
        fclose($socket);

        if ($code === 250) {
            return true;
        }

        $failures[] = $mxHost . ': message rejected (' . $code . ' ' . $response . ').';
    }

    logMailFailure('Direct SMTP delivery failed. ' . implode(' ', $failures));
    return false;
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

$sent = sendViaDirectSmtp(
    HEXSA_INQUIRY_RECIPIENT,
    $subject,
    $message,
    $name,
    $email
);

if (!$sent) {
    redirectToForm('error', $form);
}

$_SESSION['hexsa_last_inquiry'] = $now;
redirectToForm('sent', $form);
