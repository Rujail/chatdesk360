(function () {
    if (window.LiveChatWidgetLoaded) return;
    window.LiveChatWidgetLoaded = true;

    console.log("Chat widget script started loading...");

    // ==============================================
    // Firebase Config (tumhara original)
    // ==============================================
    const firebaseConfig = {
        apiKey: "AIzaSyCe43FFxqrgDdEoGrV-SF-SilfsTxyCmrY",
        authDomain: "chatsystem-a5147.firebaseapp.com",
        databaseURL: "https://chatsystem-a5147-default-rtdb.firebaseio.com",
        projectId: "chatsystem-a5147",
        storageBucket: "chatsystem-a5147.firebasestorage.app",
        messagingSenderId: "744445538894",
        appId: "1:744445538894:web:58abf53e214df9d05b491f"
    };

    // ==============================================
    // Firebase Compat Scripts Load
    // ==============================================
    function loadFirebase(callback) {
        if (window.firebase && window.firebase.database) {
            callback();
            return;
        }

        const load = (url, next) => {
            const s = document.createElement('script');
            s.src = url;
            s.async = true;
            s.onload = next;
            s.onerror = () => console.error("Failed to load: " + url);
            document.head.appendChild(s);
        };

        load(
            'https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js',
            () => load(
                'https://www.gstatic.com/firebasejs/10.14.1/firebase-database-compat.js',
                callback
            )
        );
    }

    // ==============================================
    // Wait for DOM + Firebase
    // ==============================================
    function initWidgetWhenReady() {
        if (document.readyState !== 'complete') {
            setTimeout(initWidgetWhenReady, 100);
            return;
        }

        if (!window.firebase || !window.firebase.database) {
            console.warn("Firebase not ready yet → retrying...");
            setTimeout(initWidgetWhenReady, 300);
            return;
        }

        firebase.initializeApp(firebaseConfig);
        const db = firebase.database();

        console.log("Firebase initialized successfully");

        // Visitor ID
        function getVisitorId() {
            let id = localStorage.getItem('chat_visitor_id');
            if (!id) {
                id = 'vis_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                localStorage.setItem('chat_visitor_id', id);
            }
            return id;
        }

        const visitorId = getVisitorId();
        const currentChatId = 'chat_' + visitorId;

        // ==============================================
        // DOM Elements with null-safety
        // ==============================================
        const $ = id => document.getElementById(id);

        const elements = {
            bubble: $('chat-bubble'),
            popup: $('chat-popup'),
            closePopup: $('close-popup'),
            minimize: $('minimize-chat'),
            input: $('chat-input'),
            submit: $('chat-submit'),
            messages: $('chat-messages'),
            startBtn: $('start-chat-btn'),
            backBtn: $('back-to-home-btn'),
            homeTab: $('home-tab-btn'),
            chatTab: $('chat-tab-btn'),
            attachBtn: $('attach-btn'),
            emojiBtn: $('emoji-btn'),
            attachMenu: $('attachment-menu'),
            emojiMenu: $('emoji-menu'),
            fileInput: $('hidden-file-input'),
            preview: $('file-upload-preview'),
            modal: $('image-preview-modal'),
            modalImg: $('modal-image'),
            closeModal: $('close-modal-btn')
        };

        // Agar koi critical element missing ho to error log karo aur ruk jao
        if (!elements.bubble || !elements.popup || !elements.messages) {
            console.error("Critical chat elements missing in DOM. Check your HTML IDs.");
            return;
        }

        let filesToUpload = [];
        let isOpen = false;

        // ==============================================
        // Helper Functions
        // ==============================================
        function togglePopup() {
            isOpen = !isOpen;
            elements.popup.classList.toggle('hidden', !isOpen);
            if (isOpen) switchTo('chat');
        }

        function switchTo(view) {
            const homeScreen = $('chat-home-screen');
            const convScreen = $('chat-conversation');
            const footer = $('chat-footer');

            if (view === 'home') {
                homeScreen?.classList.remove('hidden');
                convScreen?.classList.add('hidden');
                footer?.classList.remove('hidden');
                elements.homeTab?.classList.add('active-tab');
                elements.chatTab?.classList.remove('active-tab');
            } else {
                homeScreen?.classList.add('hidden');
                convScreen?.classList.remove('hidden');
                footer?.classList.add('hidden');
                elements.homeTab?.classList.remove('active-tab');
                elements.chatTab?.classList.add('active-tab');
                elements.input?.focus();
            }
        }

        function closeMenus() {
            elements.attachMenu?.classList.add('hidden');
            elements.emojiMenu?.classList.add('hidden');
            elements.attachBtn?.classList.remove('active');
            elements.emojiBtn?.classList.remove('active');
        }

        function escape(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function appendMsg(text, from = 'bot', imgUrl = null) {
            if (!elements.messages) return;

            const div = document.createElement('div');
            div.className = `message-container ${from}`;

            let bubble = `<div class="message-bubble ${from}">${escape(text)}</div>`;
            if (imgUrl) {
                bubble = `<div class="message-bubble ${from}"><img src="${imgUrl}" class="chat-image" alt="image"></div>`;
            }

            div.innerHTML = bubble + (from === 'user' ? '<div class="read-status">Read</div>' : '');
            elements.messages.appendChild(div);
            elements.messages.scrollTop = elements.messages.scrollHeight;

            // Image click → modal
            div.querySelectorAll('.chat-image').forEach(img => {
                img.onclick = () => {
                    if (elements.modalImg) elements.modalImg.src = img.src;
                    elements.modal?.classList.remove('hidden');
                };
            });
        }

        // ==============================================
        // Firebase Real-time
        // ==============================================
        const chatRef = db.ref(`chats/${currentChatId}/messages`);

        chatRef.on('child_added', snap => {
            const msg = snap.val();
            if (!msg) return;

            if (msg.sender === 'visitor') {
                // already shown locally
            } else {
                if (msg.type === 'image') {
                    appendMsg(msg.text || 'Image', 'bot', msg.imageUrl);
                } else {
                    appendMsg(msg.text || '', 'bot');
                }
            }
        });

        // ==============================================
        // Send Logic
        // ==============================================
        function send() {
            const text = elements.input?.value.trim();
            if (!text && filesToUpload.length === 0) return;

            if (filesToUpload.length > 0) {
                filesToUpload.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        chatRef.push({
                            sender: 'visitor',
                            type: 'image',
                            text: file.name,
                            imageUrl: e.target.result,
                            timestamp: Date.now()
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (text) {
                chatRef.push({
                    sender: 'visitor',
                    type: 'text',
                    text: text,
                    timestamp: Date.now()
                });
                appendMsg(text, 'user');
            }

            if (elements.input) elements.input.value = '';
            filesToUpload = [];
            elements.preview?.classList.add('hidden');
        }

        // ==============================================
        // Event Listeners (safe)
        // ==============================================
        elements.bubble?.addEventListener('click', togglePopup);
        elements.closePopup?.addEventListener('click', togglePopup);
        elements.minimize?.addEventListener('click', togglePopup);

        elements.startBtn?.addEventListener('click', () => switchTo('chat'));
        elements.backBtn?.addEventListener('click', () => switchTo('home'));
        elements.homeTab?.addEventListener('click', () => switchTo('home'));
        elements.chatTab?.addEventListener('click', () => switchTo('chat'));

        elements.submit?.addEventListener('click', send);
        elements.input?.addEventListener('keypress', e => {
            if (e.key === 'Enter') send();
        });

        elements.attachBtn?.addEventListener('click', e => {
            e.stopPropagation();
            closeMenus();
            elements.attachMenu?.classList.toggle('hidden');
            elements.attachBtn?.classList.toggle('active');
        });

        elements.emojiBtn?.addEventListener('click', e => {
            e.stopPropagation();
            closeMenus();
            elements.emojiMenu?.classList.toggle('hidden');
            elements.emojiBtn?.classList.toggle('active');
        });

        elements.fileInput?.addEventListener('change', e => {
            filesToUpload = Array.from(e.target.files || []).slice(0, 5);
            e.target.value = '';

            if (filesToUpload.length > 0 && elements.preview) {
                elements.preview.classList.remove('hidden');
                const list = elements.preview.querySelector('.preview-list');
                if (list) {
                    list.innerHTML = '';
                    filesToUpload.forEach((f, i) => {
                        const item = document.createElement('div');
                        item.className = 'file-preview-item';
                        if (f.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(f);
                            item.appendChild(img);
                        } else {
                            item.textContent = `.${f.name.split('.').pop().toUpperCase()}`;
                        }
                        const delBtn = document.createElement('button');
                        delBtn.textContent = '×';
                        delBtn.className = 'delete-file-btn';
                        delBtn.onclick = () => {
                            filesToUpload.splice(i, 1);
                            item.remove();
                            if (filesToUpload.length === 0) elements.preview.classList.add('hidden');
                        };
                        item.appendChild(delBtn);
                        list.appendChild(item);
                    });
                }
            }
        });

        elements.closeModal?.addEventListener('click', () => {
            elements.modal?.classList.add('hidden');
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('#attach-btn, #emoji-btn, #attachment-menu, #emoji-menu')) {
                closeMenus();
            }
        });

        // Initial welcome
        setTimeout(() => {
            appendMsg("Hello! How can I help you today?", 'bot');
        }, 800);

        console.log("Chat widget fully initialized → Visitor:", visitorId);
    }

    // Start the process
    loadFirebase(initWidgetWhenReady);

})();