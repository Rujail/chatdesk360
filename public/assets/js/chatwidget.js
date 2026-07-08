document.addEventListener('DOMContentLoaded', function () {
    // ── Element Selection ──
    const chatInput      = document.getElementById('chat-input');
    const chatSubmit     = document.getElementById('chat-submit');
    const chatMessages   = document.getElementById('chat-messages');
    const chatBubble     = document.getElementById('chat-bubble');
    const chatPopup      = document.getElementById('chat-popup');
    const closePopup     = document.getElementById('close-popup');
    const chatHomeScreen = document.getElementById('chat-home-screen');
    const chatConversation = document.getElementById('chat-conversation');
    const chatFooter     = document.getElementById('chat-footer');
    const startChatBtn   = document.getElementById('start-chat-btn');
    const backToHomeBtn  = document.getElementById('back-to-home-btn');
    const homeTabBtn     = document.getElementById('home-tab-btn');
    const chatTabBtn     = document.getElementById('chat-tab-btn');
    const minimizeChatBtn = document.getElementById('minimize-chat');
    const attachBtn      = document.getElementById('attach-btn');
    const emojiBtn       = document.getElementById('emoji-btn');
    const optionsBtn     = document.getElementById('options-btn');
    const attachmentMenu = document.getElementById('attachment-menu');
    const emojiMenu      = document.getElementById('emoji-menu');
    const optionsMenu    = document.getElementById('options-menu');
    const emojis         = document.querySelectorAll('.emoji-grid span');
    const sendFileBtn    = document.getElementById('send-file-btn');
    const addScreenshotBtn = document.getElementById('add-screenshot-btn');
    const hiddenFileInput = document.getElementById('hidden-file-input');
    const previewContainer = document.getElementById('file-upload-preview');
    const previewCount   = previewContainer?.querySelector('.preview-count');
    const previewHeader  = previewContainer?.querySelector('.preview-header');
    const imagePreviewModal = document.getElementById('image-preview-modal');
    const modalImage     = document.getElementById('modal-image');
    const closeModalBtn  = document.getElementById('close-modal-btn');

    let filesToUpload = [];
    let chatState     = 'home';
    let isPopupOpen   = false;

    // ── Helper Functions ──
    function closeAllMenus() {
        attachmentMenu?.classList.add('hidden');
        emojiMenu?.classList.add('hidden');
        optionsMenu?.classList.add('hidden');
        attachBtn?.classList.remove('active');
        emojiBtn?.classList.remove('active');
        optionsBtn?.classList.remove('active');
    }

    function switchView(view) {
        if (view === 'home') {
            chatHomeScreen?.classList.remove('hidden');
            chatFooter?.classList.remove('hidden');
            chatConversation?.classList.add('hidden');
            homeTabBtn?.classList.add('active-tab');
            chatTabBtn?.classList.remove('active-tab');
            chatState = 'home';
        } else if (view === 'chat') {
            chatHomeScreen?.classList.add('hidden');
            chatFooter?.classList.add('hidden');
            chatConversation?.classList.remove('hidden');
            homeTabBtn?.classList.remove('active-tab');
            chatTabBtn?.classList.add('active-tab');
            chatInput?.focus();
            closeAllMenus();
            chatState = 'chat';
        }
    }

    function appendUserMessage(htmlContent) {
        const messageContainer = document.createElement('div');
        messageContainer.className = 'message-container user';
        messageContainer.innerHTML = `
            <div class="message-bubble user">${htmlContent}</div>
            <div class="read-status">Read</div>
        `;
        chatMessages?.appendChild(messageContainer);
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
        setTimeout(() => reply('Received! How else can I help?'), 1500);
    }

    function reply(text) {
        const messageContainer = document.createElement('div');
        messageContainer.className = 'message-container bot';
        messageContainer.innerHTML = `<div class="message-bubble bot">${text}</div>`;
        chatMessages?.appendChild(messageContainer);
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeFile(index) {
        if (index >= 0 && index < filesToUpload.length) {
            filesToUpload.splice(index, 1);
        }
        renderFilePreview();
    }

    function renderFilePreview() {
        if (!previewContainer) return;
        const previewList = previewContainer.querySelector('.preview-list');
        if (filesToUpload.length > 0) {
            previewContainer.classList.remove('hidden');
            previewContainer.classList.remove('collapsed');
        } else {
            previewContainer.classList.add('hidden');
            previewContainer.classList.remove('collapsed');
            if (previewList) previewList.innerHTML = '';
            return;
        }
        if (previewCount) previewCount.textContent = `${filesToUpload.length} of 5 uploaded`;
        previewList.innerHTML = '';
        filesToUpload.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.dataset.index = index;
            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    item.innerHTML = `<img src="${e.target.result}" alt="${file.name}" />`;
                    addControls(item, index);
                };
                reader.readAsDataURL(file);
            } else {
                item.innerHTML = `<div style="background:#6a5acd;color:white;height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;padding:5px;text-align:center;">.${file.name.split('.').pop().toUpperCase()}</div>`;
                addControls(item, index);
            }
            previewList.appendChild(item);
        });

        function addControls(item, originalIndex) {
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-file-btn';
            deleteBtn.type = 'button';
            deleteBtn.addEventListener('click', (e) => { e.stopPropagation(); removeFile(originalIndex); });
            item.appendChild(deleteBtn);
        }
    }

    function displayFilesAndMessage(files, message) {
        let nonImageFiles = [];
        files.forEach((file) => {
            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imgSrc = e.target.result;
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'message-container user';
                    imgContainer.innerHTML = `<div class="message-bubble user"><img src="${imgSrc}" class="chat-image" alt="${file.name}"></div><div class="read-status">Read</div>`;
                    chatMessages?.appendChild(imgContainer);
                    const chatImageElement = imgContainer.querySelector('.chat-image');
                    if (chatImageElement) {
                        chatImageElement.addEventListener('click', () => {
                            if (modalImage) modalImage.src = imgSrc;
                            imagePreviewModal?.classList.remove('hidden');
                        });
                    }
                    if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
                };
                reader.readAsDataURL(file);
            } else {
                nonImageFiles.push(file);
            }
        });
        if (message || nonImageFiles.length > 0) {
            let fileSummaryHTML = '';
            if (nonImageFiles.length > 0) {
                fileSummaryHTML = `<div style="font-weight:bold;margin-bottom:5px;">${nonImageFiles.length} file(s) attached:</div>${nonImageFiles.map(f => `<div style="font-size:12px;">📄 ${f.name}</div>`).join('')}`;
            }
            const finalMessageContent = fileSummaryHTML + (message ? `<br>${escapeHtml(message)}` : '');
            const messageContainer = document.createElement('div');
            messageContainer.className = 'message-container user';
            messageContainer.innerHTML = `<div class="message-bubble user">${finalMessageContent}</div><div class="read-status">Read</div>`;
            chatMessages?.appendChild(messageContainer);
            if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        setTimeout(() => reply('Files and message received! How else can I help?'), 1500);
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    // ── POPUP VISIBILITY ──
    function togglePopup() {
        isPopupOpen = !isPopupOpen;
        if (isPopupOpen) {
            chatPopup?.classList.remove('hidden');
            switchView(chatState);
        } else {
            chatPopup?.classList.add('hidden');
            closeAllMenus();
        }
    }

    chatBubble?.addEventListener('click', togglePopup);
    closePopup?.addEventListener('click', togglePopup);
    minimizeChatBtn?.addEventListener('click', togglePopup);
    closeModalBtn?.addEventListener('click', () => imagePreviewModal?.classList.add('hidden'));
    previewHeader?.addEventListener('click', () => previewContainer?.classList.toggle('collapsed'));

    // ── NAVIGATION ──
    startChatBtn?.addEventListener('click', () => switchView('chat'));
    backToHomeBtn?.addEventListener('click', () => switchView('home'));
    homeTabBtn?.addEventListener('click', () => switchView('home'));
    chatTabBtn?.addEventListener('click', () => switchView('chat'));

    // ── MENU TOGGLING ──
    attachBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = attachmentMenu?.classList.contains('hidden');
        closeAllMenus();
        if (isHidden) { attachmentMenu?.classList.remove('hidden'); attachBtn?.classList.add('active'); }
    });
    emojiBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = emojiMenu?.classList.contains('hidden');
        closeAllMenus();
        if (isHidden) { emojiMenu?.classList.remove('hidden'); emojiBtn?.classList.add('active'); }
    });
    optionsBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = optionsMenu?.classList.contains('hidden');
        closeAllMenus();
        if (isHidden) { optionsMenu?.classList.remove('hidden'); optionsBtn?.classList.add('active'); }
    });
    document.addEventListener('click', (e) => {
        if (chatPopup?.classList.contains('hidden')) return;
        if (!e.target.closest('#attachment-menu,#emoji-menu,#options-menu,#attach-btn,#emoji-btn,#options-btn')) closeAllMenus();
    });

    // ── EMOJI ──
    emojis.forEach(emoji => { emoji.addEventListener('click', function() { if (chatInput) chatInput.value += this.innerText; chatInput?.focus(); }); });

    // ── FILE UPLOAD ──
    sendFileBtn?.addEventListener('click', () => { closeAllMenus(); hiddenFileInput?.click(); });
    hiddenFileInput?.addEventListener('change', function() {
        if (!this.files || !this.files.length) return;
        Array.from(this.files).forEach(f => { if (filesToUpload.length < 5) filesToUpload.push(f); });
        renderFilePreview();
        this.value = '';
    });

    // ── SCREENSHOT ──
    addScreenshotBtn?.addEventListener('click', async () => {
        closeAllMenus();
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({ video: { mediaSource: 'screen' } });
            const video = document.createElement('video');
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                setTimeout(() => {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    stream.getTracks().forEach(t => t.stop());
                    canvas.toBlob(blob => {
                        const screenshotFile = new File([blob], 'screenshot.png', { type: 'image/png' });
                        if (filesToUpload.length < 5) { filesToUpload.push(screenshotFile); renderFilePreview(); }
                    }, 'image/png');
                }, 500);
            };
        } catch (err) {}
    });

    // ── TRANSCRIPT ──
    const sendTranscriptBtn = optionsMenu?.querySelector('.options-item:first-child');
    sendTranscriptBtn?.addEventListener('click', () => { closeAllMenus(); reply('Transcript requested! Sent to your email.'); });

    // ── MESSAGING ──
    chatSubmit?.addEventListener('click', function() {
        const message = chatInput?.value.trim();
        if (!message && filesToUpload.length === 0) return;
        if (filesToUpload.length > 0) {
            displayFilesAndMessage(filesToUpload, message);
            filesToUpload = [];
            previewContainer?.classList.add('hidden');
        } else if (message) {
            appendUserMessage(escapeHtml(message));
        }
        if (chatInput) chatInput.value = '';
        closeAllMenus();
    });

    chatInput?.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') chatSubmit?.click();
    });

    // ── Initialize ──
    switchView('home');

    // Chat post preview

    const builder = document.getElementById('form-builder');
    const previewTarget = document.getElementById('postchat-preview');
    if (!builder || !previewTarget) return;

    let idCounter = Date.now();

    const TEMPLATES = {
        thankyou: { label: 'Thank you message' },
        question: { label: 'Question' },
        message: { label: 'Message' },
        choice: { label: 'Choice list' },
        dropdown: { label: 'Dropdown' },
        multiple: { label: 'Multiple choice' },
        rating: { label: 'Chat rating' },
    };

    // helper to create element
    function el(tag, attrs = {}, html = '') {
        const d = document.createElement(tag);
        for (const k in attrs) {
            if (k === 'class') d.className = attrs[k];
            else if (k === 'dataset') Object.keys(attrs[k]).forEach((k2) => (d.dataset[k2] = attrs[k][k2]));
            else d.setAttribute(k, attrs[k]);
        }
        if (html) d.innerHTML = html;
        return d;
    }

    // add field buttons hook
    document.querySelectorAll('.add-field-btn').forEach((btn) => {
        btn.addEventListener('click', () => addField(btn.dataset.type));
    });

    // Add field function
    function addField(type, preset = {}) {
        // ---- Prevent duplicate fields ----
        // if (builder.querySelector(`.field-wrapper[data-type="${type}"]`)) {
        //     // Already exists → do not add again
        //     return;
        // }

        const id = ++idCounter;
        const wrapper = el('div', { class: 'field-wrapper', draggable: 'true', dataset: { id: id, type: type } });
        const head = el('div', { class: 'field-head' });
        head.appendChild(el('h4', {}, TEMPLATES[type].label || 'Field'));
        const actions = el('div', { class: 'field-actions' });
        const delBtn = el('button', { class: 'small-btn danger deletebtn' }, '<i class="ti ti-trash"></i>');
        const dragHint = el('span', {}, '☰');
        actions.appendChild(delBtn);
        actions.appendChild(dragHint);
        head.appendChild(actions);
        wrapper.appendChild(head);

        const body = el('div', { class: 'form-field' });

        if (type === 'thankyou') {
            body.appendChild(el('label', {}, 'Message:'));
            const textarea = el('textarea', {
                class: 'field-input form-control',
                dataset: { fieldProp: 'text' },
            });
            textarea.placeholder = 'Thank you for chatting with us!';
            // ★ FIX: Use preset value if provided
            textarea.value = preset.text || 'Thank you for the chat. Feel free to leave us any additional feedback.';
            body.appendChild(textarea);
        } else {
            body.appendChild(el('label', { class: 'form-label' }, 'Label:'));
            const labelInp = el('input', {
                type: 'text',
                class: 'field-input form-control',
                value: preset.label || '',
                dataset: { fieldProp: 'label' },
            });
            labelInp.placeholder = 'Field label';
            body.appendChild(labelInp);
        }

        if (type === 'choice' || type === 'dropdown' || type === 'multiple') {
            const optsWrap = el('div', { class: 'options-wrap' });
            const addOptBtn = el('button', { class: 'small-btn add-opt form-control' }, '+ Add option');
            optsWrap.appendChild(addOptBtn);
            body.appendChild(optsWrap);

            const opts = preset.options && preset.options.length ? preset.options : ['Option 1', 'Option 2'];
            opts.forEach((o) => addOptionRow(optsWrap, o));
            addOptBtn.addEventListener('click', () => addOptionRow(optsWrap, ''));
        }

        if (type === 'rating') {
            body.appendChild(el('div', {}, '<small class="note">Thumbs up / thumbs down + comment</small>'));
        }

        wrapper.appendChild(body);
        builder.appendChild(wrapper);

        // bindings
        delBtn.addEventListener('click', () => {
            wrapper.remove();
            renderPreview();
        });

        wrapper.querySelectorAll('.field-input').forEach((i) => i.addEventListener('input', renderPreview));
        wrapper.querySelectorAll('.option-input').forEach((i) => i.addEventListener('input', renderPreview));

        // drag events
        wrapper.addEventListener('dragstart', onDragStart);
        wrapper.addEventListener('dragend', onDragEnd);
        wrapper.addEventListener('dragover', onDragOver);
        wrapper.addEventListener('drop', onDrop);

        renderPreview();
        setTimeout(() => wrapper.querySelector('.field-input')?.focus(), 50);
    }

    function addOptionRow(optsWrap, value = '') {
        const row = el('div', { class: 'option-row' });
        const input = el('input', { type: 'text', class: 'option-input form-control ', value: value });
        input.placeholder = 'Option text';
        const del = el('button', { class: 'small-btn' }, '✕');
        row.appendChild(input);
        row.appendChild(del);
        optsWrap.insertBefore(row, optsWrap.querySelector('.add-opt'));
        input.addEventListener('input', renderPreview);
        del.addEventListener('click', () => {
            row.remove();
            renderPreview();
        });
    }

    // Drag & drop
    let dragEl = null;
    function onDragStart(e) {
        dragEl = this;
        this.classList.add('dragging');
        try {
            e.dataTransfer.setData('text/plain', this.dataset.id);
        } catch (err) {}
    }
    function onDragEnd() {
        this.classList.remove('dragging');
        dragEl = null;
    }
    function onDragOver(e) {
        e.preventDefault();
        const target = this;
        if (!dragEl || dragEl === target) return;
        const rect = target.getBoundingClientRect();
        const offset = e.clientY - rect.top;
        if (offset < rect.height / 2) target.parentNode.insertBefore(dragEl, target);
        else target.parentNode.insertBefore(dragEl, target.nextSibling);
    }
    function onDrop(e) {
        e.preventDefault();
        renderPreview();
    }

    function renderPreview() {
        previewTarget.innerHTML = '';
        const frame = el('div', { class: 'postchat-preview-frame' });

        Array.from(builder.children).forEach((wrapper, fieldIndex) => {
            const type = wrapper.dataset.type;
            const body = wrapper.querySelector('.form-field');
            let label = '',
                options = [],
                text = '';

            if (type === 'thankyou') {
                text =
                    body.querySelector('textarea[data-field-prop="text"]')?.value ||
                    'Thank you for the chat. Feel free to leave us any additional feedback.';
                const pf = el(
                    'div',
                    { class: 'preview-field user-like' },
                    `<div class="form-label">${escapeHtml(text)}</div>`
                );
                frame.appendChild(pf);
                return;
            }

            label = body.querySelector('input[data-field-prop="label"]')?.value || '';
            body.querySelectorAll('.option-input').forEach((o) => options.push(o.value || 'Option'));

            let node;

            if (type === 'question') {
                node = el(
                    'div',
                    { class: 'preview-field' },
                    `<div class="form-label">${escapeHtml(
                        label
                    )}</div><input placeholder="Your answer..." class="form-control"/>`
                );
            } else if (type === 'message') {
                node = el(
                    'div',
                    { class: 'preview-field' },
                    `<div class="form-label">${escapeHtml(
                        label
                    )}</div><textarea placeholder="Write your message..." rows="3" class="form-control"></textarea>`
                );
            } else if (type === 'choice') {
                const items = options
                    .map(
                        (o, i) =>
                            `<div class="form-check">
                            <input class="form-check-input" type="radio" name="field_${fieldIndex}" value="${escapeHtml(
                                o
                            )}">
                            <label class="form-check-label">${escapeHtml(o)}</label>
                         </div>`
                    )
                    .join('');
                node = el(
                    'div',
                    { class: 'preview-field' },
                    `<div class="form-label">${escapeHtml(label)}</div>${items}`
                );
            } else if (type === 'dropdown') {
                const optsHTML = options.map((o) => `<option>${escapeHtml(o)}</option>`).join('');
                node = el(
                    'div',
                    { class: 'preview-field' },
                    `<div class="form-label">${escapeHtml(label)}</div><select>${optsHTML}</select>`
                );
            } else if (type === 'multiple') {
                const items = options
                    .map(
                        (o) =>
                            `<label class="form-label">
                            <input type="checkbox" value="${escapeHtml(o)}"> ${escapeHtml(o)}
                         </label>`
                    )
                    .join('');
                node = el(
                    'div',
                    { class: 'preview-field' },
                    `<div class="form-label">${escapeHtml(label)}</div>${items}`
                );
            } else if (type === 'rating') {
                node = el(
                    'div',
                    { class: 'preview-field rating-group' },
                    `
                <div class="form-label">${escapeHtml(label || 'How would you rate this chat?')}</div>
                <div class="rating-icons">
                    <button class="rating-btn rating-positive" type="button">👍</button>
                    <button class="rating-btn rating-negative" type="button">👎</button>
                </div>
                `
                );
            }

            frame.appendChild(node);
        });

        // ⭐ Rating toggle
        frame.querySelectorAll('.rating-group .rating-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const group = this.closest('.rating-group');
                group.querySelectorAll('.rating-btn').forEach((b) => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // ---- Submit button (always at end) ----
        const submitWrap = el('div', { class: 'preview-submit-wrap' });
        submitWrap.innerHTML = `<button class="btn btn-primary preview-submit-btn">Submit</button>`;
        frame.appendChild(submitWrap);

        previewTarget.appendChild(frame);
    }

    function escapeHtml(s) {
        if (!s) return '';
        return s
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Export & load example
    const exportBtn = document.getElementById('export-json');
    const loadExampleBtn = document.getElementById('load-example');
    exportBtn?.addEventListener('click', () => {
        const data = Array.from(builder.children).map((w) => {
            const type = w.dataset.type;
            const body = w.querySelector('.form-field');
            const obj = { type };
            // ★ FIX: thankyou uses textarea, not input
            if (type === 'thankyou') obj.text = body.querySelector('textarea[data-field-prop="text"]')?.value || '';
            else obj.label = body.querySelector('input[data-field-prop="label"]')?.value || '';
            if (type === 'choice' || type === 'dropdown' || type === 'multiple') {
                obj.options = Array.from(body.querySelectorAll('.option-input')).map((i) => i.value.trim()).filter(Boolean);
            }
            return obj;
        });
        alert(JSON.stringify(data, null, 2));
    });

    loadExampleBtn?.addEventListener('click', () => {
        builder.innerHTML = '';
        addField('question', { label: 'Please enter your name' });
        addField('choice', { label: 'Was your issue resolved?', options: ['Yes', 'No'] });
        addField('rating', { label: 'How would you rate this chat?' });
        // addField('thankyou', { text: 'Thanks for your feedback!' });
        renderPreview();
    });

    // initialize with three field
    // ★ Load saved form config from database, or use defaults
    const savedConfig = window.__postChatFormConfig || [];
    
    if (savedConfig.length > 0) {
        // Load saved fields from database
        savedConfig.forEach(field => {
            addField(field.type, {
                label: field.label || '',
                text: field.text || '',
                options: field.options || [],
            });
        });
    } else {
        // Default fields for new setup
        addField('question', { label: 'Please enter your name' });
        addField('choice', { label: 'Was your issue resolved?', options: ['Yes', 'No'] });
        addField('rating', { label: 'How would you rate this chat?' });
    }
    
    // ★ Set the enabled toggle from saved state
    const savedEnabled = window.__postChatFormEnabled;
    const toggleEl = document.getElementById('postchat-enabled-toggle');
    if (toggleEl && typeof savedEnabled === 'boolean') {
        toggleEl.checked = savedEnabled;
        const label = document.getElementById('postchat-enabled-label');
        if (label) {
            label.textContent = savedEnabled ? 'Enabled' : 'Disabled';
            label.className = 'badge ' + (savedEnabled ? 'bg-success' : 'bg-secondary');
        }
    }
    
    const currentPath = window.location.pathname;
    if (currentPath.endsWith('/settings/post-chat-form')) {
        switchView('chat');
        renderPreview();
    }
});

//
