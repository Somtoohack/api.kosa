<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param string $token
     */
    public function __construct(public User $user, public $token)
    {
    }
    public function build()
    {
        return $this->view('mails.password_reset_token')
            ->subject('Recover your account');
    }
}
