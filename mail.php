<?php
// mail.php - centralized email sending component using PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

/**
 * Send an email using configured SMTP settings from config.json
 *
 * @param string $to
 * @param string $subject
 * @param string $body HTML body
 * @param array|null $config if null, the function will try to read config.json
 * @return bool true on success, false on failure
 */
function send_email($to, $subject, $body, $config = null) {
    if ($config === null) {
        $configPath = __DIR__ . '/config.json';
        if (!file_exists($configPath)) return false;
        $config = json_decode(file_get_contents($configPath), true);
    }

    $smtp_settings = $config['smtp_settings'] ?? [];
    $admin_email = $smtp_settings['admin_email'] ?? '';
    $app_password = $smtp_settings['app_password'] ?? '';
    if (empty($admin_email) || empty($app_password)) return false;

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $smtp_settings['host'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $admin_email;
        $mail->Password = $app_password;
        $mail->SMTPSecure = $smtp_settings['encryption'] ?? PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $smtp_settings['port'] ?? 465;
        $mail->setFrom($admin_email, $smtp_settings['from_name'] ?? 'Submonth');
        $mail->addAddress($to);
        if (!empty($smtp_settings['reply_to'])) { $mail->addReplyTo($smtp_settings['reply_to']); }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Could log $e->getMessage() to a file or error log for debugging
        error_log('Mail send error: ' . $e->getMessage());
        return false;
    }
}