<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupUserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'topup_user_header_id', 'user_id', 'amount'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function topupUserHeader()
    {
        return $this->belongsTo(TopupUserHeader::class, 'topup_user_header_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
