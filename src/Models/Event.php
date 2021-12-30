<?php

namespace Dotburo\LogMetrics\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * Base model for logged messages and metrics.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $loggable_id
 * @property string $loggable_type
 * @property string $context
 * @property Carbon $created_at
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class Event extends Model
{
    /** @inheritDoc */
    public const UPDATED_AT = null;

    /** @inheritDoc */
    public $incrementing = false;

    /** @inheritDoc */
    protected $keyType = 'uuid';

    /** @inheritDoc */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /** @inheritDoc */
    protected $guarded = ['id', 'created_at'];

    /** @inheritDoc */
    protected $hidden = ['id'];

    /**
     * Get the parent model.
     * @return MorphTo
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Override the constructor to give the model a timestamp and an UUID as primary key if it isn't assigned yet.
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $keyName = $this->getKeyName();

        if (! $this->getAttribute($keyName)) {
            $this->setAttribute($keyName, Str::uuid()->toString());

            $this->updateTimestamps();
        }
    }

    /**
     * Save the time with millisecond precision.
     * {@inheritDoc}
     */
    public function setCreatedAt($value)
    {
        $this->{$this->getCreatedAtColumn()} = $value->format('Y-m-d H:i:s.u');

        return $this;
    }

    /**
     * Make sure the tenant ID is set as an int or null.
     * @param int $id
     * @return Event
     */
    public function setTenantIdAttribute(int $id): Event
    {
        $this->attributes['tenant_id'] = $id ?: null;

        return $this;
    }

    /**
     * Make sure the context is set as a string or null.
     * @param string $label
     * @return Event
     */
    public function setContextAttribute(string $label): Event
    {
        $this->attributes['context'] = $label ?: null;

        return $this;
    }

    /**
     * Make sure the relationship ID is set as an int or null.
     * @param int|string $id Can be integer or UUID.
     * @return Event
     */
    public function setLoggableIdAttribute($id): Event
    {
        $this->attributes['loggable_id'] = $id ?: null;

        return $this;
    }

    /**
     * Make sure the relationship class name is set as a string or null.
     * @param string $name
     * @return Event
     */
    public function setLoggableTypeAttribute(string $name): Event
    {
        $this->attributes['loggable_type'] = $name ?: null;

        return $this;
    }
}
