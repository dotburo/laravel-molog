<?php

namespace Dotburo\LogMetrics\Factories;

use Dotburo\LogMetrics\Exceptions\EventFactoryException;
use Dotburo\LogMetrics\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Stringable;

abstract class EventFactory implements Stringable
{
    /** @var Collection */
    protected Collection $items;

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
     * Set the properties on the model and return the factory.
     * @param int|Model $id
     * @param string $name
     * @return $this
     */
    public function setRelation($id = 0, string $name = ''): EventFactory
    {
        $modelInstance = $id instanceof Model;

        $id = $modelInstance ? $id->getKey() : (int)$id;

        $name = !$name && $modelInstance ? get_class($id) : $name;

        $this->items->each(function(Event $event) use ($id, $name) {
            $event->setLoggableIdAttribute($id);

            $event->setLoggableTypeAttribute($name);
        });

        return $this;
    }

    /**
     * Set the property on the model and return the factory.
     * @param object|string $label
     * @return $this
     */
    public function setContext($label): EventFactory
    {
        $label = is_object($label) ? get_class($label) : $label;

        $this->items->each(function(Event $event) use ($label) {
            $event->setContextAttribute($label);
        });

        return $this;
    }

    /**
     * Set the property on the model and return the factory.
     * @param int $id
     * @return $this
     */
    public function setTenant(int $id): EventFactory
    {
        $this->items->each(function(Event $event) use ($id) {
            $event->setTenantIdAttribute($id);
        });

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
     * @param bool $reset
     * @return int
     */
    public function save(bool $reset = true): int
    {
        $count = $this->items->filter(function (Model $model) {
            return $model->save();
        })->count();

        if ($reset) {
            $this->reset();
        }

        return $count;
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
