<?php
declare(strict_types=1);

/** Mail credentials stay outside the publicly served release directory. */
function hexsaMailConfiguration(): array
{
    $path = '/home/forge/hexsa.in/mail-config.json';
    if (!is_readable($path)) {
        return [];
    }

    $data = json_decode((string) file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function hexsaBrevoRequest(string $apiKey, string $body): array
{
    $curl = curl_init('https://api.brevo.com/v3/smtp/email');
    if ($curl === false) {
        return ['status' => 0, 'body' => ''];
    }

    try {
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['api-key: ' . $apiKey, 'Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        ]);
        $response = curl_exec($curl);
        return ['status' => (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE), 'body' => is_string($response) ? $response : ''];
    } finally {
        curl_close($curl);
    }
}

function hexsaSendInquiry(array $config, string $recipient, string $subject, string $html, string $replyName, string $replyEmail, ?callable $request = null): bool
{
    $apiKey = (string) ($config['api_key'] ?? '');
    $sender = (string) ($config['sender_email'] ?? 'support@qgol-ai.in');
    if ($apiKey === '' || !filter_var($sender, FILTER_VALIDATE_EMAIL) || !filter_var($recipient, FILTER_VALIDATE_EMAIL) || !filter_var($replyEmail, FILTER_VALIDATE_EMAIL)) {
        error_log('Hexsa inquiry delivery unavailable: mail configuration or address is invalid.');
        return false;
    }

    try {
        $body = json_encode([
            'sender' => ['email' => $sender, 'name' => 'Hexsa Website'],
            'to' => [['email' => $recipient]],
            'replyTo' => ['email' => $replyEmail, 'name' => $replyName],
            'subject' => $subject,
            'htmlContent' => $html,
            'tags' => ['hexsa-website-inquiry'],
        ], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        $result = ($request ?? 'hexsaBrevoRequest')($apiKey, $body);
        $response = json_decode((string) ($result['body'] ?? ''), true);
        if ((int) ($result['status'] ?? 0) === 201 && is_array($response) && is_string($response['messageId'] ?? null) && $response['messageId'] !== '') {
            return true;
        }
    } catch (Throwable) {
        // Provider responses and exceptions may contain credentials or visitor data.
    }

    error_log('Hexsa inquiry delivery failed; no provider response or visitor data logged.');
    return false;
}
