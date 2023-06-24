<?php

namespace App\Mail;

interface ISendEmailService
{  
    /**
    * Send an email with attachments.
    *
    * @param string $recipientName The name of the recipient.
    * @param string $recipientEmail The email address of the recipient.
    * @param string $subject The subject of the email.
    * @param string $text The content of the email.
    * @param array|null $attachments (Optional) An array of attachments to include in the email.
    * @return bool Returns true if the email was sent successfully, false otherwise.
    */
    public function sendEmailWithAttachments(string $recipientName, string $recipientEmail, string $subject, string $text, array $attachments = null): bool;
}
