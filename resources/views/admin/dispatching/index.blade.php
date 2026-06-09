<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Dispatching') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Dispatch available vehicles by type.</p>
    </x-slot>

    <div class="container" style="padding: 0px 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <style>
                    .dispatch-tabs { display:flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
                    .dispatch-tab { display:inline-flex; align-items:center; gap:10px; padding: 10px 14px; border-radius: 999px; font-weight: 900; border: 1px solid #e2e8f0; background: white; color: var(--primary); cursor: pointer; }
                    .dispatch-tab.active { border-color: rgba(245,158,11,0.35); background: rgba(245,158,11,0.15); color: var(--accent); }
                    .dispatch-table-wrap { border: 1px solid #e2e8f0; border-radius: 12px; overflow: auto; }
                    .dispatch-table { width: 100%; border-collapse: collapse; min-width: 980px; }
                    .dispatch-table thead th { text-align:left; background: #0f172a; color: white; padding: 12px 14px; font-weight: 900; font-size: 0.85rem; }
                    .dispatch-table tbody td { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
                    .dispatch-name { font-weight: 900; color: #0f172a; }
                    .dispatch-owner { font-weight: 900; color: #0f172a; text-decoration: none; }
                    .dispatch-owner:hover { color: var(--accent); }
                    .dispatch-sub { color: #64748b; font-weight: 800; font-size: 0.85rem; margin-top: 4px; }
                    .dispatch-dot { margin: 0 6px; color: #cbd5e1; }
                    .dispatch-empty-row { padding: 26px; border-bottom: 0; background: #f8fafc; text-align:center; }
                    .dispatch-empty-title { font-weight: 900; color: var(--primary); }
                    .dispatch-empty-sub { font-weight: 800; color: #64748b; margin-top: 6px; }
                    .dispatch-loading { padding: 14px 16px; border-radius: 12px; background: rgba(15,23,42,0.04); border: 1px solid #e2e8f0; font-weight: 900; color: #0f172a; margin-bottom: 12px; }
                </style>

                @if(!empty($error))
                    @include('admin.dispatching.partials.error', ['message' => $error])
                @else
                    <div class="dispatch-tabs" id="dispatchTabs">
                        @forelse($types as $type)
                            <a href="{{ route('admin.dispatching.index', ['type_id' => $type->id]) }}"
                               class="dispatch-tab {{ (int)$activeTypeId === (int)$type->id ? 'active' : '' }}"
                               data-type-id="{{ $type->id }}">
                                {{ strtoupper($type->name) }}
                            </a>
                        @empty
                            <div style="padding: 14px 16px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 900; color: #64748b;">
                                No vehicle types configured.
                            </div>
                        @endforelse
                    </div>

                    <div id="dispatchVehicles">
                        @include('admin.dispatching.partials.vehicle_grid', ['vehicles' => $vehicles])
                    </div>

                    <div id="dispatchModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
                        <div style="width: min(980px, 100%); max-height: min(86vh, 820px); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction: column;">
                            <div style="padding: 16px 18px; background: #0f172a; color: white; display:flex; align-items:center; justify-content: space-between;">
                                <div style="font-weight: 900; letter-spacing: 0.3px;">Dispatch Booking</div>
                                <button type="button" id="dispatchModalClose" style="background: transparent; border: 0; color: white; font-weight: 900; font-size: 18px; cursor: pointer;">×</button>
                            </div>
                            <div id="dispatchModalBody" style="padding: 0; overflow:auto; -webkit-overflow-scrolling: touch; flex: 1;"></div>
                        </div>
                    </div>

                    <script>
                        (function () {
                            if (!window.jQuery) return;
                            const $tabs = $('#dispatchTabs');
                            const $vehicles = $('#dispatchVehicles');
                            const $modal = $('#dispatchModal');
                            const $modalBody = $('#dispatchModalBody');

                            function setLoading(isLoading) {
                                if (isLoading) {
                                    if ($('#dispatchLoading').length === 0) {
                                        $vehicles.prepend('<div id="dispatchLoading" class="dispatch-loading">Loading vehicles...</div>');
                                    }
                                    $vehicles.css('opacity', '0.55');
                                } else {
                                    $('#dispatchLoading').remove();
                                    $vehicles.css('opacity', '1');
                                }
                            }

                            function loadVehicles(typeId, pushUrl) {
                                setLoading(true);
                                $.get('{{ route('admin.dispatching.vehicles') }}', { type_id: typeId })
                                    .done(function (html) {
                                        $vehicles.html(html);
                                        if (pushUrl) window.history.replaceState({}, '', pushUrl);
                                    })
                                    .fail(function () {
                                        $vehicles.html(@json(view('admin.dispatching.partials.error', ['message' => 'Failed to load vehicles for this tab. Please try again.'])->render()));
                                    })
                                    .always(function () {
                                        setLoading(false);
                                    });
                            }

                            function openDispatchModal(vehicleId) {
                                $modalBody.html('<div class="dispatch-loading" style="margin: 16px;">Loading booking form...</div>');
                                $modal.css('display', 'flex');
                                $.get('{{ route('admin.dispatching.dispatch-form') }}', { vehicle_id: vehicleId })
                                    .done(function (html) {
                                        $modalBody.html(html);
                                    })
                                    .fail(function () {
                                        $modalBody.html(@json(view('admin.dispatching.partials.error', ['message' => 'Failed to load dispatch form.'])->render()));
                                    });
                            }

                            function closeDispatchModal() {
                                $modal.css('display', 'none');
                                $modalBody.html('');
                            }

                            $('#dispatchModalClose').on('click', closeDispatchModal);
                            $modal.on('click', function (e) {
                                if (e.target === $modal[0]) closeDispatchModal();
                            });

                            $tabs.on('click', 'a[data-type-id]', function (e) {
                                e.preventDefault();
                                const $a = $(this);
                                const typeId = $a.data('typeId');
                                $tabs.find('.dispatch-tab').removeClass('active');
                                $a.addClass('active');
                                const url = $a.attr('href');
                                loadVehicles(typeId, url);
                            });

                            $vehicles.on('click', '.dispatch-open-btn', function () {
                                const vehicleId = $(this).data('vehicleId');
                                openDispatchModal(vehicleId);
                            });

                            $modal.on('submit', '#dispatchBookingForm', function (e) {
                                e.preventDefault();
                                const $form = $(this);
                                const formData = new FormData($form[0]);
                                const $submit = $form.find('button[type="submit"]');

                                $form.find('[data-error]').remove();
                                $.confirm({
                                    title: 'Confirm Dispatch',
                                    content: 'Create and confirm this dispatch booking? An email will be sent to the vehicle owner.',
                                    type: 'orange',
                                    buttons: {
                                        Dispatch: {
                                            btnClass: 'btn-orange',
                                            action: function () {
                                                $submit.prop('disabled', true);
                                                if (window.AARLoading) window.AARLoading.show('Dispatching vehicle…', 'Saving booking and sending email notification…');

                                                $.ajax({
                                                    url: '{{ route('admin.dispatching.dispatch-store') }}',
                                                    method: 'POST',
                                                    data: formData,
                                                    processData: false,
                                                    contentType: false,
                                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                })
                                                .done(function (res) {
                                                    closeDispatchModal();
                                                    const active = $tabs.find('.dispatch-tab.active').data('typeId');
                                                    loadVehicles(active, window.location.href);
                                                    if (res && res.message) {
                                                        if (window.$ && $.alert) {
                                                            $.alert({ title: 'Success', content: res.message });
                                                        } else {
                                                            alert(res.message);
                                                        }
                                                    }
                                                })
                                                .fail(function (xhr) {
                                                    let msg = 'Failed to submit dispatch booking.';
                                                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                                    if (window.$ && $.alert) {
                                                        $.alert({ title: 'Error', content: msg });
                                                    }
                                                    const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : null;
                                                    if (errors) {
                                                        Object.keys(errors).forEach(function (key) {
                                                            const $field = $form.find('[name="' + key + '"]');
                                                            if ($field.length) {
                                                                $field.after('<div data-error style="margin-top:6px; font-weight:800; color:#b91c1c;">' + errors[key][0] + '</div>');
                                                            }
                                                        });
                                                    }
                                                })
                                                .always(function () {
                                                    $submit.prop('disabled', false);
                                                    if (window.AARLoading) window.AARLoading.hide();
                                                });
                                            }
                                        },
                                        Cancel: function () {}
                                    }
                                });
                            });
                        })();
                    </script>
                @endif
            </div>
        </div>
    </div>
</x-member-layout>
