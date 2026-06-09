(function () {
    const key = 'smartroom.sidebar.collapsed';
    const toggle = document.getElementById('sidebar-toggle');

    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        localStorage.setItem(key, collapsed ? '1' : '0');
    }

    setCollapsed(localStorage.getItem(key) === '1');
    toggle?.addEventListener('click', function () {
        setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
    });
})();
