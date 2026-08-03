<?php
require "C:/xampp/htdocs/qgol/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    // Honeypot check
    if (!empty($_POST['website'])) {
        die("Spam detected!");
    }

    // reCAPTCHA verification
    $secretKey = "6LdrKGorAAAAAGwLs3Os5Kl__dpnhbiydWuNO_Z1"; // Replace with your actual secret key
    $response = $_POST['g-recaptcha-response'] ?? '';
    $ip = $_SERVER['3.111.37.58'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$ip";
    $recaptcha = json_decode(file_get_contents($url));

    if (!$recaptcha->success) {
        die("CAPTCHA verification failed!");
    }



    // Validate and sanitize inputs
    $company = htmlspecialchars($_POST['company'] ?? 'N/A');
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $product = htmlspecialchars($_POST['product'] ?? '');
    $quantity = htmlspecialchars($_POST['quantity'] ?? '');
    $urgency = htmlspecialchars($_POST['urgency'] ?? '');
    $specs = htmlspecialchars($_POST['specs'] ?? '');
	$msg = htmlspecialchars($_POST['message'] ?? '');

    // Basic validation
    if (empty($company) || empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Required fields are missing or invalid.");
    }

    // Email content
	if($company == 'N/A')
	{
        $message = "
		<html>
		<head>
			<title>Enquiry</title>
			<style>
				body { font-family: Arial, sans-serif; line-height: 1.6; }
				.header { color: #2E7D32; font-size: 24px; margin-bottom: 20px; }
				.detail { margin-bottom: 10px; }
				.label { font-weight: bold; color: #333; }
			</style>
		</head>
		<body>			
			<div class='detail'><span class='label'>Contact Person:</span> $name</div>
			<div class='detail'><span class='label'>Email:</span> $email</div>
			<div class='detail'><span class='label'>Phone:</span> $phone</div>
			<div class='detail'><span class='label'>Message:</span> $msg</div>			
			<div style='margin-top: 30px;'>
				<p>This message was submitted from the hexsa website.</p>
			</div>
		</body>
		</html>
		";
    		
	}
	else
	{
		$message = "
		<html>
		<head>
			<title>New Quote Request</title>
			<style>
				body { font-family: Arial, sans-serif; line-height: 1.6; }
				.header { color: #2E7D32; font-size: 24px; margin-bottom: 20px; }
				.detail { margin-bottom: 10px; }
				.label { font-weight: bold; color: #333; }
			</style>
		</head>
		<body>
			<div class='header'>New Quote Request</div>
			
			<div class='detail'><span class='label'>Company:</span> $company</div>
			<div class='detail'><span class='label'>Contact Person:</span> $name</div>
			<div class='detail'><span class='label'>Email:</span> $email</div>
			<div class='detail'><span class='label'>Phone:</span> $phone</div>
			<div class='detail'><span class='label'>Product Interest:</span> $product</div>
			<div class='detail'><span class='label'>Estimated Quantity:</span> $quantity</div>
			<div class='detail'><span class='label'>Urgency:</span> $urgency</div>
			<div class='detail'><span class='label'>Special Requirements:</span><br>$specs</div>
			
			<div style='margin-top: 30px;'>
				<p>This request was submitted from the hexsa website.</p>
			</div>
		</body>
		</html>
		";
    }
    try {
        // Configure PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = 'support@qgol.in'; // Your Gmail
        $mail->Password = 'iyubnktonitmegxu'; // Use app password if 2FA is enabled

        // Recipients
        $mail->setFrom('marketing.hexsa@gmail.com', 'HexsaMarketing');
        //$mail->addAddress('sathish@qgol.in'); // Recipient
		$mail->addAddress('marketing.hexsa@gmail.com'); // Recipient
        $mail->addReplyTo($email, $name); // Customer's email for replies

        // Content
        $mail->isHTML(true);
		if($company == 'N/A')
        {
           $mail->Subject = "New Product Enquiry";
        }			
        else $mail->Subject = "New RFQ from $company";
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message); // Plain-text fallback

        $mail->send();
        header("Location: https://hexsa.in");
        exit();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        die("Error: Unable to send email. Please try again later.");
    }
} else {
    header("Location: https://hexsa.in");
    exit();
}
?>