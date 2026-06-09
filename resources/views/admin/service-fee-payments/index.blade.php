<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Service Fee Payments') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Review service fee payment records.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <div style="font-weight: 900; color: var(--primary); font-size: 1.05rem;">Service Fee Payments</div>
                        <div style="color:#64748b; font-weight: 700; margin-top: 6px;">Track monthly service fee payments with proof of transaction.</div>
                    </div>
                    <div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                        <a href="{{ route('admin.service-fee-payments.members', array_filter(['year' => $year, 'month' => $month])) }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">
                            Show Paid/Unpaid
                        </a>
                        <button type="button" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('createPaymentModal').style.display='flex'">
                            + Add Payment
                        </button>
                    </div>
                </div>

                <form id="serviceFeeFilters" method="GET" action="{{ route('admin.service-fee-payments.index') }}" style="margin-top: 16px; display:flex; gap: 10px; flex-wrap: wrap; align-items: end;">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Year</label>
                        <select id="filterYear" name="year" style="width: 160px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            <option value="">All</option>
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ (int)($year ?? 0) === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Month</label>
                        <select id="filterMonth" name="month" style="width: 180px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            <option value="">All</option>
                            @foreach($monthNames as $k => $v)
                                <option value="{{ $k }}" {{ (int)($month ?? 0) === (int)$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="table-scroll" style="margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 12px; overflow: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 880px;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">Period</th>
                                <th style="padding: 12px 14px;">Amount</th>
                                <th style="padding: 12px 14px;">Uploaded By</th>
                                <th style="padding: 12px 14px;">Vehicles</th>
                                <th style="padding: 12px 14px;">Proof</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                                @php
                                    $monthLabel = $monthNames[(int)$p->month] ?? ('Month ' . $p->month);
                                @endphp
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: #0f172a;">{{ $monthLabel }} {{ $p->year }}</div>
                                        <div style="color:#64748b; font-weight: 800; font-size: 0.85rem; margin-top: 4px;">Added {{ $p->created_at->format('F j, Y') }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: var(--accent);">₱{{ number_format($p->amount, 2) }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <div style="font-weight: 900; color: #0f172a;">{{ $p->user->name ?? 'N/A' }}</div>
                                        <div style="color:#64748b; font-weight: 800; font-size: 0.85rem; margin-top: 4px;">{{ $p->user->email ?? '' }}</div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <div style="display:inline-flex; align-items:center; justify-content:center; min-width: 44px; padding: 6px 10px; border-radius: 999px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 900; color:#0f172a;">
                                            {{ (int)($p->user->vehicles_count ?? 0) }}
                                        </div>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        @if($p->proof_path)
                                            <button type="button" onclick="openProofModal('{{ Storage::url($p->proof_path) }}')" style="display:inline-flex; align-items:center; gap:8px; font-weight: 900; color: var(--primary); border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 999px; background: white;">
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
                                                onclick='openEditPayment({{ $p->id }}, {{ (int)$p->year }}, {{ (int)$p->month }}, "{{ number_format((float)$p->amount, 2, '.', '') }}")'>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.service-fee-payments.destroy', $p) }}" class="confirm-delete" style="display:inline;">
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
                                        <div style="font-weight: 900; color: var(--primary);">No payments yet</div>
                                        <div style="font-weight: 800; color: #64748b; margin-top: 6px;">Add a record to start tracking service fee payments.</div>
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

    <div id="createPaymentModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: min(640px, 100%); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 25px 60px rgba(0,0,0,0.35);">
            <div style="padding: 16px 18px; background: #0f172a; color: white; display:flex; align-items:center; justify-content: space-between;">
                <div style="font-weight: 900; letter-spacing: 0.3px;">Add Payment</div>
                <button type="button" onclick="document.getElementById('createPaymentModal').style.display='none'" style="background: transparent; border: 0; color: white; font-weight: 900; font-size: 18px; cursor: pointer;">×</button>
            </div>
            <form method="POST" action="{{ route('admin.service-fee-payments.store') }}" enctype="multipart/form-data" style="padding: 18px;">
                @csrf
                <div class="admin-form-grid-2">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Year</label>
                        <input type="number" name="year" min="2000" max="2100" required value="{{ old('year', now()->year) }}" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        @error('year')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Month</label>
                        <select name="month" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            @php $mNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December']; @endphp
                            @foreach($mNames as $k => $v)
                                <option value="{{ $k }}" {{ (string)old('month', now()->month) === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        @error('month')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Amount</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    @error('amount')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Proof of Payment / Transaction</label>
                    <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                    @error('proof')<div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div>@enderror
                    <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.85rem;">Max 5MB. JPG/PNG/PDF.</div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 18px;">
                    <button type="button" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('createPaymentModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPaymentModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: min(640px, 100%); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 25px 60px rgba(0,0,0,0.35);">
            <div style="padding: 16px 18px; background: #0f172a; color: white; display:flex; align-items:center; justify-content: space-between;">
                <div style="font-weight: 900; letter-spacing: 0.3px;">Edit Payment</div>
                <button type="button" onclick="document.getElementById('editPaymentModal').style.display='none'" style="background: transparent; border: 0; color: white; font-weight: 900; font-size: 18px; cursor: pointer;">×</button>
            </div>
            <form id="editPaymentForm" method="POST" enctype="multipart/form-data" style="padding: 18px;">
                @csrf
                @method('PUT')
                <div class="admin-form-grid-2">
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Year</label>
                        <input type="number" id="edit_year" name="year" min="2000" max="2100" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    </div>
                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Month</label>
                        <select id="edit_month" name="month" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            @foreach($mNames as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Amount</label>
                    <input type="number" id="edit_amount" name="amount" step="0.01" min="0" required style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px;">
                </div>

                <div style="margin-top: 14px;">
                    <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Replace Proof (Optional)</label>
                    <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                    <div style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.85rem;">Max 5MB. JPG/PNG/PDF.</div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 18px;">
                    <button type="button" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;" onclick="document.getElementById('editPaymentModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="proofModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.85); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeProofModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; width: 100%; max-width: 900px; background: #0b1220; border: 1px solid #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 18px 35px rgba(0,0,0,0.25); z-index: 1;">
            <div style="display:flex; justify-content: space-between; align-items:center; padding: 12px 16px; background: rgba(2, 6, 23, 0.65); border-bottom: 1px solid #1e293b;">
                <div style="color: white; font-weight: 800;">Proof of Payment</div>
                <button type="button" onclick="closeProofModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="position: relative; width: 100%; height: min(75vh, 620px); background: #0b1220; display:flex; align-items:center; justify-content:center; padding: 10px;">
                <button type="button" onclick="prevProof()" id="proofPrevBtn" style="position:absolute; left: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 999px; font-size: 1.5rem; cursor: pointer; display:none;">&lsaquo;</button>
                <img id="proofModalImage" src="" alt="Proof image" style="max-width: 100%; max-height: 100%; object-fit: contain; display:none;">
                <iframe id="proofModalPdf" src="" style="width:100%; height:100%; border:0; display:none; background:white;"></iframe>
                <div id="proofModalFallback" style="display:none; color:#94a3b8; font-weight:700; text-align:center; padding: 40px;">
                    Unable to preview this file.
                    <div style="margin-top: 12px;">
                        <a id="proofModalLink" href="#" target="_blank" style="display:inline-flex; align-items:center; gap:8px; font-weight: 900; color: white; border: 1px solid rgba(255,255,255,0.2); padding: 10px 14px; border-radius: 999px; background: rgba(255,255,255,0.08);">
                            Open in new tab
                        </a>
                    </div>
                </div>
                <button type="button" onclick="nextProof()" id="proofNextBtn" style="position:absolute; right: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 999px; font-size: 1.5rem; cursor: pointer; display:none;">&rsaquo;</button>
            </div>
        </div>
    </div>

    <script>
        let proofSet = [];
        let proofIndex = 0;

        function isPdfUrl(url) {
            const u = (url || '').toLowerCase().split('?')[0];
            return u.endsWith('.pdf');
        }

        function renderProof() {
            const img = document.getElementById('proofModalImage');
            const pdf = document.getElementById('proofModalPdf');
            const fallback = document.getElementById('proofModalFallback');
            const link = document.getElementById('proofModalLink');
            const prev = document.getElementById('proofPrevBtn');
            const next = document.getElementById('proofNextBtn');

            const url = proofSet[proofIndex] || '';
            link.href = url || '#';

            const showNav = proofSet.length > 1;
            prev.style.display = showNav ? 'inline-flex' : 'none';
            next.style.display = showNav ? 'inline-flex' : 'none';

            img.style.display = 'none';
            pdf.style.display = 'none';
            fallback.style.display = 'none';

            if (!url) {
                fallback.style.display = 'block';
                return;
            }

            if (isPdfUrl(url)) {
                pdf.src = url;
                pdf.style.display = 'block';
                return;
            }

            img.src = url;
            img.onload = function () {
                img.style.display = 'block';
            };
            img.onerror = function () {
                fallback.style.display = 'block';
            };
        }

        function openProofModal(urlOrArray) {
            proofSet = Array.isArray(urlOrArray) ? urlOrArray : [urlOrArray];
            proofIndex = 0;
            document.getElementById('proofModal').style.display = 'flex';
            renderProof();
        }

        function closeProofModal() {
            document.getElementById('proofModal').style.display = 'none';
            document.getElementById('proofModalImage').src = '';
            document.getElementById('proofModalPdf').src = '';
            proofSet = [];
            proofIndex = 0;
        }

        function prevProof() {
            if (proofSet.length <= 1) return;
            proofIndex = (proofIndex - 1 + proofSet.length) % proofSet.length;
            renderProof();
        }

        function nextProof() {
            if (proofSet.length <= 1) return;
            proofIndex = (proofIndex + 1) % proofSet.length;
            renderProof();
        }

        function openEditPayment(id, year, month, amount) {
            const form = document.getElementById('editPaymentForm');
            form.action = '{{ route('admin.service-fee-payments.update', ['payment' => '__ID__']) }}'.replace('__ID__', id);
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_month').value = month;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('editPaymentModal').style.display = 'flex';
        }

        (function () {
            if (!window.jQuery) return;
            const $form = $('#serviceFeeFilters');
            const $year = $('#filterYear');
            const $month = $('#filterMonth');

            function submitFilters() {
                $form.trigger('submit');
            }

            $month.on('change', submitFilters);
            $year.on('change', submitFilters);
        })();
    </script>
</x-member-layout>
