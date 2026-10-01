<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/mail-client.php';
$log = tempnam(sys_get_temp_dir(), 'hexsa-mail-test-');
ini_set('error_log', $log);
$config = ['api_key' => 'private-test-key', 'sender_email' => 'support@qgol-ai.in'];
$calls = 0;
$send = function (array $settings, callable $request) use (&$calls): bool {
    return hexsaSendInquiry($settings, 'marketing.hexsa@gmail.com', 'Test enquiry', '<p>Escaped &amp; content</p>', 'Visitor', 'visitor@example.com', function ($key, $body) use (&$calls, $request) {
        $calls++;
        $payload = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        if ($key !== 'private-test-key' || $payload['sender']['email'] !== 'support@qgol-ai.in' || $payload['to'][0]['email'] !== 'marketing.hexsa@gmail.com' || $payload['replyTo']['email'] !== 'visitor@example.com' || $payload['htmlContent'] !== '<p>Escaped &amp; content</p>') {
            throw new RuntimeException('Payload identity or content changed.');
        }
        return $request();
    });
};
function ensure(bool $value, string $message): void { if (!$value) { throw new RuntimeException($message); } }
try {
    ensure($send($config, fn() => ['status' => 201, 'body' => '{"messageId":"accepted"}']), 'Accepted mail must succeed.');
    foreach ([['status' => 201, 'body' => '{}'], ['status' => 200, 'body' => '{"messageId":"accepted"}'], ['status' => 429, 'body' => '{"error":"private-test-key visitor@example.com"}'], ['status' => 0, 'body' => '']] as $response) {
        ensure(!$send($config, fn() => $response), 'Rejected or ambiguous responses must fail.');
    }
    ensure(!$send($config, fn() => throw new RuntimeException('private-test-key visitor@example.com')), 'Transport errors must fail safely.');
    $before = $calls;
    ensure(!$send([], fn() => throw new RuntimeException('Must not send')), 'Missing credentials must fail.');
    ensure($calls === $before, 'Missing credentials must prevent network requests.');
    $logged = file_get_contents($log);
    ensure(!str_contains($logged, 'private-test-key') && !str_contains($logged, 'visitor@example.com'), 'Private data must not be logged.');
    echo "Hexsa mail acceptance, failure, configuration and redaction checks passed.\n";
} finally { unlink($log); }
