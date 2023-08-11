<?php

namespace App\Mail;

use Exception;
use SendGrid\Mail\Mail as SendGridMail;

class SendEmailService implements ISendEmailService
{
    /**
    * {@inheritDoc}
    */
    public function sendEmailWithAttachments(string $recipientName, string $recipientEmail, string $subject, string $text, string $pdfContent = null) : bool
    {
        $email = new SendGridMail();
        $email->setFrom("nmflights-costumerservice@hotmail.com", "N&M flights booking");
        $email->setSubject($subject);
        $email->addTo($recipientEmail, $recipientName);
        $email->addContent("text/plain", $text);

        if($pdfContent != null){
            $email->addAttachment(
                $pdfContent,
                'application/pdf',  
                'booking_details.pdf', 
                "attachment"
            );
        }
        
    
        $sendgrid = new \SendGrid(getenv('SEND_GRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    
}
    
    
