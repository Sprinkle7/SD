<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceEmailLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['payment_intent', 'type', 'sent_at'];
}
