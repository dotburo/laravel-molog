<?php

namespace Dotburo\Molog\Factories;

use Dotburo\Molog\EventInterface;
use Dotburo\Molog\Exceptions\MologException;
use Dotburo\Molog\Models\Event;
use Dotburo\Molog\Models\Message;
use Dotburo\Molog\Models\Gauge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Stringable;

/**
 * Class EventFactory.
 *
 * @copyright 2021 dotburo
 * @author dotburo <code@dotburo.org>
 */
class EventFactory implements EventInterface, Stringable
{
    /** @var Collection */
    protected Collection $items;

    /** @var string|null */
    protected ?string $context = null;

    /** @var Model|null */
    protected ?Model $relation = null;

    /** @var int|null */
    protected ?int $user_id = null;

    /** @var int|null */
    protected ?int $tenant_id = null;

    /**
     * EventFactory constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Return the last created event.
     * @return Event|Gauge|Message|null
     */
    public function last(): ?Event
    {
        return $this->items->last();
    }

    /**
     * @param Event $event
     * @return Event
     */
    protected function setGlobalProperties(Event $event): Event
    {
        foreach (['context', 'user_id', 'tenant_id'] as $property) {
            if (isset($this->$property)) {
                $event->$property = $this->$property;
            }
        }

        if ($this->relation) {
            $event->concerning($this->relation);
        }

        return $event;
    }

    /** @inheritdoc */
    public function concerning(?Model $model = null): self
    {
        $this->relation = $model;

        return $this;
    }

    /** @inheritdoc */
    public function setContext(string $label = ''): self
    {
        $this->context = $label ?: null;

        return $this;
    }

    /** @inheritdoc */
    public function setTenant($tenant = 0): self
    {
        if ($tenant && $tenant instanceof Model) {
            $tenant = $tenant->getKey();
        }

        $this->tenant_id = $tenant ?: null;

        return $this;
    }

    /** @inheritdoc */
    public function setUser($user = 0): self
    {
        if ($user && $user instanceof Model) {
            $user = $user->getKey();
        }

        $this->user_id = $user ?: null;

        return $this;
    }

    /**
     * Pass method calls to the event collection if possible.
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws MologException
     */
    public function __call(string $name, array $arguments)
    {
        if (! method_exists($this->items, $name)) {
            throw new MologException("Method '$name' is not implemented in " . static::class);
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

    /**
     * Return the print-out in reversed order.
     * @return string
     */
    public function __toString(): string
    {
        return $this->items->reverse()->map(function (Event $event) {
            return (string)$event;
        })->join(PHP_EOL);
    }
}
