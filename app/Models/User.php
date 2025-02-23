<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\AuthorizationPin;
use App\Models\Currency;
use App\Models\LoginLog;
use App\Models\PinHistory;
use App\Models\UserKYC;
use App\Models\UserProfile;
use App\Models\VirtualBankAccount;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_key',
        'country',
        'failed_attempts',
        'is_locked',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function loginLogs()
    {
        return $this->morphMany(LoginLog::class, 'loggable');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if (! $user->wallet) {

                $currency_id         = Currency::where('country_code', $user->country)->first()->id;
                $wallet              = new Wallet();
                $wallet->user_id     = $user->id;
                $wallet->balance     = 0;
                $wallet->currency_id = $currency_id;
                $wallet->reference   = Str::uuid() . '_' . now()->timestamp;
                $wallet->save();
            }
        });
    }

    public function pinHistories(): HasMany
    {
        return $this->hasMany(PinHistory::class);
    }
    public function kyc(): HasOne
    {
        return $this->hasOne(UserKYC::class);
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }

    public function virtualBankAccounts(): HasMany
    {
        return $this->hasMany(VirtualBankAccount::class);
    }

    public function authorizationPin(): HasOne
    {
        return $this->hasOne(AuthorizationPin::class);
    }
}
