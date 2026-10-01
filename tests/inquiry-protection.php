<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/inquiry-protection.php';

function inquiryEnsure(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function inquiryResetSession(): void
{
    $_SESSION = [];
    $_GET = [];
    $_POST = [];
}

function inquiryChallengeAnswer(string $id): string
{
    inquiryEnsure(isset($_SESSION['hexsa_captchas'][$id]), 'Challenge must exist in the server session.');

    return (string) $_SESSION['hexsa_captchas'][$id]['answer'];
}

hexsaStartSession();
$originalSession = $_SESSION;
$originalGet = $_GET;
$originalPost = $_POST;
$now = 1700000000;

try {
    inquiryResetSession();
    inquiryEnsure(!hexsaValidateCaptcha('quote', null, null, $now), 'Missing CAPTCHA must fail.');
    inquiryEnsure(!hexsaValidateCaptcha('quote', 'unknown', 'ABCDE', $now), 'Unknown challenge must fail.');
    inquiryEnsure(!hexsaValidateCaptcha('quote', [], [], $now), 'Non-scalar CAPTCHA fields must fail safely.');

    $quoteId = hexsaCreateCaptcha('quote', $now);
    $quoteAnswer = inquiryChallengeAnswer($quoteId);
    inquiryEnsure(strlen($quoteAnswer) === 5, 'CAPTCHA answer must have five characters.');
    inquiryEnsure(!hexsaValidateCaptcha('contact', $quoteId, $quoteAnswer, $now), 'A quote CAPTCHA must not authorize contact submissions.');
    inquiryEnsure(isset($_SESSION['hexsa_captchas'][$quoteId]), 'Wrong form must not consume the original challenge.');
    inquiryEnsure(hexsaValidateCaptcha('quote', $quoteId, $quoteAnswer, $now), 'Valid CAPTCHA must pass for its form.');
    inquiryEnsure(!hexsaValidateCaptcha('quote', $quoteId, $quoteAnswer, $now), 'Consumed CAPTCHA must not replay.');

    $wrongId = hexsaCreateCaptcha('contact', $now);
    $wrongAnswer = inquiryChallengeAnswer($wrongId);
    inquiryEnsure(!hexsaValidateCaptcha('contact', $wrongId, 'incorrect', $now), 'Wrong answer must fail.');
    inquiryEnsure(!hexsaValidateCaptcha('contact', $wrongId, $wrongAnswer, $now), 'A failed matching-form attempt must consume the challenge.');

    $emptyId = hexsaCreateCaptcha('quote', $now);
    $emptyAnswer = inquiryChallengeAnswer($emptyId);
    inquiryEnsure(!hexsaValidateCaptcha('quote', $emptyId, '', $now), 'Empty answer must fail.');
    inquiryEnsure(!hexsaValidateCaptcha('quote', $emptyId, $emptyAnswer, $now), 'An empty matching-form attempt must also consume the challenge.');

    $normalizedId = hexsaCreateCaptcha('quote', $now);
    $normalizedAnswer = inquiryChallengeAnswer($normalizedId);
    inquiryEnsure(hexsaValidateCaptcha('quote', $normalizedId, '  ' . strtolower($normalizedAnswer) . '  ', $now), 'Answers must ignore case and surrounding whitespace.');

    $expiredId = hexsaCreateCaptcha('contact', $now);
    $expiredAnswer = inquiryChallengeAnswer($expiredId);
    inquiryEnsure(!hexsaValidateCaptcha('contact', $expiredId, $expiredAnswer, $now + 601), 'Expired CAPTCHA must fail.');

    inquiryResetSession();
    $otherSessionId = hexsaCreateCaptcha('quote', $now);
    $otherSessionAnswer = inquiryChallengeAnswer($otherSessionId);
    $visitorSession = $_SESSION;
    inquiryResetSession();
    inquiryEnsure(!hexsaValidateCaptcha('quote', $otherSessionId, $otherSessionAnswer, $now), 'A CAPTCHA from another session must fail.');
    $_SESSION = $visitorSession;
    inquiryEnsure(hexsaValidateCaptcha('quote', $otherSessionId, $otherSessionAnswer, $now), 'Rejected cross-session attempt must not affect the original visitor.');

    inquiryResetSession();
    $firstTabId = hexsaCreateCaptcha('quote', $now);
    $firstTabAnswer = inquiryChallengeAnswer($firstTabId);
    $secondTabId = hexsaCreateCaptcha('quote', $now + 1);
    $secondTabAnswer = inquiryChallengeAnswer($secondTabId);
    $contactId = hexsaCreateCaptcha('contact', $now + 2);
    $contactAnswer = inquiryChallengeAnswer($contactId);
    inquiryEnsure($firstTabId !== $secondTabId && $secondTabId !== $contactId, 'Every form render must get an independent challenge ID.');
    inquiryEnsure(hexsaValidateCaptcha('quote', $firstTabId, $firstTabAnswer, $now + 3), 'Opening another tab must preserve the first tab CAPTCHA.');
    inquiryEnsure(hexsaValidateCaptcha('contact', $contactId, $contactAnswer, $now + 3), 'Quote submission must not consume contact CAPTCHA.');
    inquiryEnsure(hexsaValidateCaptcha('quote', $secondTabId, $secondTabAnswer, $now + 3), 'Submitting the first tab must preserve the second tab CAPTCHA.');

    inquiryResetSession();
    $ids = [];
    for ($index = 0; $index < 25; $index++) {
        $ids[] = hexsaCreateCaptcha('quote', $now + $index);
    }
    inquiryEnsure(count($_SESSION['hexsa_captchas']) === 20, 'CAPTCHA session storage must be bounded to 20 challenges.');
    foreach (array_slice($ids, 0, 5) as $discardedId) {
        inquiryEnsure(!isset($_SESSION['hexsa_captchas'][$discardedId]), 'Oldest challenges must be discarded when the store is full.');
    }
    foreach (array_slice($ids, 5) as $retainedId) {
        inquiryEnsure(isset($_SESSION['hexsa_captchas'][$retainedId]), 'Newest 20 challenges must remain available.');
    }
    $freshId = hexsaCreateCaptcha('contact', $now + 1000);
    inquiryEnsure(count($_SESSION['hexsa_captchas']) === 1 && isset($_SESSION['hexsa_captchas'][$freshId]), 'Creating a challenge must prune expired session entries.');

    inquiryResetSession();
    $_GET = ['inquiry' => 'sent', 'form' => 'quote'];
    inquiryEnsure(hexsaTakeInquiryFlash() === null, 'Forged success URL parameters must not create a confirmation.');
    $values = ['name' => 'Visitor', 'company' => 'Example company', 'email' => 'visitor@example.com'];
    hexsaSetInquiryFlash('invalid', 'quote', $values);
    inquiryEnsure(hexsaTakeInquiryFlash() === ['status' => 'invalid', 'form' => 'quote', 'values' => $values], 'Validation flash must preserve its form and entered values.');
    inquiryEnsure(hexsaTakeInquiryFlash() === null, 'Inquiry flash must be consumed once.');
    hexsaSetInquiryFlash('sent', 'contact');
    inquiryEnsure(hexsaTakeInquiryFlash() === ['status' => 'sent', 'form' => 'contact', 'values' => []], 'Successful confirmation must identify the submitted form.');
    inquiryEnsure(hexsaTakeInquiryFlash() === null, 'Successful confirmation must not replay from session storage.');

    echo "Hexsa CAPTCHA validation, expiry, replay, form/session isolation, tab storage and confirmation checks passed.\n";
} finally {
    $_SESSION = $originalSession;
    $_GET = $originalGet;
    $_POST = $originalPost;
}
