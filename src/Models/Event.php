<?php

namespace dotburo\LogMetrics\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
    const UPDATED_AT = null;

    /** @inheritDoc */
    protected $guarded = ['*'];

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
     * Make sure the tenant ID is set as an int or null.
     * @param int $id
     * @return void
     */
    public function setTenantIdAttribute(int $id): void
    {
        $this->attributes['tenant_id'] = $id ?: null;
    }

    /**
     * Make sure the context is set as a string or null.
     * @param string $label
     * @return void
     */
    public function setContextAttribute(string $label): void
    {
        $this->attributes['context'] = $label ?: null;
    }

    /**
     * Make sure the relationship ID is set as an int or null.
     * @param int $id
     * @return void
     */
    public function setLoggableIdAttribute(int $id): void
    {
        $this->attributes['loggable_id'] = $id ?: null;
    }

    /**
     * Make sure the relationship class name is set as a string or null.
     * @param int $name
     * @return void
     */
    public function setLoggableTypeAttribute(int $name): void
    {
        $this->attributes['loggable_type'] = $name ?: null;
    }

}