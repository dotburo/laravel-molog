<?php

namespace Dotburo\Molog\Contracts;

use Illuminate\Database\Eloquent\Model;

interface EventInterface
{
    /**
     * Attach the model concerned by the current log(s).
     * @param Model|null $model
     * @return self
     */
    public function concerning(?Model $model = null): self;

    /**
     * Set the context property for the object.
     * @param string $label
     * @return self
     */
    public function setContext(string $label = ''): self;

    /**
     * Set the `user_id` property for the object.
     * @param int|Model $tenant
     * @return self
     */
    public function setTenant($tenant = 0): self;

    /**
     * Set the `user_id` property for the object.
     * @param int|Model $user
     * @return self
     */
    public function setUser($user = 0): self;
}
