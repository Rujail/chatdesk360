<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    // 🔹 Tell Laravel to use the secondary database
    protected $connection = 'mysql_alphasuperdb';

    protected $table = 'contact_leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
    ];
}