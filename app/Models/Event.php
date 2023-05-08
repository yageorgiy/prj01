<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Event
 * Used for retrieving statistics
 * @package App\Models
 */
class Event extends Model
{

    const TYPE_PER_EVENT = 1;
    const TYPE_PER_USER = 2;
    const TYPE_PER_AUTH_STATUS = 3;

    use HasFactory;

    protected $table = "events";

    protected $fillable = [
        "event_type_id",
        "user_id",
        "ip_address"
    ];

    /**
     * Get associated event type
     * @return BelongsTo
     */
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(
            EventType::class,
            "event_type_id",
            "id",
        );
    }

    /**
     * Get associated user
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            "user_id",
            "id",
        );
    }
}
