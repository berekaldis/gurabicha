<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    private const CATEGORY_ORDER = ['FOOD', 'PIZZA', 'DRINKS'];

    /**
     * GET /api/menu — all menu items ordered by category (FOOD, PIZZA, DRINKS) then name.
     */
    public function index(): JsonResponse
    {
        try {
            $items = MenuItem::all();

            $sorted = $items->sort(function ($a, $b) {
                $ca = array_search($a->category, self::CATEGORY_ORDER, true);
                $cb = array_search($b->category, self::CATEGORY_ORDER, true);

                $ca = $ca === false ? 999 : $ca;
                $cb = $cb === false ? 999 : $cb;

                if ($ca !== $cb) {
                    return $ca <=> $cb;
                }

                return strcasecmp($a->name, $b->name);
            })->values();

            return response()->json(['menu' => $sorted]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
