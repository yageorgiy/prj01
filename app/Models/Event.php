<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    const TYPE_PER_EVENT = 1;
    const TYPE_PER_USER = 2;
    const TYPE_PER_AUTH_STATUS = 3;

    use HasFactory;
}
