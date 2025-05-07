document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarToggleOpen = document.getElementById('sidebarToggleOpen');
    const sidebarToggleClose = document.getElementById('sidebarToggleClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    
    // Check if elements exist
    if (!sidebarToggle || !sidebar || !sidebarBackdrop) {
        console.error('Required sidebar elements not found');
        return;
    }
    
    // Function to open sidebar
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarToggleOpen.classList.add('hidden');
        sidebarToggleClose.classList.remove('hidden');
        sidebarBackdrop.classList.remove('hidden');
        setTimeout(() => {
            sidebarBackdrop.classList.remove('opacity-0');
        }, 10);
    }
    
    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarToggleOpen.classList.remove('hidden');
        sidebarToggleClose.classList.add('hidden');
        sidebarBackdrop.classList.add('opacity-0');
        setTimeout(() => {
            sidebarBackdrop.classList.add('hidden');
        }, 300);
    }
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });
    
    // Close sidebar when clicking on backdrop
    sidebarBackdrop.addEventListener('click', closeSidebar);
    
    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
            closeSidebar();
        }
    });
    
    // Close sidebar on window resize (if screen becomes large)
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            if (!sidebar.classList.contains('-translate-x-full') && !sidebar.classList.contains('lg:translate-x-0')) {
                closeSidebar();
            }
        }
    });
    
    // User dropdown functionality
    const userMenuButton = document.getElementById('user-menu-button');
    const dropdownUser = document.getElementById('dropdown-user');
    
    if (userMenuButton && dropdownUser) {
        userMenuButton.addEventListener('click', function() {
            dropdownUser.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !dropdownUser.contains(event.target)) {
                dropdownUser.classList.add('hidden');
            }
        });
    }
});