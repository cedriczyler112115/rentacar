<x-guest-layout>
    <div style="width: 100%; max-width: 820px; margin: 0 auto; padding: 10px 0;">
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; color:#0f172a;">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center;">
                <div>
                    <div style="font-weight: 900; font-size: 1.35rem;">Privacy & Cookie Policy</div>
                    <div style="margin-top: 6px; color:#64748b; font-weight: 700;">Last updated: {{ now()->format('M d, Y') }}</div>
                </div>
                <a href="{{ url('/') }}" style="text-decoration:none; font-weight: 900; color:#f59e0b;">Back to Home</a>
            </div>

            <div style="margin-top: 18px; color:#0f172a; font-weight: 600; line-height: 1.65;">
                This policy explains how Auto Amegos Rent-a-Car (“AARAAC”) uses cookies and similar technologies. Essential cookies are required for core site functionality such as secure login and session management. Optional cookies are used only when you consent.
            </div>

            <div style="margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
                <div style="font-weight: 900; font-size: 1.1rem;">Cookie Categories</div>
                <div style="margin-top: 10px; display:grid; gap: 10px;">
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900;">Essential (Always On)</div>
                        <div style="margin-top: 6px; color:#334155; font-weight: 600;">
                            Used to keep you logged in securely, protect against CSRF attacks, and maintain application state.
                        </div>
                    </div>
                    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900;">Analytics (Optional)</div>
                        <div style="margin-top: 6px; color:#334155; font-weight: 600;">
                            Helps us understand usage patterns to improve features and performance.
                        </div>
                    </div>
                    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
                        <div style="font-weight: 900;">Marketing (Optional)</div>
                        <div style="margin-top: 6px; color:#334155; font-weight: 600;">
                            Enables third-party services and marketing personalization where applicable.
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
                <div style="font-weight: 900; font-size: 1.1rem;">Cookies We Use</div>
                <div style="margin-top: 10px; overflow-x:auto; border:1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#0f172a; color:white;">
                                <th style="padding: 12px 14px; text-align:left; font-weight: 900;">Name</th>
                                <th style="padding: 12px 14px; text-align:left; font-weight: 900;">Category</th>
                                <th style="padding: 12px 14px; text-align:left; font-weight: 900;">Purpose</th>
                                <th style="padding: 12px 14px; text-align:left; font-weight: 900;">Expiration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 12px 14px; font-weight: 800;">{{ config('session.cookie') }}</td>
                                <td style="padding: 12px 14px; font-weight: 800;">Essential</td>
                                <td style="padding: 12px 14px;">Secure session identifier for authenticated users.</td>
                                <td style="padding: 12px 14px;">{{ (int) config('session.lifetime') }} minutes (idle)</td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 12px 14px; font-weight: 800;">XSRF-TOKEN</td>
                                <td style="padding: 12px 14px; font-weight: 800;">Essential</td>
                                <td style="padding: 12px 14px;">Helps protect forms and requests against CSRF attacks.</td>
                                <td style="padding: 12px 14px;">Session</td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 12px 14px; font-weight: 800;">aar_cookie_consent</td>
                                <td style="padding: 12px 14px; font-weight: 800;">Preference</td>
                                <td style="padding: 12px 14px;">Stores your cookie consent choices (analytics/marketing).</td>
                                <td style="padding: 12px 14px;">365 days</td>
                            </tr>
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 12px 14px; font-weight: 800;">Third-party cookies</td>
                                <td style="padding: 12px 14px; font-weight: 800;">Optional</td>
                                <td style="padding: 12px 14px;">May be set by integrated third-party services depending on your consent.</td>
                                <td style="padding: 12px 14px;">Varies</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
                <div style="font-weight: 900; font-size: 1.1rem;">Manage Preferences</div>
                <div style="margin-top: 8px; color:#334155; font-weight: 600; line-height: 1.65;">
                    After you log in, you can manage your cookie preferences from your Profile page.
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

