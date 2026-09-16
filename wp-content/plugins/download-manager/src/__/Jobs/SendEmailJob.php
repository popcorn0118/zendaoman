<?php
/**
 * Send Email Job
 *
 * Handles sending emails asynchronously.
 *
 * @package WPDM\__\Jobs
 * @since 7.0.1
 */

namespace WPDM\__\Jobs;

use WPDM\__\Email;

class SendEmailJob extends Job
{
    /**
     * Execute the send email job
     *
     * @param object|array $data Job payload containing:
     *   - to: Email recipient(s)
     *   - subject: Email subject
     *   - message: Email body (HTML or plain text)
     *   - template: (optional) Email template name
     *   - headers: (optional) Additional headers
     *   - attachments: (optional) Array of file paths
     * @return bool
     */
    public function handle($data): bool
    {
        $to = $this->get('to');
        $subject = $this->get('subject');
        $message = $this->get('message');

        if (empty($to) || empty($subject)) {
            $this->log('Missing required email fields (to or subject)', 'error');
            return false;
        }

        $this->log("Sending email to {$to}: {$subject}");

        // Check if using WPDM Email class with template
        $template = $this->get('template');
        if ($template && class_exists('\WPDM\__\Email')) {
            return $this->sendWithTemplate($template);
        }

        // Standard wp_mail
        return $this->sendStandardEmail();
    }

    /**
     * Send email using WPDM Email template system
     *
     * @param string $template Template name
     * @return bool
     */
    private function sendWithTemplate(string $template): bool
    {
        $emailData = [
            'to_email' => $this->get('to'),
            'subject' => $this->get('subject'),
            'message' => $this->get('message'),
        ];

        // Add optional fields
        $optionalFields = ['to_name', 'from_email', 'from_name', 'reply_to', 'cc', 'bcc'];
        foreach ($optionalFields as $field) {
            $value = $this->get($field);
            if ($value) {
                $emailData[$field] = $value;
            }
        }

        // Merge any template variables
        $vars = $this->get('vars', []);
        if (is_array($vars) || is_object($vars)) {
            $emailData = array_merge($emailData, (array) $vars);
        }

        try {
            Email::send($template, $emailData);
            $this->log('Email sent successfully using template: ' . $template);
            return true;
        } catch (\Throwable $e) {
            $this->log('Failed to send email: ' . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Send email using standard wp_mail
     *
     * @return bool
     */
    private function sendStandardEmail(): bool
    {
        $to = $this->get('to');
        $subject = $this->get('subject');
        $message = $this->get('message');
        $headers = $this->get('headers', []);
        $attachments = $this->get('attachments', []);

        // Ensure headers is an array
        if (!is_array($headers)) {
            $headers = [$headers];
        }

        // Add content-type for HTML if message contains HTML
        if ($this->containsHtml($message)) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        }

        // Add from header if specified
        $fromName = $this->get('from_name');
        $fromEmail = $this->get('from_email');
        if ($fromEmail) {
            $from = $fromName ? "{$fromName} <{$fromEmail}>" : $fromEmail;
            $headers[] = "From: {$from}";
        }

        // Add reply-to if specified
        $replyTo = $this->get('reply_to');
        if ($replyTo) {
            $headers[] = "Reply-To: {$replyTo}";
        }

        // Add CC if specified
        $cc = $this->get('cc');
        if ($cc) {
            $headers[] = "Cc: {$cc}";
        }

        // Add BCC if specified
        $bcc = $this->get('bcc');
        if ($bcc) {
            $headers[] = "Bcc: {$bcc}";
        }

        // Ensure attachments is an array
        if (!is_array($attachments)) {
            $attachments = $attachments ? [$attachments] : [];
        }

        $result = wp_mail($to, $subject, $message, $headers, $attachments);

        if ($result) {
            $this->log('Email sent successfully via wp_mail');
        } else {
            $this->log('Failed to send email via wp_mail', 'error');
        }

        return $result;
    }

    /**
     * Check if content contains HTML tags
     *
     * @param string $content
     * @return bool
     */
    private function containsHtml(string $content): bool
    {
        return $content !== strip_tags($content);
    }

    /**
     * Handle job failure - log the error
     *
     * @param \Throwable $e
     * @param object|array $data
     * @return void
     */
    public function failed(\Throwable $e, $data): void
    {
        parent::failed($e, $data);

        // Log additional details
        $to = is_object($data) ? ($data->to ?? 'unknown') : ($data['to'] ?? 'unknown');
        $subject = is_object($data) ? ($data->subject ?? 'unknown') : ($data['subject'] ?? 'unknown');

        error_log(sprintf(
            '[WPDM SendEmailJob] Failed to send email to %s with subject "%s": %s',
            $to,
            $subject,
            $e->getMessage()
        ));
    }

    /**
     * Get job name
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'Send Email';
    }

    /**
     * Get job description
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Sends emails asynchronously';
    }
}
