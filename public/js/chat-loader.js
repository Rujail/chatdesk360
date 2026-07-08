// public/js/chat-loader.js
(function () {
    if (window.ChatLoaderInitialized) return;
    window.ChatLoaderInitialized = true;

    const iframe = document.createElement('iframe');
    iframe.src = '/live-chat-widget-iframe';
    iframe.style.cssText = 'position:fixed;bottom:0;right:0;width:1px;height:1px;border:none;opacity:0;pointer-events:none;z-index:-9999;';
    document.body.appendChild(iframe);
})();