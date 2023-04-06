<?php

namespace Dotburo\Molog;

use Illuminate\Database\Eloquent\Model;

interface EventInterface
{
    /**
     * Attach the model concerned by the current log(s).
     * @param Model|null $model
     * @return $this
     */
    public function concerning(Model $model): self;

    /**
     * Set the context property for the object.
     * @param string $label
     * @return $this
     */
    public function setContext(string $label = ''): self;

    /**
     * Set the `user_id` property for the object.
     * @param int|Model $tenant
     * @return $this
     */
    public function setTenant($tenant = 0): self;

    /**
     * Set the `user_id` property for the object.
     * @param int|Model $user
     * @return $this
     */
    public function setUser($user = 0): self;
}
