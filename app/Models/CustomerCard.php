<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_cards';

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'brand',
        'last4',
        'exp_month',
        'exp_year',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
