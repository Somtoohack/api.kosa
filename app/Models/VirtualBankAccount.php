<?php
namespace App\Models;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'wallet_key',
        'account_number',
        'account_name',
        'bank_name',
        'status',
        'meta',
        'provider',
        'reference',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the user that owns the virtual bank account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    protected function meta(): Attribute
    {

        return Attribute::make(

            get: fn($value) => json_decode($value, true),

            set: fn($value) => json_encode($value),

        );

    }
}
