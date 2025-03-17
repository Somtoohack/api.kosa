<?php
namespace App\Services;

use App\Jobs\SendTransactionNotification;
use App\Models\User;

class NotificationService
{

    public function sendTransactionNotification(User $user, array $transactionDetails): void
    {
        // Dispatch the notification job
        dispatch(new SendTransactionNotification($user, $transactionDetails));
    }
}