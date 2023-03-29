<?php

namespace App\Mail;

use Exception;
use SendGrid\Mail\Mail;

class SendEmail
{
    public static function sendEmailWithAttachments($attachments, $recipientName, $recipientEmail) {
        $email = new Mail();
        $email->setFrom('muhmenpk@gmail.com', 'N&M flights booking');
        $email->setSubject('Booking confirmation PDF');
        $email->addTo($recipientEmail, $recipientName);
        $email->addContent(
            "text/plain",
            "Thank you for choosing to book with us. We are pleased to confirm your reservation."
        );
    
        foreach ($attachments as $attachment) {
            $attachmentFile = new \SendGrid\Mail\Attachment();
            $attachmentFile->setContent(base64_encode(file_get_contents($attachment->getRealPath())));
            $attachmentFile->setType($attachment->getClientMimeType());
            $attachmentFile->setFilename($attachment->getClientOriginalName());
            $attachmentFile->setDisposition('attachment');
            $email->addAttachment($attachmentFile);
        }
    
        $sendgrid = new \SendGrid('');
    
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }
    
}
