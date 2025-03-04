<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     */
    public function __construct(public User $user)
    {
    }

    public function build()
    {
        return $this->view('mails.password_reset_confirmation')
            ->subject('Your password was changed');
    }
}
