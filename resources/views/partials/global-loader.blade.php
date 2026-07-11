<div id="aarGlobalLoader" style="display:none; position:fixed; inset:0; z-index:300000; align-items:center; justify-content:center; background: rgba(2,6,23,0.75); backdrop-filter: blur(2px);">
    <div style="width: min(420px, calc(100vw - 40px)); background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 26px 22px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column; align-items:center; text-align:center;">
        <div style="position: relative; width: 118px; height: 118px; display:flex; align-items:center; justify-content:center;">
            <div style="position:absolute; inset:0; border-radius:999px; border: 5px solid rgba(245,158,11,0.18); border-top-color: var(--accent, #f59e0b); border-right-color: rgba(15,23,42,0.8); animation: aarSpin 1s linear infinite;"></div>
            <div style="position:relative; z-index:1; width: 84px; height: 84px; border-radius:999px; background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%); border: 1px solid #fde68a; box-shadow: 0 10px 24px rgba(15,23,42,0.12); display:flex; align-items:center; justify-content:center; padding: 14px;">
                <img src="{{ asset('images/logo/logo.png') }}" alt="AARACC Logo" style="max-width:100%; max-height:100%; width:auto; height:auto;" onerror="this.style.display='none'">
            </div>
        </div>
        <div style="min-width:0; width:100%; margin-top: 14px;">
            <div id="aarGlobalLoaderTitle" style="font-weight: 900; color:#0f172a; font-size: 1rem;">Please wait while we load your content...</div>
            <div id="aarGlobalLoaderSub" style="margin-top: 6px; color:#64748b; font-weight: 800; font-size: 0.95rem;">Please wait.</div>
        </div>
    </div>
</div>

