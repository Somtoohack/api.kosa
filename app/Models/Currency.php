<?php
namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country_code',
        'country_name',
        'flag',
        'dial_code',
        'exchange_rate',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_active'     => 'boolean',
    ];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

}
