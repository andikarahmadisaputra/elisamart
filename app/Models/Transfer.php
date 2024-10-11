<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected static function boot()
    {

        parent::boot();

        // updating transaction_number when model is created
        static::creating(function ($model) {
            $model->transaction_number = self::generateTransactionNumber();
        });
    }

    private static function generateTransactionNumber()
    {
        $prefix = 'TRF-';
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
        'transaction_number', 'sender_id', 'recipient_id', 'amount', 'note', 'status'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
