@php
    $consentRaw = request()->cookie('aar_cookie_consent');
    $consent = null;
    if (is_string($consentRaw) && $consentRaw !== '') {
        $decoded = json_decode($consentRaw, true);
        if (is_array($decoded)) {
            $expiresAt = isset($decoded['expires_at']) ? strtotime((string) $decoded['expires_at']) : null;
            if (!$expiresAt || $expiresAt >= time()) {
                $consent = $decoded;
            }
        }
    }
    $needsConsent = auth()->check() && !$consent;
@endphp

@if($needsConsent)
    <div id="aarCookieConsentModal" style="display:flex; position:fixed; inset:0; z-index:200600; align-items:center; justify-content:center; background: rgba(2,6,23,0.75); padding: 20px;">
        <div style="width:min(720px, calc(100vw - 40px)); background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow:hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35);">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Cookie Preferences</div>
                <a href="{{ url('/privacy-policy') }}" style="color:#f59e0b; font-weight: 900; text-decoration:none;">Policy</a>
            </div>
            <div style="padding: 16px; background: #ffffff;">
                <div style="color:#0f172a; font-weight: 700; line-height: 1.55;">
                    We use cookies to keep your session secure and to improve your experience. Essential cookies are always enabled. You can choose whether to allow analytics and marketing cookies. You can change this anytime in Profile → Cookie Preferences.
                </div>

                <div style="margin-top: 14px; display:grid; grid-template-columns: 1fr; gap: 10px;">
                    <div style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#f8fafc;">
                        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                            <div>
                                <div style="font-weight: 900; color:#0f172a;">Essential</div>
                                <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Login/session security, CSRF protection. Always on.</div>
                            </div>
                            <input type="checkbox" checked disabled style="width: 18px; height: 18px;">
                        </div>
                    </div>

                    <label style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#ffffff; display:flex; justify-content:space-between; gap:12px; align-items:center; cursor:pointer;">
                        <div>
                            <div style="font-weight: 900; color:#0f172a;">Analytics</div>
                            <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Helps us understand usage and improve the app.</div>
                        </div>
                        <input id="aarCookieAnalytics" type="checkbox" style="width: 18px; height: 18px;">
                    </label>

                    <label style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#ffffff; display:flex; justify-content:space-between; gap:12px; align-items:center; cursor:pointer;">
                        <div>
                            <div style="font-weight: 900; color:#0f172a;">Marketing</div>
                            <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Enables third-party services and marketing personalization.</div>
                        </div>
                        <input id="aarCookieMarketing" type="checkbox" style="width: 18px; height: 18px;">
                    </label>
                </div>

                <div id="aarCookieConsentError" style="display:none; margin-top: 12px; color:#b91c1c; font-weight: 900;"></div>

                <div style="margin-top: 16px; display:flex; gap: 10px; flex-wrap: wrap; justify-content:flex-end;">
                    <button type="button" id="aarCookieEssentialOnlyBtn" class="btn btn-outline" style="padding: 10px 14px;">Essential Only</button>
                    <button type="button" id="aarCookieSaveBtn" class="btn btn-primary" style="padding: 10px 14px;">Accept Selected</button>
                    <button type="button" id="aarCookieAcceptAllBtn" class="btn btn-primary" style="padding: 10px 14px; background:#f59e0b; color:#0f172a;">Accept All</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('aarCookieConsentModal');
            const analytics = document.getElementById('aarCookieAnalytics');
            const marketing = document.getElementById('aarCookieMarketing');
            const err = document.getElementById('aarCookieConsentError');
            const acceptAllBtn = document.getElementById('aarCookieAcceptAllBtn');
            const saveBtn = document.getElementById('aarCookieSaveBtn');
            const essentialOnlyBtn = document.getElementById('aarCookieEssentialOnlyBtn');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const setError = (msg) => {
                if (!err) return;
                if (!msg) { err.style.display = 'none'; err.textContent = ''; return; }
                err.textContent = msg;
                err.style.display = 'block';
            };

            const post = async (url, payload) => {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(payload || {})
                });
                const ct = res.headers.get('content-type') || '';
                const isJson = ct.includes('application/json');
                const data = isJson ? await res.json().catch(() => null) : null;
                if (!res.ok) {
                    const msg = data && data.message ? String(data.message) : 'request_failed';
                    throw new Error(msg);
                }
                return data;
            };

            const finish = () => {
                if (modal) modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                window.location.reload();
            };

            const submit = async (prefs) => {
                setError('');
                if (!navigator.cookieEnabled) {
                    setError('Cookies are disabled in your browser. This application requires cookies for login and preferences.');
                    return;
                }
                try {
                    if (window.AARLoading) window.AARLoading.show('Saving cookie preferences…', 'Applying your consent settings…');
                    await post(@json(route('cookie-consent.store')), prefs);
                    finish();
                } catch (e) {
                    const msg = e && e.message && e.message !== 'request_failed' ? e.message : 'Unable to save cookie preferences. Please try again or check your browser privacy settings.';
                    setError(msg);
                } finally {
                    if (window.AARLoading) window.AARLoading.hide();
                }
            };

            document.body.style.overflow = 'hidden';

            acceptAllBtn?.addEventListener('click', () => submit({ analytics: true, marketing: true }));
            saveBtn?.addEventListener('click', () => submit({ analytics: !!analytics?.checked, marketing: !!marketing?.checked }));
            essentialOnlyBtn?.addEventListener('click', () => submit({ analytics: false, marketing: false }));

            window.AARCookieConsent = window.AARCookieConsent || {};
            window.AARCookieConsent.open = function () {
                if (modal) modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };
        })();
    </script>
@else
    <script>
        (function () {
            window.AARCookieConsent = window.AARCookieConsent || {};
            window.AARCookieConsent.open = function () {
                window.location.href = @json(url('/profile'));
            };
        })();
    </script>
@endif
