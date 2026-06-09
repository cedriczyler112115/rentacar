<div class="dispatch-table-wrap">
    <table class="dispatch-table">
        <thead>
            <tr>
                <th style="width: 120px;">Priority</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Last Date Rented</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $vehicle)
                @php
                    $primaryImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first();
                    $priorityRank = $vehicle->last_rented_at ? 2 : 1;
                @endphp
                <tr>
                    <td>
                        @if($priorityRank === 1)
                            <span style="background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">HIGH</span>
                        @else
                            <span style="background: rgba(239,68,68,0.10); color:#991b1b; border: 1px solid rgba(239,68,68,0.25); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">LOW</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            @if($primaryImage)
                                <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $vehicle->name }}" style="width: 56px; height: 42px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0;">
                            @else
                                <div style="width: 56px; height: 42px; border-radius: 10px; background: #f1f5f9; border: 1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight: 900; font-size: 0.75rem;">
                                    No Photo
                                </div>
                            @endif
                            <div>
                                <div class="dispatch-name">{{ $vehicle->name }}</div>
                                <div class="dispatch-sub">
                                    {{ $vehicle->license_plate ?? '—' }}
                                    <span class="dispatch-dot">•</span>
                                    {{ $vehicle->color ?? '—' }}
                                    <span class="dispatch-dot">•</span>
                                    {{ $vehicle->year_model ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($vehicle->user)
                            <a class="dispatch-owner" href="{{ route('admin.users.edit', $vehicle->user) }}">{{ $vehicle->user->name }}</a>
                            <div class="dispatch-sub">{{ $vehicle->user->contact_number ?? '—' }}</div>
                        @else
                            <div class="dispatch-owner">—</div>
                        @endif
                    </td>
                    <td>
                        @if($vehicle->last_rented_at)
                            <div class="dispatch-owner">{{ \Carbon\Carbon::parse($vehicle->last_rented_at)->format('F j, Y') }}</div>
                        @else
                            <div class="dispatch-sub">Never</div>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <button type="button" class="btn btn-primary dispatch-open-btn" data-vehicle-id="{{ $vehicle->id }}" style="padding: 8px 12px; font-size: 0.9rem;">
                            Dispatch
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="dispatch-empty-row">
                        <div class="dispatch-empty-title">No available vehicles</div>
                        <div class="dispatch-empty-sub">Try selecting another tab.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
