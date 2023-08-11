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
    public function sendEmailWithAttachments(string $recipientName, string $recipientEmail, string $subject, string $text, string $pdfContent) : bool
    {
        $email = new SendGridMail();
        $email->setFrom("nmflights-costumerservice@hotmail.com", "N&M flights booking");
        $email->setSubject($subject);
        $email->addTo($recipientEmail, $recipientName);
        $email->addContent("text/plain", $text);

        $email->addAttachment(
            $pdfContent,
            'application/pdf',  
            'booking_details.pdf', 
            "attachment"
        );

        $sendgrid = new \SendGrid(getenv('SEND_GRID_API_KEY'));
        try {
            $sendgrid->send($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    
}
    
    
