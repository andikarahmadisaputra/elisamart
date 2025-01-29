<?php

return [
    'topup_store' => [
        'index' => [
            'title' => 'Daftar Isi Ulang Saldo Toko',
        ],
        'create' => [
            'title' => 'Isi Ulang Saldo Toko',
        ],
        'edit' => [
            'title' => 'Ubah Isi Ulang Saldo Toko'
        ],
        'button' => [
            'add' => 'Isi Ulang',
            'cancel' => 'Batal',
            'submit' => 'Simpan',
            'edit' => 'Ubah',
            'under_review' => 'Tinjau',
            'approve' => 'Setujui',
            'reject' => 'Tolak',
        ],
        'table' => [
            'no' => 'No',
            'transaction_number' => 'Nomer Transaksi',
            'store' => 'Toko Penerima',
            'amount' => 'Jumlah',
            'note' => 'Catatan',
            'status' => 'Status',
            'created_at' => 'Dibuat Tanggal',
            'created_by' => 'Dibuat Oleh',
            'action' => 'Aksi',
        ],
        'form' => [
            'store' => 'Toko',
            'amount' => 'Jumlah',
            'note' => 'Catatan',
        ],
        'message' => [
            'create' => [
                'success' => 'Buat draft isi ulang saldo toko berhasil',
                'failed' => 'Mohon maaf, proses buat draft isi ulang toko gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
            'edit' => [
                'success' => 'Ubah draft isi ulang saldo toko berhasil',
                'failed' => 'Mohon maaf, proses ubah draft isi ulang toko gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
            'cancel' => [
                'success' => 'Proses update status `canceled` berhasil. Draft isi ulang saldo toko batak diajukan',
                'failed' => 'Mohon maaf, proses update status `canceled` gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
            'under_review' => [
                'success' => 'Proses update status `under review` berhasil. Toko sudah tidak diperbolehkan mengubah draft isi ulang toko',
                'failed' => 'Mohon maaf, proses update status `under review` gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
            'approve' => [
                'success' => 'Proses update status `approved` berhasil. Saldo toko telah ditambahkan',
                'failed' => 'Mohon maaf, proses update status `approved` gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
            'reject' => [
                'success' => 'Proses update status `rejected` berhasil. Saldo toko batal ditambahkan',
                'failed' => 'Mohon maaf, proses update status `rejected` gagal. Silahkan hubungi admin untuk bantuan lebih lanjut.',
            ],
        ],
    ],
    'topup_user_header' => [
        'index' => [
            'title' => 'Topup User',
        ],
        'create' => [
            'title' => 'Create Topup User',
        ],
        'button' => [
            'create_by_tag' => 'Isi Ulang Berdasarkan Label',
            'create_by_user' => 'Isi Ulang Berdasarkan User',
            'cancel' => 'Batal',
            'submit' => 'Simpan',
            'edit' => 'Ubah',
            'under_review' => 'Tinjau',
            'approve' => 'Setujui',
            'reject' => 'Tolak',
            'show' => 'Lihat',
        ],
        'table' => [
            'no' => 'No',
            'transaction_number' => 'Nomer Transaksi',
            'store' => 'Toko Pengirim',
            'total_amount' => 'Total Saldo',
            'note' => 'Keterangan',
            'status' => 'Status',
            'created_at' => 'Dibuat Tanggal',
            'created_by' => 'Dibuat Oleh',
            'action' => 'Aksi',
            'nik' => 'NIK',
            'user' => 'Nama',
            'tag' => 'Label',
            'amount' => 'Jumlah',
        ],
        'form' => [

        ],
        'message' => [

        ],
    ],
    'topup_user' => [
        'button' => [
            'back' => 'Kembali'
        ],
    ],
    'payment_request' => [
        'index' => [
            'title' => 'Permintaan Pembayaran',
        ],
        'create' => [
            'title' => 'Buat Permintaan Pembayaran',
        ],
        'table' => [
            'no' => 'No',
            'transaction_number' => 'Nomer Transaksi',
            'bon_number' => 'Nomor BON',
            'store' => 'Toko',
            'user' => 'Konsumen',
            'amount' => 'Total',
            'note' => 'Keterangan',
            'status' => 'Status',
            'created_at' => 'Tanggal',
            'created_by' => 'Dibuat Oleh',
        ],
        'form' => [
            'store' => 'Toko',
            'user' => 'Konsumen',
            'amount' => 'Jumlah',
            'note' => 'Keterangan',
        ],
        'button' => [
            'add' => 'Buat Baru',
            'edit' => 'Ubah',
            'cancel' => 'Batal',
            'submit' => 'Simpan',
        ],
        'message' => [
            'create' => [
                'success' => 'sukses',
                'error' => 'error',
            ],
        ],
    ],
];