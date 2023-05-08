<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EventType
 * @package App\Models
 */
class EventType extends Model
{
    use HasFactory;

    protected $table = "event_types";

    protected $fillable = [
        "event_name"
    ];

}
