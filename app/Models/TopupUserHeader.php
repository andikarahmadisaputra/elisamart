<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TopupUserHeader extends Model
{
    use HasFactory;

    protected static function boot()
    {

        parent::boot();

        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = Auth::id() ?: null;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Auth::id() ?: null;
            }
            $model->transaction_number = self::generateTransactionNumber();
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = Auth::id() ?: null;
            }
        });
    }

    private static function generateTransactionNumber()
    {
        $prefix = 'USR-';
        $date = date('Ymd');

        // Mendapatkan nomor urut dari transaksi terakhir di hari yang sama
        $lastTransaction = self::whereDate('created_at', now()->format('Y-m-d'))
                                ->orderBy('id', 'desc')
                                ->first();

        if ($lastTransaction) {
            // Ambil nomor urut dari nomor transaksi terakhir
            $lastNumber = intval(substr($lastTransaction->transaction_number, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format nomor urut menjadi 4 digit
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return $prefix . $date . '-' . $formattedNumber;
    }

    protected $fillable = [
        'store_id', 'total_user', 'total_amount', 'note', 'status'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function topupUserDetails()
    {
        return $this->hasMany(TopupUserDetail::class);
    }

}
