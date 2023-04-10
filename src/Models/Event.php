<?php

namespace Dotburo\Molog\Models;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Dotburo\Molog\Contracts\EventInterface;
use Dotburo\Molog\MologConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Base model for logged messages and gauges.
 *
 * @property int|string $id
 * @property int $user_id
 * @property int $tenant_id
 * @property string $context
 * @property Carbon $created_at
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
abstract class Event extends Model implements EventInterface
{
    /** @inheritdoc */
    public const UPDATED_AT = null;

    /** @inheritdoc */
    protected $dateFormat = MologConstants::CREATED_AT_FORMAT;

    /** @inheritdoc */
    protected $guarded = ['id'];

    /** @inheritdoc */
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
     * Override the constructor to give the model an early timestamp.
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        # Set `created_at` asap to approximate the time at which the event is recorded.
        $attributes[$this->getCreatedAtColumn()] = new DateTime();

        parent::__construct($attributes);
    }

    /** @inheritdoc */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(MologConstants::CREATED_AT_FORMAT);
    }

    /** @inheritdoc */
    public function concerning(?Model $model = null): self
    {
        if (!$model) {
            $this->loggable()->dissociate();

            return $this;
        }

        $this->loggable()->associate($model);

        return $this;
    }

    /** @inheritdoc */
    public function setContext(string $label = ''): self
    {
        return $this->setContextAttribute($label);
    }

    /**
     * Make sure the context is a non-empty string or null.
     * @param string $label
     * @return Event
     */
    protected function setContextAttribute(string $label): Event
    {
        $this->attributes['context'] = $label ?: null;

        return $this;
    }

    /** @inheritdoc */
    public function setTenant($tenant = 0): self
    {
        return $this->setTenantIdAttribute($tenant);
    }

    /**
     * Make sure the tenant ID is an int or null.
     * @param int|Model $id
     * @return Event
     */
    protected function setTenantIdAttribute($id = 0): Event
    {
        if ($id && $id instanceof Model) {
            $id = $id->getKey();
        }

        $this->attributes['tenant_id'] = $id ?: null;

        return $this;
    }

    /** @inheritdoc */
    public function setUser($user = 0): self
    {
        return $this->setUserIdAttribute($user);
    }

    /**
     * Make sure the user ID is an int or null.
     * @param int|Model $id
     * @return Event
     */
    protected function setUserIdAttribute($id = 0): Event
    {
        if ($id && $id instanceof Model) {
            $id = $id->getKey();
        }

        $this->attributes['user_id'] = $id ?: null;

        return $this;
    }
}
