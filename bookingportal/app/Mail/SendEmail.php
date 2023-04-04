<?php

namespace App\Mail;

use Exception;
use Illuminate\Mail\Mailables\Attachment;
use SendGrid;
use SendGrid\Mail\Mail;

class SendEmail
{
    public static function sendEmailWithAttachments($recipientName, $recipientEmail, $subject, $text, $attachments = null) {
        $email = new Mail();
        $email->setFrom('muhmenpk@gmail.com', 'N&M flights booking');
        $email->setSubject($subject);
        $email->addTo($recipientEmail, $recipientName);
        $email->addContent("text/plain", $text);
    
        if(!is_null($attachments)){   
            foreach ($attachments as $attachment) {
                $attachmentFile = new Attachment();
                $attachmentFile->setContent(base64_encode(file_get_contents($attachment->getRealPath())));
                $attachmentFile->setType($attachment->getClientMimeType());
                $attachmentFile->setFilename($attachment->getClientOriginalName());
                $attachmentFile->setDisposition('attachment');
                $email->addAttachment($attachmentFile);
            }
        }
    
        $sendgrid = new SendGrid('');
        try {
            $response = $sendgrid->send($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    
}
