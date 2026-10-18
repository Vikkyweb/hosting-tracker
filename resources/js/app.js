const sidebar = document.getElementById('admin-sidebar');
const backdrop = document.getElementById('sidebar-backdrop');
const toggle = document.getElementById('sidebar-toggle');

function openSidebar() {
    if (!sidebar || !backdrop || !toggle) {
        return;
    }

    sidebar.classList.remove('-translate-x-full');
    backdrop.classList.remove('hidden');
    toggle.setAttribute('aria-expanded', 'true');
}

function closeSidebar() {
    if (!sidebar || !backdrop || !toggle) {
        return;
    }

    sidebar.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
    toggle.setAttribute('aria-expanded', 'false');
}

if (sidebar && backdrop && toggle) {
    toggle.addEventListener('click', () => {
        if (backdrop.classList.contains('hidden')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    backdrop.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('[data-sidebar-close]').forEach((element) => {
        element.addEventListener('click', closeSidebar);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) {
            closeSidebar();
        }
    });
}
