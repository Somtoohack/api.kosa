<?php
namespace App\Jobs;

use App\Mail\TransactionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTransactionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $transactionDetails;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user
     * @param  array  $transactionDetails
     */
    public function __construct($user, $transactionDetails)
    {
        $this->user               = $user;
        $this->transactionDetails = $transactionDetails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send the transaction notification email
        Mail::to($this->user->email)->send(new TransactionNotification($this->transactionDetails));
    }
}