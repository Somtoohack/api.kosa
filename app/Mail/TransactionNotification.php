<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $transactionDetails;

    public function __construct(array $transactionDetails)
    {
        $this->transactionDetails = $transactionDetails;
    }

    public function build()
    {
        return $this->subject('Transaction Notification')
            ->view('mails.transaction_notification');
    }
}