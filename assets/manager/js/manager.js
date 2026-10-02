(() => {
    const sidebar = document.getElementById('managerSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !toggle || !overlay) return;

    const close = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    };

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('open');
        overlay.classList.toggle('show', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    overlay.addEventListener('click', close);
    window.addEventListener('resize', () => {
        if (window.innerWidth > 860) close();
    });
})();


(() => {
    const textareas = document.querySelectorAll('textarea[data-rich-editor]');
    if (!textareas.length) return;

    const allowedTags = new Set(['P','BR','STRONG','B','EM','I','U','H2','H3','H4','UL','OL','LI','BLOCKQUOTE','A','HR']);
    const dropTags = new Set(['SCRIPT','STYLE','IFRAME','OBJECT','EMBED','FORM','INPUT','BUTTON','TEXTAREA','SELECT','OPTION','SVG','MATH']);

    const escapeHtml = (value) => value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const plainToHtml = (value) => escapeHtml(value).replace(/\r?\n/g, '<br>');

    const safeHref = (href) => {
        const value = (href || '').trim();
        if (!value) return false;
        if (value.startsWith('#') || value.startsWith('/')) return true;

        try {
            const parsed = new URL(value, window.location.origin);
            return ['http:', 'https:', 'mailto:', 'tel:'].includes(parsed.protocol);
        } catch {
            return false;
        }
    };

    const sanitizeClient = (html) => {
        const doc = new DOMParser().parseFromString('<div id="root">' + html + '</div>', 'text/html');
        const root = doc.getElementById('root');
        if (!root) return '';

        const clean = (parent) => {
            [...parent.childNodes].forEach((node) => {
                if (node.nodeType === Node.COMMENT_NODE) {
                    node.remove();
                    return;
                }

                if (node.nodeType !== Node.ELEMENT_NODE) return;

                if (dropTags.has(node.tagName)) {
                    node.remove();
                    return;
                }

                if (!allowedTags.has(node.tagName)) {
                    clean(node);
                    node.replaceWith(...node.childNodes);
                    return;
                }

                const href = node.tagName === 'A' ? node.getAttribute('href') : null;
                const target = node.tagName === 'A' ? node.getAttribute('target') : null;

                [...node.attributes].forEach((attr) => node.removeAttribute(attr.name));

                if (node.tagName === 'A' && href && safeHref(href)) {
                    node.setAttribute('href', href);
                    if (target === '_blank') {
                        node.setAttribute('target', '_blank');
                        node.setAttribute('rel', 'noopener noreferrer');
                    }
                }

                clean(node);
            });
        };

        clean(root);
        return root.innerHTML;
    };

    const commandButton = (label, title, command, value = null) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'rich-editor-btn';
        button.textContent = label;
        button.title = title;
        button.addEventListener('click', () => {
            document.execCommand(command, false, value);
        });
        return button;
    };

    textareas.forEach((textarea) => {
        const wasRequired = textarea.required;
        textarea.required = false;
        textarea.classList.add('rich-editor-source');

        const shell = document.createElement('div');
        shell.className = 'rich-editor';

        const toolbar = document.createElement('div');
        toolbar.className = 'rich-editor-toolbar';
        toolbar.setAttribute('aria-label', 'Toolbar editor');

        [
            ['P', 'Paragraf', 'formatBlock', 'P'],
            ['H2', 'Heading 2', 'formatBlock', 'H2'],
            ['H3', 'Heading 3', 'formatBlock', 'H3'],
            ['B', 'Bold', 'bold'],
            ['I', 'Italic', 'italic'],
            ['U', 'Underline', 'underline'],
            ['• List', 'Bullet list', 'insertUnorderedList'],
            ['1. List', 'Numbered list', 'insertOrderedList'],
            ['❝', 'Kutipan', 'formatBlock', 'BLOCKQUOTE'],
        ].forEach((item) => toolbar.appendChild(commandButton(...item)));

        const linkButton = document.createElement('button');
        linkButton.type = 'button';
        linkButton.className = 'rich-editor-btn';
        linkButton.textContent = 'Link';
        linkButton.title = 'Tambahkan link';
        linkButton.addEventListener('click', () => {
            const href = window.prompt('Masukkan URL/link:');
            if (!href) return;
            if (!safeHref(href)) {
                window.alert('Link tidak valid. Gunakan http(s), mailto, tel, /path, atau #anchor.');
                return;
            }
            document.execCommand('createLink', false, href);
        });
        toolbar.appendChild(linkButton);
        toolbar.appendChild(commandButton('Clear', 'Hapus format', 'removeFormat'));

        const editor = document.createElement('div');
        editor.className = 'rich-editor-area';
        editor.contentEditable = 'true';
        editor.setAttribute('role', 'textbox');
        editor.setAttribute('aria-multiline', 'true');

        const initial = textarea.value || '';
        editor.innerHTML = sanitizeClient(initial.includes('<') ? initial : plainToHtml(initial));

        const sync = () => {
            textarea.value = sanitizeClient(editor.innerHTML).trim();
        };

        editor.addEventListener('input', sync);
        editor.addEventListener('blur', sync);

        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', (event) => {
                sync();
                if (wasRequired && editor.textContent.trim() === '') {
                    event.preventDefault();
                    editor.focus();
                    window.alert('Isi konten wajib diisi.');
                }
            });
        }

        shell.appendChild(toolbar);
        shell.appendChild(editor);
        textarea.insertAdjacentElement('afterend', shell);
    });
})();
