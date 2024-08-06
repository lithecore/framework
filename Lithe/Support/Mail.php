<?php

namespace Lithe\Support;

use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected static $mailer;

    /**
     * Initializes PHPMailer with default settings.
     *
     * @throws \Exception When PHPMailer is not installed.
     */
    private static function initializeMailer()
    {
        // Check if PHPMailer class is loaded
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new \Exception('PHPMailer is not installed. Install PHPMailer to use the Mail class.');
        }

        // Initialize PHPMailer object
        self::$mailer = new \PHPMailer\PHPMailer\PHPMailer(true); // 'true' enables exceptions

        try {
            // Use SMTP for email sending
            self::$mailer->isSMTP();
            self::$mailer->Host = Env::get('MAIL_HOST'); // SMTP server
            self::$mailer->Port = Env::get('MAIL_PORT'); // SMTP server port
            self::$mailer->SMTPAuth = true; // Enable SMTP authentication
            self::$mailer->Username = Env::get('MAIL_USERNAME'); // SMTP username
            self::$mailer->Password = Env::get('MAIL_PASSWORD'); // SMTP password
            self::$mailer->SMTPSecure = Env::get('MAIL_ENCRYPTION'); // Encryption method (tls or ssl)
            self::$mailer->setFrom(Env::get('MAIL_FROM_ADDRESS'), Env::get('MAIL_FROM_NAME')); // Default sender

            // Set character encoding to UTF-8
            self::$mailer->CharSet = 'UTF-8';

            // Optional: Set other default settings (e.g., SMTP debug, timeout) if necessary
        } catch (\Exception $e) {
            Log::error('Error initializing PHPMailer: ' . $e->getMessage());
            throw new \RuntimeException('Error initializing PHPMailer: ' . $e->getMessage());
        }
    }

    /**
     * Sets the email recipient.
     *
     * @param string $to Recipient's email address.
     * @param string|null $name Recipient's name (optional).
     */
    public static function to(string $to, ?string $name = null)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addAddress($to, $name);
        return new self;
    }

    /**
     * Adds a CC recipient.
     *
     * @param string $cc CC recipient's email address.
     * @param string|null $name CC recipient's name (optional).
     */
    public function cc(string $cc, ?string $name = null)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addCC($cc, $name);
        return $this;
    }

    /**
     * Adds a BCC recipient.
     *
     * @param string $bcc BCC recipient's email address.
     * @param string|null $name BCC recipient's name (optional).
     */
    public function bcc(string $bcc, ?string $name = null)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addBCC($bcc, $name);
        return $this;
    }

    /**
     * Sets the reply-to address.
     *
     * @param string $replyTo Reply-to email address.
     * @param string|null $name Reply-to name (optional).
     */
    public function replyTo(string $replyTo, ?string $name = null)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addReplyTo($replyTo, $name);
        return $this;
    }

    /**
     * Sets the email subject.
     *
     * @param string $subject Email subject.
     */
    public function subject(string $subject)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->Subject = $subject;
        return $this;
    }

    /**
     * Sets the email body as plain text.
     *
     * @param string $body Email body in plain text.
     */
    public function text(string $body)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->isHTML(false); // Ensure plain text mode
        self::$mailer->Body = $body;
        return $this;
    }

    /**
     * Sets the email body as HTML.
     *
     * @param string $body Email body in HTML format.
     */
    public function html(string $body)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->isHTML(true); // Ensure HTML mode
        self::$mailer->Body = $body;
        return $this;
    }

    /**
     * Attaches a file to the email.
     *
     * @param string $filePath Path to the file to be attached.
     * @param string|null $fileName File name (optional).
     */
    public function attach(string $filePath, ?string $fileName = null)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addAttachment($filePath, $fileName);
        return $this;
    }

    /**
     * Adds a custom header to the email.
     *
     * @param string $header Header name.
     * @param string $value Header value.
     */
    public function addHeader(string $header, string $value)
    {
        if (!self::$mailer) {
            self::initializeMailer();
        }

        self::$mailer->addCustomHeader($header, $value);
        return $this;
    }

    /**
     * Sends the email.
     *
     * @return bool Returns true if the email was sent successfully, false otherwise.
     */
    public function send()
    {
        try {
            if (!self::$mailer) {
                self::initializeMailer();
            }

            self::$mailer->send();
            return true;
        } catch (\Exception $e) {
            // Handle email sending errors here
            Log::error($e);
            return false;
        }
    }
}
