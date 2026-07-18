<?php

namespace app\aura\utils;

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {

    /**
     * Send email using PHPMailer (SMTP)
     *
     * @param array $post_data Form data
     * @return bool $result Sending result
     */
    public function sendMail(array $post_data) {

        // Default result = false
        $result = false;

        // Set multibyte language and encoding
        mb_language($_ENV['MB_LANGUAGE']);
        mb_internal_encoding($_ENV['MB_INTERNAL_ENCODING']);

        // Get recipient email
        $to = $post_data['mail'] ?? '';

        // Validate email address
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            return false;
        }

        // Get user inputs
        $username = $post_data['username'] ?? 'no-name';
        $message  = $post_data['comment'] ?? '';

        try {
            // Create PHPMailer instance
            $mail = new PHPMailer(true);

            // =========================
            // SMTP Configuration
            // =========================
            $mail->isSMTP(); // Use SMTP

            $mail->Host = $_ENV['SMTP_HOST']; // SMTP server
            $mail->Port = $_ENV['SMTP_PORT']; // SMTP port

            // Disable authentication for smtp4dev
            $mail->SMTPAuth = $_ENV['SMTP_AUTH'];

            // Disable encryption for local testing
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];

            // Charset
            $mail->CharSet = 'UTF-8';

            // =========================
            // Sender & Recipient
            // =========================
            // Sender (must be your domain in production)
            $mail->setFrom($_ENV['MAIL_FROM'], 'GhostPHP');

            // Recipien
            $mail->addAddress($to);

            // Reply-To (user email) 
            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($to, $username);
            }

            // =========================
            // Mail Content
            // =========================
            // Subject
            $mail->Subject = 'Letter from ' . $username;

            // Body (plain text)
            $mail->Body = $message;

            // Send email
            $mail->send();

            // Success
            $result = true;
            echo "Email successfully sent.";

        } catch (Exception $e) {
            // Error handling
            echo "Mailer Error: " . $mail->ErrorInfo;
        }

        return $result;
    }
}