<section>
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
        <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.12); color: #10b981; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M18 8l-6 6-3-3"/></svg>
        </div>
        <div>
            <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--primary);">{{ __('Upload Proof of Legitemacy') }}</h3>
            <p style="color: #64748b;">Upload up to 15 images (max 5MB each) and describe yourself as an owner.</p>
        </div>
    </div>

    <div id="ownerLegitimacyGrid" style="display:grid; grid-template-columns: 1fr; gap: 16px;">
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="margin-top: 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
            @csrf
            @method('patch')

            <input type="hidden" name="legitimacy_form" value="1">
            <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
            <input type="hidden" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}">
            <input type="hidden" name="address" value="{{ old('address', $user->address) }}">

            <div>
                <x-input-label for="legitimacy_proofs" :value="__('Upload Proof Images')" />
                <input id="legitimacy_proofs" type="file" name="legitimacy_proofs[]" accept="image/*" multiple style="margin-top: 8px; width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                <div id="legitimacyFilesHint" style="margin-top: 8px; color:#64748b; font-weight: 700; font-size: 0.9rem;">0 files selected</div>
                <x-input-error class="mt-2" :messages="$errors->get('legitimacy_proofs')" />
                <x-input-error class="mt-2" :messages="$errors->get('legitimacy_proofs.*')" />
            </div>



            @php $proofCount = isset($legitimacyProofs) ? $legitimacyProofs->count() : 0; @endphp
            <div style="margin-top: 10px; color:#94a3b8; font-weight: 800; font-size: 0.9rem;">
                Uploaded proofs: {{ (int) $proofCount }} / 15
            </div>
            <div style="margin-top: 20px; display:flex; align-items:flex-start; gap: 10px;">
                <input id="legitimacy_terms" name="legitimacy_terms" type="checkbox" value="1" {{ old('legitimacy_terms') ? 'checked' : '' }} style="margin-top: 4px; width: 18px; height: 18px; accent-color: var(--accent);">
                <div style="min-width:0;">
                    <label for="legitimacy_terms" style="font-weight: 900; color: var(--primary); cursor: pointer;">
                        I agree to the <button type="button" onclick="openLegitimacyTermsModal()" style="background:none; border:none; padding:0; margin-left: 6px; color: var(--accent); font-weight: 900; cursor:pointer; text-decoration: underline; text-decoration-thickness: 2px; text-underline-offset: 2px;">
                            Terms & Privacy Policy 
                        </button> for proof uploads 
                    </label>
                    <div style="margin-top: 6px; color:#64748b; font-weight: 700; font-size: 0.9rem; line-height: 1.35;">

                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('legitimacy_terms')" />
                </div>
            </div>
            <div class="flex items-center gap-4" style="margin-top: 12px;">
                <x-primary-button id="legitimacySubmitBtn">{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>

        <div style="margin-top: 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
            <div style="font-weight: 900; color: var(--primary);">Uploaded Photos</div>
            @if(isset($legitimacyProofs) && $legitimacyProofs->count() > 0)
                <div style="margin-top: 12px; display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    @foreach($legitimacyProofs as $p)
                        <div style="position: relative;">
                            <a href="{{ Storage::url($p->file_path) }}" class="legitimacy-photo" data-src="{{ Storage::url($p->file_path) }}" style="display:block; border-radius: 10px; overflow:hidden; border: 1px solid #e2e8f0; background: white;">
                                <img src="{{ Storage::url($p->file_path) }}" alt="Proof" style="width: 100%; height: 110px; object-fit: cover;">
                            </a>
                            <form method="POST" action="{{ route('profile.legitimacy-proofs.destroy', $p) }}" class="confirm-delete" style="position:absolute; top: 8px; right: 8px; z-index: 5;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.9); color: white; border: 1px solid rgba(185, 28, 28, 0.9); width: 34px; height: 34px; border-radius: 999px; display:flex; align-items:center; justify-content:center;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="margin-top: 10px; color:#94a3b8; font-weight: 800;">No uploaded photos yet.</div>
            @endif
        </div>

        <form method="post" action="{{ route('profile.update') }}" style="margin-top: 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px;">
            @csrf
            @method('patch')
            <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
            <input type="hidden" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}">
            <input type="hidden" name="address" value="{{ old('address', $user->address) }}">

            <div style="font-weight: 900; color: var(--primary); margin-bottom: 8px;">About the owner</div>
            <textarea id="about_owner" name="about_owner" rows="20" style="margin-top: 8px; width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">{{ old('about_owner', $user->about_owner) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('about_owner')" />

            <div class="flex items-center gap-4" style="margin-top: 12px;">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>

    <div id="legitimacyTermsModal" style="display:none; position: fixed; inset: 0; background: rgba(2,6,23,0.88); z-index: 12000; align-items: center; justify-content: center; padding: 20px;">
        <div onclick="closeLegitimacyTermsModal()" style="position:absolute; inset:0;"></div>
        <div style="position: relative; z-index: 1; width: 100%; max-width: 980px; background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.35); max-height: 85vh; display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background: #0f172a; color: white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900; letter-spacing: 0.2px;">Terms & Privacy Policy (Proof Uploads)</div>
                <button type="button" onclick="closeLegitimacyTermsModal()" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background: #f8fafc; overflow:auto;">
                <div style="background:white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; color:#0f172a; font-weight: 500; line-height: 1.6; white-space: pre-wrap;">
By uploading proof documents (“Proofs”) you confirm that you have the right to provide these materials and that they relate to your legitimacy as a vehicle owner or rental service provider.

1) What You May Upload
Only upload clear photos/images of legitimate supporting documents, which may include:
- Business permits / Mayor’s permit / DTI or SEC registration (if applicable)
- Certificates of registration, accreditations, or certifications relevant to vehicle rental operations
- Other pertinent documents that support legitimacy and lawful operation

2) Purpose of Collection and Use
Your Proofs may be used for:
- Owner identity and legitimacy verification
- Fraud prevention and platform safety
- Booking approval and dispute resolution
- Compliance with applicable laws and lawful requests

