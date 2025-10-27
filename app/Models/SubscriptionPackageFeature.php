<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageFeature extends Model
{
    use HasFactory;

    protected $table = 'subscription_package_features';

    protected $fillable = [
        'subscription_package_id',
        'title',
        'value',
        'sorting',
        'created_at',
        'updated_at'
    ];

    public function package() {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id', 'id');
    }
}
