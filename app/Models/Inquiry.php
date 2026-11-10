<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'service_id',
        'message',
        'status',
        'priority',
        'admin_notes',
        'ip_address',
    ];

    /**
     * Scope for pending/new inquiries
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['new', 'in_progress']);
    }

    /**
     * Scope for recent inquiries
     */
    public function scopeRecent($query)
    {
        return $query->latest();
    }
}
