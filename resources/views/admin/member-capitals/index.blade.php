<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Member Capitals Library') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Manage member capital investments.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">Member Capitals</div>
                    <a href="{{ route('admin.member-capitals.create') }}" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Add Member Capital</a>
                </div>

                @if (session('success'))
                    <div style="margin-bottom: 14px; background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">Member</th>
                                <th style="padding: 12px 14px;">Current Capital</th>
                                <th style="padding: 12px 14px;">Status</th>
                                <th style="padding: 12px 14px;">Max Loan (80%)</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($capitals as $capital)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ $capital->user->name }}</td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">₱{{ number_format($capital->current_capital, 2) }}</td>
                                    <td style="padding: 12px 14px;">
                                        @if($capital->status === 'active')
                                            <span style="background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">ACTIVE</span>
                                        @else
                                            <span style="background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">WITHDRAWN</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 900; color:var(--accent);">₱{{ number_format($capital->current_capital * 0.8, 2) }}</td>
                                    <td style="padding: 12px 14px; text-align:right; white-space: nowrap;">
                                        <button
                                            type="button"
                                            class="btn btn-outline js-view-capital-log"
                                            style="padding: 8px 12px; font-size: 0.9rem;"
                                            data-member="{{ $capital->user->name }}"
                                            data-log="{{ base64_encode(json_encode($capital->capital_additions_log ?? [])) }}"
                                        >View</button>
                                        <a href="{{ route('admin.member-capitals.edit', $capital) }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.9rem; margin-left: 6px;">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 20px; text-align:center; color:#64748b; font-weight: 800;">No member capitals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function hasJqueryConfirm() {
                return typeof window.jQuery !== 'undefined' && typeof window.jQuery.confirm === 'function';
            }

            function b64DecodeUnicode(str) {
                try {
                    return decodeURIComponent(Array.prototype.map.call(atob(str), function (c) {
                        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                    }).join(''));
                } catch (e) {
                    try {
                        return atob(str || '');
                    } catch (e2) {
                        return '';
                    }
                }
            }

            function escapeHtml(s) {
                return String(s ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            document.querySelectorAll('.js-view-capital-log').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var member = this.getAttribute('data-member') || 'Member';
                    var raw = b64DecodeUnicode(this.getAttribute('data-log') || '');
                    var items = [];
                    try {
                        items = JSON.parse(raw || '[]') || [];
                    } catch (e) {
                        items = [];
                    }

                    if (!Array.isArray(items)) items = [];

                    items = items.slice().reverse();

                    var content = '';

                    if (!items.length) {
                        content = '<div style="color:#64748b; font-weight: 800;">No capital additions logged yet.</div>';
                    } else {
                        content += '<div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">' +
                            '<table style="width: 100%; border-collapse: collapse;">' +
                                '<thead>' +
                                    '<tr style="text-align:left; background: #0f172a; color: white;">' +
                                        '<th style="padding: 10px 12px;">Date Added</th>' +
                                        '<th style="padding: 10px 12px;">Amount</th>' +
                                        '<th style="padding: 10px 12px;">Created By</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>';

                        items.forEach(function (it) {
                            var dateAdded = escapeHtml(it.date_added || '');
                            var amount = Number(it.amount || 0);
                            var createdByRaw = it.created_by ?? '';
                            var createdBy = escapeHtml(createdByRaw);
                            if (/^\d+$/.test(String(createdByRaw))) {
                                createdBy = 'User #' + escapeHtml(String(createdByRaw));
                            }
                            content += '<tr style="border-bottom: 1px solid #e2e8f0;">' +
                                '<td style="padding: 10px 12px; font-weight: 800;">' + dateAdded + '</td>' +
                                '<td style="padding: 10px 12px; font-weight: 900; color:#16a34a;">₱' + amount.toFixed(2) + '</td>' +
                                '<td style="padding: 10px 12px; font-weight: 800; color:#64748b;">' + createdBy + '</td>' +
                            '</tr>';
                        });

                        content += '</tbody></table></div>';
                    }

                    if (hasJqueryConfirm()) {
                        window.jQuery.confirm({
                            title: 'Capital Additions - ' + escapeHtml(member),
                            content: content,
                            type: 'blue',
                            columnClass: 'col-md-8 col-md-offset-2',
                            buttons: {
                                Close: function () {}
                            }
                        });
                    } else {
                        alert('Capital additions viewer requires the confirmation modal library to be loaded.');
                    }
                });
            });
        });
    </script>
</x-member-layout>
