<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerRatingsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $filter = (string) $request->query('filter', 'all');

        $query = User::query()
            ->where('is_aaracc', true)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            });

        if ($filter === 'has_reviews') {
            $query->whereIn('id', Review::query()->select('owner_id')->distinct());
        } elseif ($filter === 'no_reviews') {
            $query->whereNotIn('id', Review::query()->select('owner_id')->distinct());
        } elseif ($filter === 'below3') {
            $query->whereIn('id', Review::query()->select('owner_id')->groupBy('owner_id')->havingRaw('AVG(rating) < 3'));
        } elseif ($filter === 'above3') {
            $query->whereIn('id', Review::query()->select('owner_id')->groupBy('owner_id')->havingRaw('AVG(rating) >= 3'));
        }

        $owners = $query
            ->withCount('vehicles')
            ->withAvg('reviewsReceived', 'rating')
            ->withCount('reviewsReceived')
            ->with([
                'vehicles' => function ($q) {
                    $q->withAvg('reviews', 'rating')
                        ->withCount('reviews')
                        ->with([
                            'reviews' => function ($rq) {
                                $rq->with('reviewer:id,name')
                                    ->orderByDesc('id');
                            },
                        ])
                        ->orderBy('name')
                        ->orderBy('license_plate');
                },
            ])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.owner-ratings.index', compact('owners', 'q', 'filter'));
    }
}
