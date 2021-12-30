<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\Exceptions\EventFactoryException;
use Dotburo\LogMetrics\Models\Event;
use Dotburo\LogMetrics\Models\Message;
use Dotburo\LogMetrics\Models\Metric;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Class EventFactory.
 *
 * @method Event last()
 * @method Event where()
 * @
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
abstract class EventFactory implements Stringable
{
    /** @var Collection */
    protected Collection $items;

    /** @var string */
    protected string $lastUuid;

    /** @var string|null */
    protected ?string $context = null;

    /** @var int|string|null */
    protected $relationId = null;

    /** @var string|null */
    protected ?string $relationName = null;

    /** @var int|null */
    protected ?int $tenantId = null;

    /**
     * Cast all events to a multiline string.
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * EventFactory constructor.
     */
    public function __construct()
    {
        $this->items = new Collection();
    }

    /**
     * @param Event $event
     * @return Event
     */
    protected function setGlobalProperties(Event $event): Event
    {
        if ($this->context) {
            $event->setContextAttribute($this->context);
        }

        if ($this->tenantId) {
            $event->setTenantIdAttribute($this->tenantId);
        }

        if ($this->relationId) {
            $event->setLoggableIdAttribute($this->relationId);
            $event->setLoggableTypeAttribute($this->relationName ?: '');
        }

        return $event;
    }

    /**
     * Keep track of the last created or updated event.
     * @param string $uuid
     * @return void
     */
    protected function setLastUuid(string $uuid): void
    {
        $this->lastUuid = $uuid;
    }

    /**
     * Return the last created or updated event.
     * @return string
     */
    public function previousUuid(): string
    {
        return $this->lastUuid;
    }

    /**
     * Return the last created or updated event.
     * @return Event|Metric|Message|null
     */
    public function previous(): ?Event
    {
        return !empty($this->lastUuid) ? $this->items->get($this->lastUuid) : null;
    }

    /**
     * Set the relation properties on the last event.
     * @param Model|int|string $id
     * @param string $name
     * @return $this
     */
    public function setRelation($id, string $name = ''): EventFactory
    {
        $event = $this->previous();

        list($id, $name) = $this->resolveRelation($id, $name);

        if ($event) {
            $event->setLoggableIdAttribute($id);
            $event->setLoggableTypeAttribute($name);
        }

        return $this;
    }

    /**
     * Assign the global relation properties.
     * @param Model|int|string $id
     * @param string $name
     * @return EventFactory
     */
    public function setRelationGlobally($id, string $name = ''): EventFactory
    {
        list($id, $name) = $this->resolveRelation($id, $name);

        $this->relationId = $id;

        $this->relationName = $name;

        return $this;
    }

    /**
     * Extract the relation reference from the given parameters.
     * @param Model|int|string $id
     * @param string $name
     * @return array
     */
    protected function resolveRelation($id, string $name = ''): array
    {
        $modelInstance = $id instanceof Model;

        $relationId = $modelInstance ? $id->getKey() : $id;

        $relationName = !$name && $modelInstance ? get_class($id) : $name;

        return [$relationId, $relationName];
    }

    /**
     * Set the context property on the last event.
     * @param object|string $label
     * @return $this
     */
    public function setContext($label): EventFactory
    {
        $label = is_object($label) ? get_class($label) : $label;

        if ($event = $this->previous()) {
            $event->setContextAttribute($label);
        }

        return $this;
    }

    /**
     * Assign the global context property.
     * @param string $label
     * @return EventFactory
     */
    public function setContextGlobally(string $label): EventFactory
    {
        $this->context = $label;

        return $this;
    }

    /**
     * Set the tenant property on the last event.
     * @param int $id
     * @return $this
     */
    public function setTenant(int $id): EventFactory
    {
        if ($event = $this->previous()) {
            $event->setTenantIdAttribute($id);
        }

        return $this;
    }

    /**
     * Assign the global tenant property.
     * @param int $id
     * @return EventFactory
     */
    public function setTenantGlobally(int $id): EventFactory
    {
        $this->tenantId = $id;

        return $this;
    }

    /**
     * Pass method calls to the event collection if possible.
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws EventFactoryException
     */
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this->items, $name)) {
            throw new EventFactoryException("The method '$name' is not implemented.");
        }

        return $this->items->$name(...$arguments);
    }

    /**
     * Store all events.
     * @return int
     */
    public function save(): int
    {
        return $this->items->filter(function (Model $model) {
            return $model->save();
        })->count();
    }

    /**
     * Clear the current events.
     * @return EventFactory
     */
    public function reset(): EventFactory
    {
        $this->items = new Collection();

        return $this;
    }
}
