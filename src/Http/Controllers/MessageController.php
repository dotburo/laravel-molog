<?php

namespace Dotburo\LogMetrics\Http\Controllers;

use Dotburo\LogMetrics\Models\Message;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class MessageController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Jsonable
     */
    public function index(Request $request): Jsonable
    {
        $query = Message::query();

        if ($request->boolean('metrics', true)) {
            $query->with('metrics');
        }

        $perPage = config('log-metrics.per-page');

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
