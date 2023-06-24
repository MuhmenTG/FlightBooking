<?php

namespace App\Mail;

interface ISendEmailService
{
    public function sendEmailWithAttachments(string $recipientName, string $recipientEmail, string $subject, string $text, array $attachments = null): bool;
}
