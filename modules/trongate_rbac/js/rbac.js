(function() {
    'use strict';

    // Group toggle checkboxes: check/uncheck all permissions in a module group
    document.querySelectorAll('.rbac-group-toggle').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var module = this.getAttribute('data-module');
            var checked = this.checked;
            document.querySelectorAll('.rbac-perm-cb[data-module="' + module + '"]').forEach(function(permCb) {
                permCb.checked = checked;
            });
        });
    });

    // When individual permissions change, update the group toggle state
    document.querySelectorAll('.rbac-perm-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var module = this.getAttribute('data-module');
            var allCbs = document.querySelectorAll('.rbac-perm-cb[data-module="' + module + '"]');
            var allChecked = true;
            allCbs.forEach(function(permCb) {
                if (!permCb.checked) allChecked = false;
            });
            var groupToggle = document.querySelector('.rbac-group-toggle[data-module="' + module + '"]');
            if (groupToggle) {
                groupToggle.checked = allChecked;
            }
        });
    });
})();
