<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\UserLegitimacyProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicOwnerController extends Controller
{
    public function show(Request $request, User $user)
    {
        $perPage = $this->parsePerPage($request->query('per_page', '10'));
        $onlyVehicle = (string) $request->query('only_vehicle', '0') === '1';

        $owner = User::query()
            ->whereKey($user->id)
            ->withCount('vehicles')
            ->firstOrFail();

        $selectedVehicleId = (int) $request->query('vehicle_id', 0);
        $selectedVehicle = null;
        if ($selectedVehicleId > 0) {
            $selectedVehicle = Vehicle::query()
                ->where('id', $selectedVehicleId)
                ->where('user_id', $owner->id)
                ->first(['id', 'name', 'license_plate', 'color', 'year_model']);
        }

        $stats = Review::query()
            ->where('owner_id', $owner->id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $avg = $stats?->avg_rating ? round((float) $stats->avg_rating, 2) : 0;
        $total = (int) ($stats?->total ?? 0);

        $ownerReviewsQuery = Review::query()
            ->with([
                'vehicle:id,name,license_plate,color,year_model',
                'reviewer:id,name',
            ])
            ->where('owner_id', $owner->id)
            ->when($onlyVehicle && $selectedVehicle, fn ($q) => $q->where('vehicle_id', $selectedVehicle->id))
            ->orderByDesc('id');

        if ($perPage === null) {
            $items = $ownerReviewsQuery->get();
            $reviews = $items->map(fn ($r) => $this->mapReview($r))->all();
            $pagination = [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 'all',
                'total' => $items->count(),
                'next_page_url' => null,
                'prev_page_url' => null,
            ];
        } else {
            $p = $ownerReviewsQuery->paginate($perPage)->withQueryString();
            $reviews = collect($p->items())->map(fn ($r) => $this->mapReview($r))->all();
            $pagination = [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
                'next_page_url' => $p->nextPageUrl(),
                'prev_page_url' => $p->previousPageUrl(),
            ];
        }

        $proofs = UserLegitimacyProof::query()
            ->where('user_id', $owner->id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($pf) {
                return [
                    'url' => Storage::url($pf->file_path),
                    'path' => $pf->file_path,
                ];
            })
            ->all();

        return response()->json([
            'owner' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'address' => $owner->address,
                'profile_photo_url' => $owner->profile_photo_path ? Storage::url($owner->profile_photo_path) : null,
                'vehicles_count' => (int) ($owner->vehicles_count ?? 0),
                'avg_rating' => $avg,
                'total_reviews' => $total,
                'about_owner' => $owner->about_owner,
            ],
            'selected_vehicle' => $selectedVehicle ? [
                'id' => $selectedVehicle->id,
                'name' => $selectedVehicle->name,
                'license_plate' => $selectedVehicle->license_plate,
                'color' => $selectedVehicle->color,
                'year_model' => $selectedVehicle->year_model,
            ] : null,
            'reviews' => $reviews,
            'pagination' => $pagination,
            'legitimacy_proofs' => $proofs,
        ]);
    }

    private function parsePerPage($raw): ?int
    {
        $rawStr = strtolower(trim((string) $raw));
        if ($rawStr === 'all') {
            return null;
        }
        $v = (int) $raw;
        return in_array($v, [10, 20, 50], true) ? $v : 10;
    }

    private function mapReview(Review $r): array
    {
        return [
            'id' => $r->id,
            'vehicle' => [
                'id' => $r->vehicle?->id,
                'name' => $r->vehicle?->name,
                'license_plate' => $r->vehicle?->license_plate,
                'color' => $r->vehicle?->color,
                'year_model' => $r->vehicle?->year_model,
            ],
            'reviewer' => [
                'id' => $r->reviewer?->id,
                'name' => $r->reviewer?->name,
            ],
            'rating' => (int) $r->rating,
            'comment' => (string) $r->comment,
            'created_at' => $r->created_at?->toIso8601String(),
        ];
    }
}
