<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_id' => 'required|integer|exists:rentals,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:2000',
        ]);

        $rental = Rental::query()
            ->with('vehicle')
            ->where('id', $validated['rental_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($rental->status !== 'Completed') {
            return $this->respondError($request, 'Only completed rentals can be reviewed.', 422);
        }

        if ($rental->review) {
            return $this->respondError($request, 'You already submitted a review for this booking.', 422);
        }

        $ownerId = (int) ($rental->vehicle?->user_id ?? 0);
        if ($ownerId <= 0) {
            return $this->respondError($request, 'Unable to determine the vehicle owner for this booking.', 422);
        }

        $review = Review::create([
            'rental_id' => $rental->id,
            'vehicle_id' => $rental->vehicle_id,
            'owner_id' => $ownerId,
            'reviewer_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => trim($validated['comment']),
        ]);

        $ownerStats = Review::query()
            ->where('owner_id', $ownerId)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $payload = [
            'review' => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at?->toIso8601String(),
            ],
            'owner' => [
                'id' => $ownerId,
                'avg_rating' => $ownerStats?->avg_rating ? round((float) $ownerStats->avg_rating, 2) : 0,
                'total_reviews' => (int) ($ownerStats?->total ?? 0),
            ],
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('dashboard')->with('success', 'Review submitted successfully.');
    }

    public function vehicle(Request $request, Vehicle $vehicle)
    {
        $reviews = Review::query()
            ->with('reviewer:id,name')
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('id')
            ->paginate(5);

        $avg = Review::query()
            ->where('vehicle_id', $vehicle->id)
            ->avg('rating');

        return response()->json([
            'vehicle' => [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'license_plate' => $vehicle->license_plate,
            ],
            'avg_rating' => $avg ? round((float) $avg, 2) : 0,
            'total_reviews' => $reviews->total(),
            'reviews' => $reviews->items(),
            'next_page_url' => $reviews->nextPageUrl(),
            'prev_page_url' => $reviews->previousPageUrl(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage(),
        ]);
    }

    private function respondError(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['review' => $message])->withInput();
    }
}
