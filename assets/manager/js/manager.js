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
