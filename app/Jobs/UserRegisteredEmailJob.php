<?php
namespace App\Jobs;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Ensure this line is present

class UserRegisteredEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    protected User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // Prepare the welcome message
        $message = "Welcome, " . $this->user->name . "! Thank you for registering.";

        // Send the email (assuming you have a Mailable class for the welcome email)
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));

        // Log the welcome message
        Log::info("Sent welcome email to: " . $this->user->email . " with message: " . $message);
    }
}
