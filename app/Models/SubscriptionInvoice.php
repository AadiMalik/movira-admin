<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subscription_package_id',
        'stripe_invoice_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'raw_payload'
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscription_package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }
}
