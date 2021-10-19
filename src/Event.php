<?php

namespace Dotburo\LogMetrics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Base model for logged messages and metrics.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $context
 * @property int $context_id
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
    protected $hidden = ['id'];

}
