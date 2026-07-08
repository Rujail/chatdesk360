(function (d, w) {
    'use strict';

    if (!w.__cd || !w.__cd.site_id) {
        console.warn('[ChatDesk360] window.__cd.site_id missing.');
        return;
    }

    var SITE_ID  = w.__cd.site_id;
    var BASE_URL = 'https://chatdesk360.com';

    if (d.getElementById('cd-frame')) return;

    var firebaseConfig = null;
    var iframeEl       = null;
    var imageOverlay   = null;
    var retryTimer     = null;
    var RETRY_MS       = 10000; 
    var MAX_RETRIES    = 3;     // Only retry 3 times on initial load failure

    // ★★★ CENTRALIZED AUTHORIZATION CHECK (Runs ONCE) ★★★
    function checkAuthorization() {
        fetch(BASE_URL + '/api/chat/config', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                site_id: SITE_ID,
                parent_url: window.location.origin
            })
        })
        .then(async (r) => {
            const data = await r.json();
            
            // ★ Check for Country Restriction specifically
            if (r.status === 403 && data.error === 'Country restricted') {
                removeWidget();
                if (retryTimer) { clearInterval(retryTimer); retryTimer = null; }
                return;
            }

            if (!r.ok || !data || data.error) {
                handleAuthFailure();
                return;
            }

            // ★ Success — update stored config
            firebaseConfig = data;

            if (!iframeEl) {
                createWidget();
            } else {
                sendConfigToIframe();
            }
        })
        .catch(() => {
            handleAuthFailure();
        });
    }

    function handleAuthFailure() {
        if (!firebaseConfig) {
            // ★ Initial load failed — retry automatically (fixes refresh issue)
            if (!retryTimer) {
                var retryCount = 0;
                retryTimer = setInterval(function() {
                    retryCount++;
                    if (retryCount >= MAX_RETRIES) {
                        clearInterval(retryTimer);
                        retryTimer = null;
                        return;
                    }
                    checkAuthorization();
                }, RETRY_MS);
            }
        } else {
            // ★ Was authorized before, now unauthorized — remove widget
            removeWidget();
        }
    }

    function removeWidget() {
        if (iframeEl) {
            iframeEl.remove();
            iframeEl = null;
        }
        closeImagePreview();
    }

    // ★ START INITIAL CHECK (Only runs once or on retry failure)
    checkAuthorization();

    function createWidget() {
        if (iframeEl) return; 

        if (retryTimer) {
            clearInterval(retryTimer);
            retryTimer = null;
        }

        var f = d.createElement('iframe');
        f.id  = 'cd-frame';
        iframeEl = f;

        f.src = BASE_URL + '/livechat/widget/iframe?site_id=' + SITE_ID
            + '&po=' + encodeURIComponent(window.location.origin);

        f.style.cssText = [
            'position:fixed', 'bottom:24px', 'right:24px', 'width:60px', 'height:60px',
            'border:none', 'z-index:99999', 'border-radius:50%', 'overflow:hidden',
            'transition:width .3s cubic-bezier(0.4,0,0.2,1),height .3s cubic-bezier(0.4,0,0.2,1),border-radius .3s cubic-bezier(0.4,0,0.2,1),bottom .3s,right .3s,left .3s',
            'box-shadow:0 4px 12px rgba(0,0,0,0.2)'
        ].join(';');

        f.setAttribute('allow', 'microphone; camera; display-capture');

        d.body ? d.body.appendChild(f) : d.addEventListener('DOMContentLoaded', function () { d.body.appendChild(f); });

        f.onload = function () {
            sendConfigToIframe();
            ping();
        };

        w.addEventListener('message', messageHandler);
        w.addEventListener('resize', function () {
            if (!iframeEl || iframeEl.dataset.cdOpen !== 'true') return;
            var bot  = parseInt(iframeEl.style.bottom) || 24;
            var maxH = w.innerHeight - bot - 20;
            iframeEl.style.height = Math.min(600, maxH) + 'px';
        });
    }

    function messageHandler(e) {
        if (!e.data || !e.data.type) return;
        switch (e.data.type) {
            case '__cd_request_config': sendConfigToIframe(); break;
            case '__cd_resize': handleResize(e.data); break;
            case '__cd_image_preview': showImagePreview(e.data.url); break;
            case '__cd_close_image': closeImagePreview(); break;
        }
    }

    function sendConfigToIframe() {
        if (!firebaseConfig || !iframeEl) return;
        try {
            iframeEl.contentWindow.postMessage({ type: '__cd_config', config: firebaseConfig }, '*');
        } catch (e) {}
    }

    function handleResize(data) {
        if (!iframeEl) return;
        var s   = iframeEl.style;
        var bot = parseInt(data.bottom) || 24;
        s.width        = data.width        || '60px';
        s.bottom       = data.bottom       || '24px';
        s.right        = data.right        || 'auto';
        s.left         = data.left         || 'auto';
        s.borderRadius = data.borderRadius || '50%';
        s.boxShadow    = data.boxShadow    || '0 4px 12px rgba(0,0,0,0.2)';
        var maxH = w.innerHeight - bot - 20;
        s.height = Math.min(parseInt(data.height) || 60, maxH) + 'px';
        iframeEl.dataset.cdOpen = (parseInt(data.height) || 60) > 70 ? 'true' : 'false';
    }

    function showImagePreview(url) {
        if (imageOverlay) closeImagePreview();
        imageOverlay = d.createElement('div');
        imageOverlay.id = 'cd-img-overlay';
        imageOverlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.92);display:flex;align-items:center;justify-content:center;z-index:999999;cursor:zoom-out';
        var img = d.createElement('img');
        img.src = url;
        img.style.cssText = 'max-width:92%;max-height:92vh;object-fit:contain;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,0.4)';
        var closeBtn = d.createElement('button');
        closeBtn.style.cssText = 'position:absolute;top:20px;right:30px;color:#fff;font-size:40px;font-weight:700;background:none;border:none;cursor:pointer;opacity:.85;line-height:1';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', closeImagePreview);
        imageOverlay.addEventListener('click', function (e) { if (e.target === imageOverlay) closeImagePreview(); });
        imageOverlay.appendChild(img);
        imageOverlay.appendChild(closeBtn);
        d.body.appendChild(imageOverlay);
    }

    function closeImagePreview() {
        if (imageOverlay) { imageOverlay.remove(); imageOverlay = null; }
        if (iframeEl) { try { iframeEl.contentWindow.postMessage({ type: '__cd_image_closed' }, '*'); } catch (e) {} }
    }

    function ping() {
        if (!iframeEl) return;
        try { iframeEl.contentWindow.postMessage({ type: '__cd_page', url: w.location.href, title: d.title }, '*'); } catch (e) {}
    }

    var _ps = history.pushState.bind(history);
    var _rs = history.replaceState.bind(history);
    history.pushState = function () { _ps.apply(history, arguments); ping(); };
    history.replaceState = function () { _rs.apply(history, arguments); ping(); };
    w.addEventListener('popstate', ping);

}(document, window));