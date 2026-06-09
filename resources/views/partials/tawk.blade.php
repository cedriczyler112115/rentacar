@php
    $tawkPropertyId = config('services.tawk.property_id');
    $tawkWidgetId = config('services.tawk.widget_id');
    $tawkPos = config('services.tawk.position', 'br');
    $tawkX = (int) config('services.tawk.x_offset', 20);
    $tawkY = (int) config('services.tawk.y_offset', 20);
@endphp

@if($tawkPropertyId && $tawkWidgetId)
    <script>
        (function () {
            try {
                window.Tawk_API = window.Tawk_API || {};
                window.Tawk_LoadStart = new Date();

                window.Tawk_API.customStyle = {
                    visibility: {
                        desktop: { position: @json($tawkPos), xOffset: @json($tawkX), yOffset: @json($tawkY) },
                        mobile: { position: @json($tawkPos), xOffset: 0, yOffset: 0 }
                    }
                };

                window.Tawk_API.onLoad = function () {
                    document.dispatchEvent(new CustomEvent('tawk:load'));
                };
                window.Tawk_API.onChatStarted = function () {
                    document.dispatchEvent(new CustomEvent('tawk:chat_started'));
                };
                window.Tawk_API.onChatEnded = function () {
                    document.dispatchEvent(new CustomEvent('tawk:chat_ended'));
                };
                window.Tawk_API.onChatMinimized = function () {
                    document.dispatchEvent(new CustomEvent('tawk:minimized'));
                };
                window.Tawk_API.onChatMaximized = function () {
                    document.dispatchEvent(new CustomEvent('tawk:maximized'));
                };

                const s1 = document.createElement('script');
                s1.async = true;
                s1.src = 'https://embed.tawk.to/' + @json($tawkPropertyId) + '/' + @json($tawkWidgetId);
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s1.onerror = function () {
                    document.dispatchEvent(new CustomEvent('tawk:error'));
                };

                const s0 = document.getElementsByTagName('script')[0];
                if (s0 && s0.parentNode) {
                    s0.parentNode.insertBefore(s1, s0);
                } else {
                    document.head.appendChild(s1);
                }
            } catch (e) {
                document.dispatchEvent(new CustomEvent('tawk:error'));
            }
        })();
    </script>
@endif

