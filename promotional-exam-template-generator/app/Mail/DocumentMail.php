<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $fileName;
    public $pdfContent;

    public function __construct($firstName, $fileName, $pdfContent)
    {
        $this->firstName=$firstName;
        $this->fileName=$fileName;
        $this->pdfContent=$pdfContent;
    }

    public function build(): array
    {
        return $this->view('emails.document')
                    ->with([
                        'firstName' => $this.firstName
                    ])
                    ->attachData($this.pdfContent, $this.fileName, [
                        'mime' => 'application/pdf'
                    ])
                    ->subject('Your Report Card for the SEC Promotional Exam.');
    }
}