3) Prohibited Content
You must not upload:
- Falsified, altered, forged, or misleading documents
- Documents that you are not authorized to share
- Content containing unrelated sensitive information (e.g., bank passwords, OTPs)
Any violation may result in removal of content, account restrictions, cancellation of bookings, and/or further action as permitted by law.

4) Privacy and Protection
We apply reasonable administrative, technical, and organizational safeguards to protect your uploaded Proofs. Proofs are treated as confidential and are accessed only when needed for verification, support, or compliance, unless disclosure is required by law.

5) Retention
Proofs may be retained as long as necessary for verification, record-keeping, dispute handling, and compliance. When no longer required, Proofs may be securely deleted or anonymized, subject to legal obligations.

6) Your Responsibility
Before uploading, review your documents and avoid including unnecessary personal data. You may request deletion, subject to legal and contractual limitations.

By checking the box, you confirm you have read and agree to these Terms & Privacy Policy for proof uploads.
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 1100px) {
            #ownerLegitimacyGrid { grid-template-columns: 1fr 1fr 1fr !important; }
        }
        .ck-powered-by { display: none !important; }
        .ck-editor__editable[role="textbox"] { min-height: 600px; max-height: 600px; }
    </style>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('legitimacy_proofs');
            const hint = document.getElementById('legitimacyFilesHint');
            if (fileInput && hint) {
                fileInput.addEventListener('change', () => {
                    const files = fileInput.files ? Array.from(fileInput.files) : [];
                    if (files.length > 15) {
                        fileInput.value = '';
                        hint.textContent = '0 files selected';
                        return;
                    }
                    hint.textContent = files.length + (files.length === 1 ? ' file selected' : ' files selected');
                });
            }

            const termsCb = document.getElementById('legitimacy_terms');
            const submitBtn = document.getElementById('legitimacySubmitBtn');
            if (termsCb && submitBtn) {
                const sync = () => {
                    const ok = !!termsCb.checked;
                    submitBtn.disabled = !ok;
                    submitBtn.style.opacity = ok ? '1' : '0.55';
                    submitBtn.style.cursor = ok ? 'pointer' : 'not-allowed';
                };
                termsCb.addEventListener('change', sync);
                sync();
            }

            const el = document.getElementById('about_owner');
            if (el && window.ClassicEditor) {
                ClassicEditor.create(el, {
                    toolbar: ['undo', 'redo', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'link'],
                }).then(editor => {
                    const editable = editor.ui.view.editable.element;
                    if (editable) editable.style.height = '300px';
                }).catch(() => {});
            }

            const modal = document.createElement('div');
            modal.id = 'proofCarouselModal';
            modal.style.cssText = 'display:none; position:fixed; inset:0; background:rgba(2,6,23,0.88); z-index:99999; align-items:center; justify-content:center; padding:20px;';
            modal.innerHTML = `
                <div id="proofBackdrop" style="position:absolute; inset:0;"></div>
                <div style="position:relative; z-index:1; width:100%; max-width:980px; background:#111827; border-radius: 12px; overflow:hidden; border:1px solid #374151;">
                    <div style="padding:10px 12px; background:#0f172a; color:white; display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:900;">Proof Photos</div>
                        <div style="display:flex; gap:8px;">
                            <button id="proofPrev" type="button" style="background:#1f2937; color:#e5e7eb; border:1px solid #374151; padding:8px 10px; border-radius:8px;">Prev</button>
                            <button id="proofNext" type="button" style="background:#1f2937; color:#e5e7eb; border:1px solid #374151; padding:8px 10px; border-radius:8px;">Next</button>
                            <button id="proofClose" type="button" style="background:#ef4444; color:white; border:1px solid #b91c1c; padding:8px 10px; border-radius:8px;">Close</button>
                        </div>
                    </div>
                    <div style="background:#111827; display:flex; align-items:center; justify-content:center; min-height:420px;">
                        <img id="proofImg" src="" alt="" style="max-width:100%; max-height:420px; object-fit:contain;"/>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            let images = [];
            let cur = 0;
            function openCarousel(startIndex) {
                const anchors = Array.from(document.querySelectorAll('.legitimacy-photo'));
                images = anchors.map(a => a.getAttribute('data-src')).filter(Boolean);
                cur = Math.max(0, Math.min(startIndex || 0, images.length - 1));
                const img = document.getElementById('proofImg');
                if (img && images.length > 0) img.src = images[cur];
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function closeCarousel() {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            function nav(offset) {
                if (images.length === 0) return;
                cur = (cur + offset + images.length) % images.length;
                const img = document.getElementById('proofImg');
                if (img) img.src = images[cur];
            }

            document.addEventListener('click', (e) => {
                const a = e.target.closest('.legitimacy-photo');
                if (!a) return;
                e.preventDefault();
                const anchors = Array.from(document.querySelectorAll('.legitimacy-photo'));
                const idx = Math.max(0, anchors.indexOf(a));
                openCarousel(idx);
            });
            document.getElementById('proofBackdrop')?.addEventListener('click', closeCarousel);
            document.getElementById('proofClose')?.addEventListener('click', closeCarousel);
            document.getElementById('proofPrev')?.addEventListener('click', () => nav(-1));
            document.getElementById('proofNext')?.addEventListener('click', () => nav(1));
            document.addEventListener('keydown', (e) => {
                if (modal.style.display === 'flex') {
                    if (e.key === 'Escape') closeCarousel();
                    if (e.key === 'ArrowLeft') nav(-1);
                    if (e.key === 'ArrowRight') nav(1);
                }
            });
        });

        function openLegitimacyTermsModal() {
            const m = document.getElementById('legitimacyTermsModal');
            if (!m) return;
            m.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeLegitimacyTermsModal() {
            const m = document.getElementById('legitimacyTermsModal');
            if (!m) return;
            m.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>
</section>
