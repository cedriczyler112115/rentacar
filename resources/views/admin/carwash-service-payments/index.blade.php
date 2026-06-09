<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Carwash Service') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Track carwash service fee payments with photo proof.</p>
            </div>
            <a href="{{ route('admin.service-fee-payments.index') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">
                ← Back to Service Fee
            </a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Carwash Service Payments</div>
                        <div style="color:#64748b; font-weight: 700; margin-top: 6px;">Date, amount paid, and photo proof of the vehicle.</div>
                    </div>
                    <button type="button" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('createCarwashModal').style.display='flex'">
                        + Add Payment
                    </button>
                </div>

                <div class="table-scroll" style="margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 860px;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">Carwash Date</th>
                                <th style="padding: 12px 14px;">Amount Paid</th>
                                <th style="padding: 12px 14px;">Uploaded By</th>
                                <th style="padding: 12px 14px;">Vehicle</th>
                                <th style="padding: 12px 14px;">Proof</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: #0f172a;">{{ $p->service_date?->format('F j, Y') }}</div>
                                        <div style="color:#64748b; font-weight: 500; font-size: 0.85rem; margin-top: 4px;">Added {{ $p->created_at->format('F j, Y') }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: var(--accent);">₱{{ number_format($p->amount_paid, 2) }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: #0f172a;">{{ $p->user->name ?? 'N/A' }}</div>
                                        <div style="color:#64748b; font-weight: 500; font-size: 0.85rem; margin-top: 4px;">{{ $p->user->email ?? '' }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        @php
                                            $vehicleName = $p->vehicle->name ?? 'N/A';
                                            $vehiclePlate = $p->vehicle->license_plate ?? '—';
                                        @endphp
                                        <div style="font-weight: 900; color:#0f172a;">{{ $vehicleName }}</div>
                                        <div style="color:#64748b; font-weight: 500; font-size: 0.85rem; margin-top: 4px;">{{ $vehiclePlate }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        @if($p->vehicle_proof_path)
                                            <button type="button" onclick="openProofModal('{{ Storage::url($p->vehicle_proof_path) }}')" style="display:inline-flex; align-items:center; gap:8px; font-weight: 900; color: var(--primary); border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 999px; background: white;">
                                                View Proof
                                            </button>
                                        @else
                                            <span style="color:#94a3b8; font-weight: 800;">None</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; text-align:right;">
                                        <div style="display:inline-flex; gap: 10px; align-items:center;">
                                            <button type="button"
                                                class="btn btn-outline"
                                                style="padding: 8px 12px; font-size: 0.9rem;"
                                                onclick='openEditCarwash({{ $p->id }}, {{ (int)($p->vehicle_id ?? 0) }}, "{{ $p->service_date?->format('Y-m-d') }}", "{{ number_format((float)$p->amount_paid, 2, '.', '') }}")'>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.carwash-service-payments.destroy', $p) }}" class="confirm-delete" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn" style="background:#fee2e2; border: 1px solid #fecaca; color:#991b1b; padding: 8px 12px; border-radius: 10px; font-weight: 900; font-size: 0.9rem;">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 26px; text-align:center; background:#f8fafc;">
                                        <div style="font-weight: 900; color: var(--primary);">No carwash payments yet</div>
                                        <div style="font-weight: 800; color: #64748b; margin-top: 6px;">Add a record to start tracking carwash service fee payments.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 16px;">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="createCarwashModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: min(640px, 100%); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 25px 60px rgba(0,0,0,0.35);">
            <div style="padding: 16px 18px; background: #0f172a; color: white; display:flex; align-items:center; justify-content: space-between;">
                <div style="font-weight: 900; letter-spacing: 0.3px;">Add Carwash Payment</div>
                <button type="button" onclick="document.getElementById('createCarwashModal').style.display='none'" style="background: transparent; border: 0; color: white; font-weight: 900; font-size: 18px; cursor: pointer;">×</button>
            </div>
            <form method="POST" action="{{ route('admin.carwash-service-payments.store') }}" enctype="multipart/form-data" style="padding: 18px;">
                @csrf
                <div style="margin-bottom: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Car</label>
                    <select name="vehicle_id" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        <option value="" selected disabled>Select vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ (string)old('vehicle_id') === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->name }} • {{ $v->license_plate ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                </div>
                <div class="admin-form-grid-2">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Date</label>
                        <input type="date" name="service_date" required value="{{ old('service_date', now()->toDateString()) }}" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        @error('service_date')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Amount Paid</label>
                        <input type="number" name="amount_paid" step="0.01" min="0" required value="{{ old('amount_paid') }}" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        @error('amount_paid')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Vehicle Proof (Photo)</label>
                    <input type="file" name="vehicle_proof" accept=".jpg,.jpeg,.png" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                    @error('vehicle_proof')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.85rem;">Max 5MB. JPG/PNG.</div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 18px;">
                    <button type="button" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('createCarwashModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editCarwashModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: min(640px, 100%); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 25px 60px rgba(0,0,0,0.35);">
            <div style="padding: 16px 18px; background: #0f172a; color: white; display:flex; align-items:center; justify-content: space-between;">
                <div style="font-weight: 900; letter-spacing: 0.3px;">Edit Carwash Payment</div>
                <button type="button" onclick="document.getElementById('editCarwashModal').style.display='none'" style="background: transparent; border: 0; color: white; font-weight: 900; font-size: 18px; cursor: pointer;">×</button>
            </div>
            <form id="editCarwashForm" method="POST" enctype="multipart/form-data" style="padding: 18px;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Car</label>
                    <select id="edit_vehicle_id" name="vehicle_id" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        <option value="" selected disabled>Select vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->name }} • {{ $v->license_plate ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-form-grid-2">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Date</label>
                        <input type="date" id="edit_service_date" name="service_date" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Amount Paid</label>
                        <input type="number" id="edit_amount_paid" name="amount_paid" step="0.01" min="0" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Replace Vehicle Proof (Optional)</label>
                    <input type="file" name="vehicle_proof" accept=".jpg,.jpeg,.png" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                    <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.85rem;">Max 5MB. JPG/PNG.</div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 18px;">
                    <button type="button" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('editCarwashModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="proofModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.85); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeProofModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; width: 100%; max-width: 900px; background: #0b1220; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 18px 35px rgba(0,0,0,0.25); z-index: 1;">
            <div style="display:flex; justify-content: space-between; align-items:center; padding: 12px 16px; background: rgba(2, 6, 23, 0.65); border-bottom: 1px solid #1e293b;">
                <div style="color: white; font-weight: 800;">Vehicle Proof</div>
                <button type="button" onclick="closeProofModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="position: relative; width: 100%; height: min(75vh, 620px); background: #0b1220; display:flex; align-items:center; justify-content:center; padding: 10px;">
                <img id="proofModalImage" src="" alt="Proof image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
        </div>
    </div>

    <script>
        function openProofModal(url) {
            document.getElementById('proofModal').style.display = 'flex';
            document.getElementById('proofModalImage').src = url || '';
        }

        function closeProofModal() {
            document.getElementById('proofModal').style.display = 'none';
            document.getElementById('proofModalImage').src = '';
        }

        function openEditCarwash(id, vehicleId, date, amount) {
            const form = document.getElementById('editCarwashForm');
            form.action = '{{ route('admin.carwash-service-payments.update', ['payment' => '__ID__']) }}'.replace('__ID__', id);
            document.getElementById('edit_vehicle_id').value = vehicleId && vehicleId > 0 ? vehicleId : '';
            document.getElementById('edit_service_date').value = date;
            document.getElementById('edit_amount_paid').value = amount;
            document.getElementById('editCarwashModal').style.display = 'flex';
        }
    </script>
</x-member-layout>
