<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;

    public function __construct($subject, $body)
    {
        $this->subject = $subject;
        $this->body    = $body;
    }

    public function build()
    {
        return $this->from('mailbox@reventsystems.com', 'Kosa Team')
            ->view('mails.trans_notification')
            ->subject($this->subject)
            ->with([
                'body' => $this->body,
            ]);
    }
}