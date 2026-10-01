<?php
declare(strict_types=1);

function hexsaStartSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
            'path' => '/',
        ]);
        session_start();
    }

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function hexsaCreateCaptcha(string $form, ?int $now = null): string
{
    $now ??= time();
    $challenges = $_SESSION['hexsa_captchas'] ?? [];
    foreach ($challenges as $id => $challenge) {
        if ($now - $challenge['created_at'] > 600) {
            unset($challenges[$id]);
        }
    }
    while (count($challenges) >= 20) {
        unset($challenges[array_key_first($challenges)]);
    }

    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $answer = '';
    for ($i = 0; $i < 5; $i++) {
        $answer .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    $id = bin2hex(random_bytes(16));
    $challenges[$id] = ['form' => $form, 'answer' => $answer, 'created_at' => $now];
    $_SESSION['hexsa_captchas'] = $challenges;

    return $id;
}

function hexsaValidateCaptcha(string $form, mixed $id, mixed $answer, ?int $now = null): bool
{
    if (!is_string($id) || !is_string($answer)) {
        return false;
    }
    $challenge = $_SESSION['hexsa_captchas'][$id] ?? null;
    if (!is_array($challenge) || $challenge['form'] !== $form) {
        return false;
    }
    unset($_SESSION['hexsa_captchas'][$id]);
    $age = ($now ?? time()) - $challenge['created_at'];

    return $age >= 0 && $age <= 600
        && hash_equals($challenge['answer'], strtoupper(trim($answer)));
}

function hexsaSetInquiryFlash(string $status, string $form, array $values = []): void
{
    $_SESSION['hexsa_inquiry_flash'] = ['status' => $status, 'form' => $form, 'values' => $values];
}

function hexsaTakeInquiryFlash(): ?array
{
    $flash = $_SESSION['hexsa_inquiry_flash'] ?? null;
    unset($_SESSION['hexsa_inquiry_flash']);

    return is_array($flash) ? $flash : null;
}

function hexsaInquiryMessage(?array $flash, string $form): string
{
    if (($flash['form'] ?? null) !== $form) {
        return '';
    }

    return match ($flash['status']) {
        'sent' => $form === 'quote'
            ? 'Thank you! Your quote request has been submitted successfully. Our team will get in touch with you.'
            : 'Thank you! Your message has been submitted successfully. Our team will get in touch with you.',
        'captcha' => 'The security code was incorrect or expired. Please enter the new code below and try again.',
        'invalid' => 'Please check the required details and submit the form again.',
        'rate-limited' => 'Please wait 15 seconds before sending another inquiry.',
        'error' => 'We could not send your inquiry right now. Your details are still here; please try again shortly.',
        default => '',
    };
}
