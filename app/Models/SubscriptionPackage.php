<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscription_packages';
    protected $fillable = [
        'title',
        'description',
        'price',
        'duration_type',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $hidden = [
        'deleted_at',
    ];

    public function features() {
        return $this->hasMany(SubscriptionPackageFeature::class, 'subscription_package_id', 'id');
    }
}
