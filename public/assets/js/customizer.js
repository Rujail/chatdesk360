/**
 * Widget Customizer — wires every control in the left panel
 * to the live preview on the right, and persists via AJAX.
 */
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════════════════════════════════
       0.  Data from Blade
       ═══════════════════════════════════════════════════════ */
    const SITE_ID   = window.__widgetSiteId   || null;
    const SAVED     = window.__widgetSettings  || {};
    const SAVE_URL  = window.__widgetSaveUrl   || '/settings/widget/save';
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!SITE_ID) { console.warn('[Customizer] No site_id.'); return; }

    /* ── Defaults (must match PHP controller) ───────────── */
    const DEFAULTS = {
        minimized_style     : 'bubble',
        theme               : 'light',
        primary_color       : '#2366ff',
        use_custom_colors   : false,
        widget_bg_color     : '#f7f7f7',
        widget_text_color   : '#1f2937',
        position            : 'right',
        side_spacing        : 24,
        bottom_spacing      : 24,
        show_logo           : false,
        logo_url            : '',
        show_agent_photo    : true,
        sound_notifications : true,
        allow_rating        : true,
        allow_transcripts   : true,
        white_label         : false,
        eye_catcher_image   : '',
        welcome_header      : 'Welcome!',
        welcome_title       : 'Text us',
        admin_name          : 'Admin',
        welcome_message     : 'Hello. How may I help you?',
    };

    let S = { ...DEFAULTS, ...SAVED };

    /* ═══════════════════════════════════════════════════════
       1.  DOM refs — with null safety
       ═══════════════════════════════════════════════════════ */
    const container  = document.getElementById('chat-widget-container');
    const bubble     = document.getElementById('chat-bubble');
    const popup      = document.getElementById('chat-popup');

    // ★ If no preview container, this page doesn't have the widget preview
    if (!container) {
        console.warn('[Customizer] No preview container found — skipping UI bindings.');
        return;
    }

    /* ═══════════════════════════════════════════════════════
       2.  Utility helpers
       ═══════════════════════════════════════════════════════ */
    function shadeColor(hex, pct) {
        if (!hex || hex.length < 7) return hex;
        let r = parseInt(hex.substring(1, 3), 16);
        let g = parseInt(hex.substring(3, 5), 16);
        let b = parseInt(hex.substring(5, 7), 16);
        r = Math.min(255, Math.max(0, Math.round(r * (100 + pct) / 100)));
        g = Math.min(255, Math.max(0, Math.round(g * (100 + pct) / 100)));
        b = Math.min(255, Math.max(0, Math.round(b * (100 + pct) / 100)));
        return '#' + [r, g, b].map(c => c.toString(16).padStart(2, '0')).join('');
    }

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
    }

    /* ═══════════════════════════════════════════════════════
       3.  APPLY — push current S into the preview DOM
       ═══════════════════════════════════════════════════════ */
    function apply() {
        if (!container) return;
        applyTheme();
        applyPrimaryColor();
        applyCustomColors();
        applyPosition();
        applyMinimizedStyle();
        applyLogo();
        applyAgentPhoto();
        applyTweaks();
        applyEyeCatcher();
        applyGreeting();
        applyBranding();
    }

    function applyTheme() {
        const isDark = S.theme === 'dark';
        container.classList.toggle('dark-mode', isDark);
        popup?.classList.toggle('dark-mode', isDark);
    }

    function applyPrimaryColor() {
        const hex = S.primary_color || '#2366ff';
        const r   = document.documentElement;
        r.style.setProperty('--primary-color', hex);
        r.style.setProperty('--primary-hover',  shadeColor(hex, -18));
        r.style.setProperty('--bubble-user-bg', hex);
        r.style.setProperty('--bubble-button-bg', hex);

        // ★ Also set on popup and container directly (overrides dark-mode specificity)
        if (popup) {
            popup.style.setProperty('--primary-color', hex);
            popup.style.setProperty('--primary-hover', shadeColor(hex, -18));
            popup.style.setProperty('--bubble-user-bg', hex);
            popup.style.setProperty('--bubble-button-bg', hex);
        }
        if (container) {
            container.style.setProperty('--primary-color', hex);
            container.style.setProperty('--bubble-button-bg', hex);
        }

        const startBtn = document.querySelector('.start-chat-btn');
        if (startBtn) startBtn.style.backgroundColor = hex;

        const sendBtn = document.querySelector('.send-btn');
        if (sendBtn) sendBtn.style.backgroundColor = hex;
    }

    function applyCustomColors() {
        const r   = document.documentElement;
        const isDark = S.theme === 'dark';

        if (S.use_custom_colors) {
            const bg   = S.widget_bg_color   || '#f7f7f7';
            const text = S.widget_text_color  || '#1f2937';

            r.style.setProperty('--popup-bg',   bg);
            r.style.setProperty('--popup-text', text);
            r.style.setProperty('--bot-bg',     bg);
            r.style.setProperty('--bot-text',   text);

            if (popup) {
                popup.style.setProperty('--popup-bg',   bg);
                popup.style.setProperty('--popup-text', text);
                popup.style.setProperty('--bot-bg',     bg);
                popup.style.setProperty('--bot-text',   text);
            }
        } else {
            const bg   = isDark ? '#1e1e1e' : '#f7f7f7';
            const text = isDark ? '#f5f5f5' : '#1f2937';
            const botBg   = isDark ? '#2b2b2b' : '#ffffff';
            const botText = isDark ? '#e6e6e6' : '#1f2937';
            const hdrIcon = isDark ? '#ffffff' : '#1f2937';
            const inpBg   = isDark ? '#2b2b2b' : '#ffffff';
            const inpText = isDark ? '#f2f2f2' : '#374151';
            const ftBg    = isDark ? '#2b2b2b' : '#ffffff';
            const ftIcon  = isDark ? '#e6e6e6' : '#9ca3af';

            const vars = {
                '--popup-bg': bg, '--popup-text': text,
                '--bot-bg': botBg, '--bot-text': botText,
                '--header-icon': hdrIcon,
                '--input-bg': inpBg, '--input-text': inpText,
                '--footer-bg': ftBg, '--footer-icon': ftIcon,
                '--bubble-button-bg': isDark ? '#111111' : S.primary_color,
            };

            Object.entries(vars).forEach(([k, v]) => {
                r.style.setProperty(k, v);
                if (popup) popup.style.setProperty(k, v);
                if (container) container.style.setProperty(k, v);
            });
        }
    }

    function applyPosition() {
        if (!bubble || !popup) return;
        const side     = S.position === 'left' ? 'left' : 'right';
        const other    = side === 'left' ? 'right' : 'left';
        const sidePx   = parseInt(S.side_spacing)  || 24;
        const bottomPx = parseInt(S.bottom_spacing) || 24;

        [bubble, popup].forEach(el => {
            el.style[side]  = sidePx + 'px';
            el.style[other] = 'auto';
        });
        bubble.style.bottom = bottomPx + 'px';
        popup.style.bottom  = (bottomPx + 70) + 'px';
    }

    function applyMinimizedStyle() {
        if (!bubble) return;
        bubble.classList.toggle('bar-style',   S.minimized_style === 'bar');
        bubble.classList.toggle('bubble-style', S.minimized_style !== 'bar');

        if (S.minimized_style === 'bar') {
            if (!bubble.querySelector('.bar-label')) {
                const span = document.createElement('span');
                span.className = 'bar-label';
                span.textContent = 'Chat with us';
                span.style.cssText = 'color:#fff;font-size:14px;font-weight:500;margin-left:8px;';
                bubble.appendChild(span);
            }
            bubble.style.width = 'auto';
            bubble.style.borderRadius = '24px';
            bubble.style.padding = '0 20px';
        } else {
            const lbl = bubble.querySelector('.bar-label');
            if (lbl) lbl.remove();
            bubble.style.width = '60px';
            bubble.style.borderRadius = '50%';
            bubble.style.padding = '0';
        }
    }

    function applyLogo() {
        const home = document.getElementById('chat-home-screen');
        if (!home) return;

        let logoEl = document.getElementById('widget-logo-preview');
        if (S.show_logo && S.logo_url) {
            if (!logoEl) {
                logoEl = document.createElement('div');
                logoEl.id = 'widget-logo-preview';
                logoEl.className = 'widget-logo';
                logoEl.style.cssText = 'text-align:center;padding:10px 20px 0;';
                home.insertBefore(logoEl, home.firstChild);
            }
            logoEl.innerHTML = '<img src="' + esc(S.logo_url) + '" alt="Logo" style="max-height:48px;max-width:100%;object-fit:contain;">';
            logoEl.style.display = '';
        } else {
            if (logoEl) logoEl.style.display = 'none';
        }
    }

    function applyAgentPhoto() {
        popup?.querySelectorAll('.avatar-circle').forEach(el => {
            el.style.display = S.show_agent_photo ? '' : 'none';
        });
    }

    function applyTweaks() {
        const transcriptItem = popup?.querySelector('#options-menu .options-item:first-child');
        if (transcriptItem) transcriptItem.style.display = S.allow_transcripts ? '' : 'none';
    }

    function applyEyeCatcher() {
        const ec = container?.querySelector('.eyecatcher');
        if (!ec) return;
        const img = ec.querySelector('img');
        if (S.eye_catcher_image) {
            img.src = S.eye_catcher_image;
            ec.style.display = '';
        } else {
            img.src = '';
            ec.style.display = 'none';
        }
    }

    function applyGreeting() {
        const hdr = popup?.querySelector('.home-header');
        const ttl = popup?.querySelector('.home-title');
        const nms = popup?.querySelectorAll('.admin-text');
        const msg = popup?.querySelector('.admin-info');

        if (hdr) hdr.textContent = S.welcome_header  || 'Welcome!';
        if (ttl) ttl.textContent = S.welcome_title   || 'Text us';
        nms?.forEach(el => el.textContent = S.admin_name || 'Admin');
        if (msg) msg.textContent = S.welcome_message  || 'Hello. How may I help you?';
    }

    function applyBranding() {
        if (!popup) return;
        let branding = popup.querySelector('.cd-preview-branding');

        if (!S.white_label) {
            if (!branding) {
                branding = document.createElement('div');
                branding.className = 'cd-preview-branding';
                branding.style.cssText = 'text-align:center;padding:8px 0;font-size:11px;color:var(--muted);background:var(--popup-bg);border-top:1px solid rgba(0,0,0,0.04);';
                branding.innerHTML = 'Powered By <a href="https://chatdesk360.com" target="_blank" rel="noopener" style="color:var(--primary-color);text-decoration:none;font-weight:600;">ChatDesk360</a>';
                popup.appendChild(branding);
            }
            branding.style.display = '';
        } else {
            if (branding) branding.style.display = 'none';
        }
    }

    /* ═══════════════════════════════════════════════════════
       4.  HELPER: Refresh Eye Catcher Selection
       ═══════════════════════════════════════════════════════ */
    function refreshEyeSelection() {
        document.querySelectorAll('.eye-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.eye === S.eye_catcher_image);
        });
        const customPreview = document.getElementById('eye-custom-preview');
        const customImg     = document.getElementById('eye-custom-preview-img');
        if (customPreview && customImg) {
            if (S.eye_catcher_image && !['/assets/images/eye1.png','/assets/images/eye2.png',
                '/assets/images/eye3.png','/assets/images/eye4.png',''].includes(S.eye_catcher_image)) {
                customImg.src       = S.eye_catcher_image;
                customImg.dataset.eye = S.eye_catcher_image;
                customPreview.style.display = 'block';
            } else {
                customPreview.style.display = 'none';
            }
        }
    }

    /* ═══════════════════════════════════════════════════════
       5.  POPULATE controls from S
       ═══════════════════════════════════════════════════════ */
    function populate() {
        setChecked('bar',    S.minimized_style === 'bar');
        setChecked('bubble', S.minimized_style !== 'bar');
        setChecked('light',  S.theme !== 'dark');
        setChecked('dark',   S.theme === 'dark');

        const picker = document.getElementById('theme-color');
        if (picker) { try { picker.value = S.primary_color; } catch(e) {} }
        document.querySelectorAll('.color-swatch[data-color]').forEach(sw => {
            sw.classList.toggle('selected', sw.dataset.color === S.primary_color);
        });

        setChecked('themeColorcheck-toggle', !S.use_custom_colors);
        setChecked('more-colors-toggle', S.use_custom_colors);
        const morePanel = document.getElementById('more-colors');
        if (morePanel) morePanel.classList.toggle('d-none', !S.use_custom_colors);

        setValue('widget-bg-color',   S.widget_bg_color   || '#f7f7f7');
        setValue('widget-text-color', S.widget_text_color  || '#1f2937');

        const posSel = document.querySelector('[name="widgetPosition"]');
        if (posSel) posSel.value = S.position;
        setValue('side-spacing',   S.side_spacing);
        setValue('bottom-spacing', S.bottom_spacing);

        setChecked('showLogo',          S.show_logo);
        setChecked('showAgent',         S.show_agent_photo);
        setChecked('shownotification',  S.sound_notifications);
        setChecked('showcusrate',       S.allow_rating);
        setChecked('showTranscripts',   S.allow_transcripts);
        setChecked('showWhitelabel',    S.white_label);

        refreshEyeSelection(); // Now this works because it's in the outer scope!
    }

    function setChecked(id, val) {
        const el = document.getElementById(id);
        if (el) el.checked = !!val;
    }
    function setValue(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val ?? '';
    }

    /* ═══════════════════════════════════════════════════════
       6.  BIND controls → update S → apply()
       ═══════════════════════════════════════════════════════ */
    function bind() {

        /* ── Bar / Bubble ─────────────────────────────────── */
        on('bar',    'change', () => { S.minimized_style = 'bar';    apply(); });
        on('bubble', 'change', () => { S.minimized_style = 'bubble'; apply(); });

        /* ── Theme ────────────────────────────────────────── */
        on('light', 'change', () => { S.theme = 'light'; apply(); });
        on('dark',  'change', () => { S.theme = 'dark';  apply(); });

        /* ── Color swatches ───────────────────────────────── */
        document.querySelectorAll('.color-swatch[data-color]').forEach(sw => {
            sw.addEventListener('click', function () {
                const c = this.dataset.color;
                if (!c) return;
                document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                S.primary_color = c;
                const picker = document.getElementById('theme-color');
                if (picker) { try { picker.value = c; } catch(e) {} }
                apply();
            });
        });

        /* ── Custom color picker ──────────────────────────── */
        on('theme-color', 'input', function () {
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
            S.primary_color = this.value;
            apply();
        });

        /* ── Theme / More-colors toggle ───────────────────── */
        on('themeColorcheck-toggle', 'change', function () {
            S.use_custom_colors = false;
            document.getElementById('more-colors')?.classList.add('d-none');
            apply();
        });
        on('more-colors-toggle', 'change', function () {
            S.use_custom_colors = true;
            document.getElementById('more-colors')?.classList.remove('d-none');
            apply();
        });

        /* ── Extra color pickers ──────────────────────────── */
        on('widget-bg-color',   'input', function () { S.widget_bg_color   = this.value; apply(); });
        on('widget-text-color', 'input', function () { S.widget_text_color = this.value; apply(); });

        /* ── Position ─────────────────────────────────────── */
        const posSel = document.querySelector('[name="widgetPosition"]');
        posSel?.addEventListener('change', function () { S.position = this.value; apply(); });
        on('side-spacing',   'input', function () { S.side_spacing  = parseInt(this.value) || 24; apply(); });
        on('bottom-spacing', 'input', function () { S.bottom_spacing = parseInt(this.value) || 24; apply(); });

        /* ── Tweaks ───────────────────────────────────────── */
        on('showLogo',         'change', function () { S.show_logo           = this.checked; apply(); });
        on('showAgent',        'change', function () { S.show_agent_photo    = this.checked; apply(); });
        on('shownotification', 'change', function () { S.sound_notifications = this.checked; });
        on('showcusrate',      'change', function () { S.allow_rating        = this.checked; });
        on('showTranscripts',  'change', function () { S.allow_transcripts   = this.checked; apply(); });
        on('showWhitelabel',   'change', function () { S.white_label         = this.checked; apply(); });

        /* ── Logo file upload ★ FIXED null safety ─────────── */
        const showLogoEl = document.getElementById('showLogo');
        const logoInput  = showLogoEl?.closest('li')?.querySelector('input[type="file"]');
        logoInput?.addEventListener('change', function () {
            if (!this.files?.length) return;

            const file = this.files[0];

            // ★ Client-side validation before sending
            if (!file.type.startsWith('image/')) {
                toast('Please select an image file (PNG, JPG, GIF, SVG).', 'error');
                return;
            }
            if (file.size > 2048 * 1024) {
                toast('Image must be smaller than 2MB.', 'error');
                return;
            }

            const fd = new FormData();
            fd.append('logo', file);

            fetch('/settings/widget/upload-logo', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd
            })
            .then(r => {
                // ★ Better error handling — show Laravel validation messages
                if (!r.ok) {
                    return r.json().then(errData => {
                        let msg = 'Upload failed.';
                        if (errData.errors) {
                            const firstErr = Object.values(errData.errors)[0];
                            if (firstErr) msg = firstErr.join(', ');
                        } else if (errData.message) {
                            msg = errData.message;
                        }
                        throw new Error(msg);
                    });
                }
                return r.json();
            })
            .then(data => {
                if (data.url) {
                    S.logo_url  = data.url;
                    S.show_logo = true;
                    if (showLogoEl) showLogoEl.checked = true;
                    apply();
                    toast('Logo uploaded!');
                }
            })
            .catch(err => {
                toast(err.message || 'Logo upload failed.', 'error');
            });
        });

        /* ── Eye catcher — toggle select/deselect + custom upload ── */
        document.querySelectorAll('#eye-catcher-list .eye-option').forEach(el => {
            el.addEventListener('click', function () {
                const val = this.dataset.eye;
                if (this.classList.contains('selected') && val !== '') {
                    // Deselect
                    S.eye_catcher_image = '';
                } else {
                    S.eye_catcher_image = val;
                }
                refreshEyeSelection();
                apply();
            });
        });
        
        // Custom upload trigger
        document.getElementById('eye-custom-upload')?.addEventListener('click', function () {
            document.getElementById('eye-catcher-file-input').click();
        });
        
        document.getElementById('eye-catcher-file-input')?.addEventListener('change', function () {
            if (!this.files?.length) return;
            const file = this.files[0];
            if (!file.type.startsWith('image/')) {
                toast('Please select an image file.', 'error'); return;
            }
            if (file.size > 2048 * 1024) {
                toast('Image must be smaller than 2MB.', 'error'); return;
            }
            const fd = new FormData();
            fd.append('eye_catcher', file);
        
            fetch('/settings/widget/upload-eye-catcher', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd
            })
            .then(r => {
                if (!r.ok) return r.json().then(e => { throw new Error(e.message || 'Upload failed'); });
                return r.json();
            })
            .then(data => {
                if (data.url) {
                    S.eye_catcher_image = data.url;
                    refreshEyeSelection();
                    apply();
                    toast('Eye catcher image uploaded!');
                }
            })
            .catch(err => toast(err.message || 'Upload failed.', 'error'));
            this.value = '';
        });
        
        // Remove custom eye catcher
        document.getElementById('eye-custom-remove')?.addEventListener('click', function () {
            S.eye_catcher_image = '';
            refreshEyeSelection();
            apply();
        });
        
        // Also handle click on the custom preview image to toggle
        document.getElementById('eye-custom-preview-img')?.addEventListener('click', function () {
            if (this.classList.contains('selected')) {
                S.eye_catcher_image = '';
            } else {
                S.eye_catcher_image = this.dataset.eye;
            }
            refreshEyeSelection();
            apply();
        });

        /* ── Save ─────────────────────────────────────────── */
        document.getElementById('save-widget-settings')?.addEventListener('click', save);

        /* ── Reset ────────────────────────────────────────── */
        document.getElementById('reset-widget-settings')?.addEventListener('click', () => {
            if (!confirm('Reset all widget settings to defaults?')) return;
            S = { ...DEFAULTS };
            populate();
            apply();
            toast('Settings reset (not saved yet).');
        });
    }

    function on(id, evt, fn) {
        const el = document.getElementById(id);
        if (el) el.addEventListener(evt, fn);
    }

    /* ═══════════════════════════════════════════════════════
       7.  SAVE — filtered to allowed keys only
       ═══════════════════════════════════════════════════════ */
    function save() {
        const btn  = document.getElementById('save-widget-settings');
        const orig = btn?.innerHTML;
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader-2 me-1 spin"></i>Saving…'; }

        // ★ Only send allowed keys
        const allowedKeys = [
            'minimized_style', 'theme', 'primary_color', 'primary_hover',
            'use_custom_colors', 'widget_bg_color', 'widget_text_color',
            'position', 'side_spacing', 'bottom_spacing',
            'show_logo', 'logo_url', 'show_agent_photo',
            'sound_notifications', 'allow_rating', 'allow_transcripts',
            'white_label', 'eye_catcher_image',
            'welcome_header', 'welcome_title', 'admin_name', 'welcome_message',
        ];

        const cleanSettings = {};
        allowedKeys.forEach(key => {
            if (S.hasOwnProperty(key)) {
                cleanSettings[key] = S[key];
            }
        });

        fetch(SAVE_URL, {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ settings: cleanSettings }),
        })
        .then(r => {
            if (r.status === 413) throw new Error('Data too large — reduce logo size.');
            if (r.status === 422) {
                return r.json().then(errData => {
                    let msg = 'Validation failed.';
                    if (errData.errors) {
                        const firstErr = Object.values(errData.errors)[0];
                        if (firstErr) msg = firstErr.join(', ');
                    }
                    throw new Error(msg);
                });
            }
            if (!r.ok) throw new Error('Server error: ' + r.status);
            return r.json();
        })
        .then(data => {
            toast(data.message || 'Saved!', data.success ? 'success' : 'error');
        })
        .catch(err => {
            toast(err.message || 'Network error — not saved.', 'error');
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.innerHTML = orig; }
        });
    }

    /* ═══════════════════════════════════════════════════════
       8.  TOAST
       ═══════════════════════════════════════════════════════ */
    function toast(msg, type = 'success') {
        let el = document.getElementById('widget-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'widget-toast';
            Object.assign(el.style, {
                position: 'fixed', top: '20px', right: '20px',
                padding: '12px 24px', borderRadius: '8px',
                color: '#fff', fontSize: '14px', fontWeight: '500',
                zIndex: 99999, transition: 'all .3s ease',
                transform: 'translateY(-16px)', opacity: 0,
            });
            document.body.appendChild(el);
        }
        el.textContent = msg;
        el.style.background = type === 'success' ? '#10b981' : '#ef4444';
        el.style.transform  = 'translateY(0)';
        el.style.opacity     = '1';
        clearTimeout(el._t);
        el._t = setTimeout(() => {
            el.style.transform = 'translateY(-16px)';
            el.style.opacity   = '0';
        }, 3200);
    }

    /* ═══════════════════════════════════════════════════════
       9.  INIT
       ═══════════════════════════════════════════════════════ */
    populate();
    bind();
    apply();
});