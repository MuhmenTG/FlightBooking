<?php

namespace App\Mail;

use Exception;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use SendGrid\Mail\Mail as SendGridMail;

class SendEmailService implements ISendEmailService
{
    /**
    * {@inheritDoc}
    */
    public function sendEmailWithAttachments(string $recipientName, string $recipientEmail, string $subject, string $text, array $attachments = null) : bool
    {
        $email = new SendGridMail();
        $email->setFrom("nmflights-costumerservice@hotmail.com", "N&M flights booking");
        $email->setSubject($subject);
        $email->addTo($recipientEmail, $recipientName);
        $email->addContent("text/plain", $text);
    
        if (!is_null($attachments)) {
            foreach ($attachments as $attachment) {
                $fileContent = base64_encode(file_get_contents($attachment->getRealPath()));
                $email->addAttachment(
                    $fileContent,
                    $attachment->getClientMimeType(),
                    $attachment->getClientOriginalName(),
                    "attachment"
                );
            }
        }
    
        $sendgrid = new \SendGrid(getenv('SEND_GRID_API_KEY'));
        try {
            $sendgrid->send($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
    
    