<style>
    @keyframes aarSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
    (function () {
        const el = document.getElementById('aarGlobalLoader');
        const titleEl = document.getElementById('aarGlobalLoaderTitle');
        const subEl = document.getElementById('aarGlobalLoaderSub');
        if (!el || !titleEl || !subEl) return;

        const state = {
            count: 0,
            timer: null,
            maxMs: 60000,
        };

        const normalize = (v, fallback) => {
            const s = typeof v === 'string' ? v : '';
            return s.trim() ? s : fallback;
        };

        const showInternal = (label, subLabel, opts) => {
            state.count = Math.max(0, state.count) + 1;
            titleEl.textContent = normalize(label, 'Loading…');
            subEl.textContent = normalize(subLabel, 'Please wait.');
            el.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            const maxMs = (opts && typeof opts.maxMs === 'number') ? opts.maxMs : state.maxMs;
            if (state.timer) clearTimeout(state.timer);
            state.timer = setTimeout(() => {
                state.count = 0;
                el.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, Math.max(1000, maxMs));
        };

        const hideInternal = () => {
            state.count = Math.max(0, state.count - 1);
            if (state.count > 0) return;
            state.count = 0;
            if (state.timer) clearTimeout(state.timer);
            state.timer = null;
            el.style.display = 'none';
            document.body.style.overflow = 'auto';
        };

        const infer = (url, method) => {
            const u = String(url || '');
            const m = String(method || 'GET').toUpperCase();

            if (u.includes('/cookie-consent')) return { title: 'Saving cookie preferences…', sub: 'Applying your consent settings…' };
            if (u.includes('/admin/dispatching/dispatch')) return { title: 'Dispatching vehicle…', sub: 'Saving booking and sending email…' };
            if (u.includes('/rentals/') && u.includes('/confirm')) return { title: 'Confirming reservation…', sub: 'Updating booking and sending email…' };
            if (u.includes('/rentals/') && u.includes('/cancel-by-renter')) return { title: 'Cancelling booking…', sub: 'Updating booking and sending email…' };
            if (u.includes('/rentals/') && u.includes('/cancel')) return { title: 'Processing request…', sub: 'Updating booking and sending email…' };
            if (u.includes('/rentals/') && u.includes('/complete')) return { title: 'Completing travel…', sub: 'Updating booking and sending email…' };
            if (u.includes('/book/') && m !== 'GET') return { title: 'Booking in progress…', sub: 'Submitting booking…' };
            if (u.includes('/book/')) return { title: 'Loading…', sub: 'Opening booking page…' };

            if (m === 'GET') return { title: 'Loading…', sub: 'Fetching data…' };
            if (m === 'POST') return { title: 'Processing request…', sub: 'Submitting data…' };
            if (m === 'PUT' || m === 'PATCH') return { title: 'Saving data…', sub: 'Updating information…' };
            if (m === 'DELETE') return { title: 'Processing request…', sub: 'Deleting…' };
            return { title: 'Processing request…', sub: 'Please wait.' };
        };

        window.AARLoading = {
            show: function (label, subLabel, opts) { showInternal(label, subLabel, opts); },
            hide: function () { hideInternal(); },
            reset: function () {
                state.count = 0;
                if (state.timer) clearTimeout(state.timer);
                state.timer = null;
                el.style.display = 'none';
                document.body.style.overflow = 'auto';
            },
            infer: infer,
        };

        const setPending = (label, subLabel) => {
            try {
                sessionStorage.setItem('aar_loader_pending', JSON.stringify({
                    t: normalize(label, 'Loading…'),
                    s: normalize(subLabel, 'Please wait.'),
                    at: Date.now(),
                }));
            } catch (e) {}
        };

        const clearPending = () => {
            try { sessionStorage.removeItem('aar_loader_pending'); } catch (e) {}
        };

        (function initPending() {
            try {
                const raw = sessionStorage.getItem('aar_loader_pending');
                if (!raw) return;
                const data = JSON.parse(raw);
                if (!data || typeof data !== 'object') { clearPending(); return; }
                const at = Number(data.at || 0);
                if (!at || (Date.now() - at) > 15000) { clearPending(); return; }
                showInternal(String(data.t || 'Loading…'), String(data.s || 'Please wait.'), { maxMs: 20000 });
            } catch (e) {
                clearPending();
            }
        })();

        window.addEventListener('load', () => {
            clearPending();
            window.AARLoading.reset();
        });

        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                clearPending();
                window.AARLoading.reset();
            }
        });

        document.addEventListener('click', (e) => {
            const a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (!a) return;
            if (a.hasAttribute('data-no-loader')) return;
            if (a.getAttribute('target') === '_blank') return;
            if (a.hasAttribute('download')) return;
            const href = a.getAttribute('href');
            if (!href) return;
            const h = href.trim();
            if (!h || h.startsWith('#') || h.startsWith('javascript:')) return;
            const inferred = infer(h, 'GET');
            showInternal(inferred.title, inferred.sub, { maxMs: 30000 });
            setTimeout(() => {
                if (!e.defaultPrevented) setPending(inferred.title, inferred.sub);
                else window.AARLoading.reset();
            }, 0);
        }, true);

        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || !form.getAttribute) return;
            if (form.hasAttribute('data-no-loader')) return;
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            const action = form.getAttribute('action') || window.location.href;
            const custom = form.getAttribute('data-loader-label');
            const inferred = custom ? { title: custom, sub: 'Please wait.' } : infer(action, method);
            showInternal(inferred.title, inferred.sub, { maxMs: 60000 });
            setTimeout(() => {
                if (!e.defaultPrevented) setPending(inferred.title, inferred.sub);
            }, 0);
        }, true);

        const initJq = () => {
            const $ = window.jQuery || window.$;
            if (!$ || !$.ajax) return;

            $(document).ajaxSend(function (_ev, xhr, settings) {
                const inferred = infer(settings && settings.url ? settings.url : '', settings && settings.type ? settings.type : 'GET');
                showInternal(inferred.title, inferred.sub, { maxMs: 60000 });
                try { xhr.__aar_loader = true; } catch (e) {}
            });

            $(document).ajaxComplete(function (_ev, xhr) {
                try { if (xhr && xhr.__aar_loader) hideInternal(); } catch (e) { hideInternal(); }
            });

            $(document).ajaxError(function () {
                window.AARLoading.reset();
            });
        };

        initJq();
        document.addEventListener('DOMContentLoaded', initJq);

        if (window.fetch && !window.__aar_fetch_wrapped) {
            window.__aar_fetch_wrapped = true;
            const origFetch = window.fetch.bind(window);
            window.fetch = function (input, init) {
                const hasNoLoaderHeader = () => {
                    try {
                        const h = init && init.headers ? init.headers : null;
                        if (!h) return false;
                        if (typeof Headers !== 'undefined' && h instanceof Headers) {
                            const v = h.get('X-AAR-No-Loader') || h.get('x-aar-no-loader');
                            return String(v || '') === '1';
                        }
                        if (Array.isArray(h)) {
                            return h.some(([k, v]) => String(k).toLowerCase() === 'x-aar-no-loader' && String(v) === '1');
                        }
                        if (typeof h === 'object') {
                            const v = h['X-AAR-No-Loader'] || h['x-aar-no-loader'];
                            return String(v || '') === '1';
                        }
                        return false;
                    } catch (e) {
                        return false;
                    }
                };
                const url = (typeof input === 'string') ? input : (input && input.url ? input.url : '');
                const method = (init && init.method) ? init.method : (input && input.method ? input.method : 'GET');
                const inferred = infer(url, method);
                if (!hasNoLoaderHeader()) {
                    showInternal(inferred.title, inferred.sub, { maxMs: 60000 });
                }
                return origFetch(input, init)
                    .then((res) => {
                        if (!hasNoLoaderHeader()) {
                            hideInternal();
                        }
                        return res;
                    })
                    .catch((err) => {
                        if (!hasNoLoaderHeader()) {
                            window.AARLoading.reset();
                        }
                        throw err;
                    });
            };
        }
    })();
</script>
