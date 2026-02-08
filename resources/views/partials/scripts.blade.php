<script>
    // Theme Management
    (function() {
        const html = document.documentElement;
        const saved = localStorage.getItem('theme');
        if (saved === 'dark') {
            html.classList.remove('light');
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
            html.classList.add('light');
        }
        updateThemeIcons();
    })();

    function toggleTheme() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');
        if (isDark) {
            html.classList.remove('dark');
            html.classList.add('light');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.remove('light');
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateThemeIcons();
    }

    function updateThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const lightIcon = document.querySelector('.theme-icon-light');
        const darkIcon = document.querySelector('.theme-icon-dark');
        if (lightIcon && darkIcon) {
            lightIcon.style.display = isDark ? 'none' : 'block';
            darkIcon.style.display = isDark ? 'block' : 'none';
        }
    }

    // Sidebar Toggle (Mobile)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar.classList.toggle('open');
        if (overlay) {
            overlay.classList.toggle('active');
        }
    }

    // User Dropdown
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('active');
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // Auto-dismiss flash messages
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('[id^="flash-"]');
        flashMessages.forEach(function(msg) {
            setTimeout(function() {
                if (msg && msg.parentNode) {
                    msg.style.transition = 'opacity 0.3s';
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        if (msg.parentNode) msg.remove();
                    }, 300);
                }
            }, 5000);
        });
    });

    // Confirm Delete
    function confirmDelete(formId, message) {
        const msg = message || 'Are you sure you want to delete this item?';
        const overlay = document.getElementById('globalModal');
        const title = document.getElementById('modalTitle');
        const body = document.getElementById('modalBody');
        const confirm = document.getElementById('modalConfirm');

        if (overlay && title && body && confirm) {
            title.textContent = 'Confirm Delete';
            body.innerHTML = '<p style="color: var(--muted-foreground); font-size: 0.875rem;">' + msg + '</p>';
            confirm.className = 'btn btn-danger';
            confirm.textContent = 'Delete';
            confirm.onclick = function() {
                document.getElementById(formId).submit();
            };
            overlay.classList.add('active');
        } else {
            if (confirm(msg)) {
                document.getElementById(formId).submit();
            }
        }
    }

    // Close Modal
    function closeModal() {
        const overlay = document.getElementById('globalModal');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
</script>
