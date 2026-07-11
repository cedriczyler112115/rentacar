<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Apply for a Loan') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Submit a new loan application.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Apply for a Loan</div>
                    
                @if ($errors->any())
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 0.95rem; font-weight: 900; color: #0f172a;">Loan Details</h3>
                        @if($isMember)
                            <p style="font-size: 0.85rem; font-weight: 800; color: #64748b; margin-top: 4px;">As a member, your interest rate is 5% diminishing. Maximum loan amount is ₱{{ number_format($maxLoanAmount, 2) }}.</p>
                        @else
                            <p style="font-size: 0.85rem; font-weight: 800; color: #64748b; margin-top: 4px;">As a non-member, your interest rate is 7% diminishing. Collateral is required (minimum 150% value of loan amount), and your selected co-maker must still have remaining 80% allowable member capacity.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="loan_amount" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Loan Amount (₱)</label>
                            <input type="number" name="loan_amount" id="loan_amount" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('loan_amount') }}" required min="1000" {{ $isMember ? 'max='.$maxLoanAmount : '' }}>
                        </div>

                        <div>
                            <label for="term_length_months" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Term Length (Months)</label>
                            <input type="number" name="term_length_months" id="term_length_months" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('term_length_months') }}" required min="1" max="60">
                        </div>
                    </div>

                    @if(!$isMember)
                        <div style="margin-bottom: 20px;">
                            <h3 style="font-size: 0.95rem; font-weight: 900; color: #0f172a;">Co-Maker</h3>
                            <p style="font-size: 0.85rem; font-weight: 800; color: #64748b; margin-top: 4px;">Choose an AARACC member whose remaining allowable amount can cover your requested loan.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label for="co_maker_id" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Co-Maker</label>
                                <select name="co_maker_id" id="co_maker_id" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>
                                    <option value="">Select eligible co-maker...</option>
                                    @forelse($coMakers as $coMaker)
                                        <option
                                            value="{{ $coMaker->id }}"
                                            data-remaining="{{ number_format((float) $coMaker->co_maker_remaining_amount, 2, '.', '') }}"
                                            {{ (string) old('co_maker_id') === (string) $coMaker->id ? 'selected' : '' }}
                                        >
                                            {{ $coMaker->name }} - Remaining ₱{{ number_format((float) $coMaker->co_maker_remaining_amount, 2) }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No eligible co-maker available</option>
                                    @endforelse
                                </select>
                                <p id="coMakerLimitHint" style="font-size: 0.8rem; font-weight: 800; color: #64748b; margin-top: 6px;">
                                    @if(old('co_maker_id'))
                                        Selected co-maker remaining allowable amount will appear here.
                                    @else
                                        Select a co-maker to see the remaining allowable amount.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
                        <div style="margin-bottom: 20px;">
                            <h3 style="font-size: 0.95rem; font-weight: 900; color: #0f172a;">Collateral Details</h3>
                            <p style="font-size: 0.85rem; font-weight: 800; color: #64748b; margin-top: 4px;">Required for non-members.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label for="collateral_type" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Collateral Type</label>
                                <select name="collateral_type" id="collateral_type" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>
                                    <option value="">Select Type...</option>
                                    <option value="vehicle" {{ old('collateral_type') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                                    <option value="land_title" {{ old('collateral_type') == 'land_title' ? 'selected' : '' }}>Land Title</option>
                                    <option value="gadget" {{ old('collateral_type') == 'gadget' ? 'selected' : '' }}>Gadget / Electronics</option>
                                    <option value="jewelry" {{ old('collateral_type') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                                    <option value="other" {{ old('collateral_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="estimated_value" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Estimated Value (₱)</label>
                                <input type="number" name="estimated_value" id="estimated_value" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('estimated_value') }}" required>
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label for="collateral_description" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Collateral Description</label>
                                <textarea name="collateral_description" id="collateral_description" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>{{ old('collateral_description') }}</textarea>
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label for="proof_of_ownership" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Proof of Ownership (File Upload)</label>
                                <input type="file" name="proof_of_ownership" id="proof_of_ownership" style="width: 100%;" required accept=".jpg,.jpeg,.png,.pdf">
                                <p style="font-size: 0.8rem; font-weight: 800; color: #64748b; margin-top: 6px;">Accepts JPG, PNG, PDF. Max 5MB.</p>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                            Submit Loan Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(!$isMember)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const coMakerSelect = document.getElementById('co_maker_id');
                const loanAmountInput = document.getElementById('loan_amount');
                const hint = document.getElementById('coMakerLimitHint');
                if (!coMakerSelect || !loanAmountInput || !hint) return;

                const syncCoMakerLimit = () => {
                    const selected = coMakerSelect.options[coMakerSelect.selectedIndex];
                    const remaining = selected ? Number(selected.getAttribute('data-remaining') || 0) : 0;

                    if (remaining > 0) {
                        loanAmountInput.max = String(remaining);
                        hint.textContent = 'Selected co-maker remaining allowable amount: ₱' + remaining.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    } else {
                        loanAmountInput.removeAttribute('max');
                        hint.textContent = 'Select a co-maker to see the remaining allowable amount.';
                    }
                };

                coMakerSelect.addEventListener('change', syncCoMakerLimit);
                syncCoMakerLimit();
            });
        </script>
    @endif
</x-member-layout>
