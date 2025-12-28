<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_surat',
        'predikat',
        'tanggal_terbit',
        'status',
        'approved_by',
        'approved_at',
        'pdf_data'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'approved_at' => 'datetime'
    ];

    // Relationship dengan User (peserta)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship dengan User (approver)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope untuk filter status
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}