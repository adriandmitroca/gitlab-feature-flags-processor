<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditEvent extends Model
{
    use HasFactory;

    protected $casts = [
        'details' => 'json',
    ];

    protected $dates = [
        'imported_at',
    ];
}
