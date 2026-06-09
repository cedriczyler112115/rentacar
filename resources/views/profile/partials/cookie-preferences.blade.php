@php
    $consentRaw = request()->cookie('aar_cookie_consent');
    $consent = null;
    if (is_string($consentRaw) && $consentRaw !== '') {
        $decoded = json_decode($consentRaw, true);
        if (is_array($decoded)) {
            $consent = $decoded;
        }
    }
    $analytics = (bool) ($consent['analytics'] ?? false);
    $marketing = (bool) ($consent['marketing'] ?? false);
@endphp

<section>
    <header>
        <h2 style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">Cookie Preferences</h2>
        <p style="margin-top: 6px; color:#64748b; font-weight: 700;">Manage your consent choices for optional cookies.</p>
    </header>

    <div style="margin-top: 14px; display:grid; gap: 10px;">
        <div style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#f8fafc;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                <div>
                    <div style="font-weight: 900; color:#0f172a;">Essential</div>
                    <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Always enabled for secure login and core features.</div>
                </div>
                <input type="checkbox" checked disabled style="width: 18px; height: 18px;">
            </div>
        </div>

        <form method="POST" action="{{ route('cookie-consent.update') }}" style="display:grid; gap: 10px;">
            @csrf
            @method('PATCH')

            <label style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#ffffff; display:flex; justify-content:space-between; gap:12px; align-items:center; cursor:pointer;">
                <div>
                    <div style="font-weight: 900; color:#0f172a;">Analytics</div>
                    <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Helps us improve performance and features.</div>
                </div>
                <input type="checkbox" name="analytics" value="1" {{ $analytics ? 'checked' : '' }} style="width: 18px; height: 18px;">
            </label>

            <label style="border:1px solid #e2e8f0; border-radius: 12px; padding: 12px; background:#ffffff; display:flex; justify-content:space-between; gap:12px; align-items:center; cursor:pointer;">
                <div>
                    <div style="font-weight: 900; color:#0f172a;">Marketing</div>
                    <div style="margin-top:4px; color:#64748b; font-weight: 700; font-size: 0.95rem;">Enables third-party and marketing personalization.</div>
                </div>
                <input type="checkbox" name="marketing" value="1" {{ $marketing ? 'checked' : '' }} style="width: 18px; height: 18px;">
            </label>

            <div style="display:flex; gap: 10px; flex-wrap: wrap; align-items:center; justify-content:flex-end;">
                <a href="{{ url('/privacy-policy') }}" class="btn btn-outline" style="padding: 10px 14px;">View Policy</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 14px;">Save Preferences</button>
            </div>
        </form>
    </div>
</section>

