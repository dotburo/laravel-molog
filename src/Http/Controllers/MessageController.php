<?php

namespace Dotburo\Molog\Http\Controllers;

use Dotburo\Molog\Models\Message;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class MessageController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Message::query();

        if ($request->boolean('gauges', true)) {
            $query->with('gauges');
        }

        if ($levels = $request->get('levels')) {
            $query->whereIn('level', (array)Message::levelCode($levels));
        }

        $orderBy = $request->get('order_by') ?: 'created_at';

        $direction = $request->get('order') ?: 'desc';

        $perPage = (int)$request->get('per_page', config('molog.per_page'));

        return $query
            ->orderBy($orderBy, $direction)
            ->paginate($perPage);
    }
}
