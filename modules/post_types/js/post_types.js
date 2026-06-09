document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('sortable-types');
    if (!tbody) return;
    var dragSrcEl = null;
    tbody.addEventListener('dragstart', function (e) {
        var tr = e.target.closest('tr');
        if (!tr) return;
        dragSrcEl = tr;
        tr.style.opacity = '0.4';
    });
    tbody.addEventListener('dragend', function (e) {
        var tr = e.target.closest('tr');
        if (!tr) return;
        tr.style.opacity = '1';
    });
    tbody.addEventListener('dragover', function (e) {
        e.preventDefault();
        var tr = e.target.closest('tr');
        if (!tr || tr === dragSrcEl) return;
        var rect = tr.getBoundingClientRect();
        var midY = rect.top + rect.height / 2;
        if (e.clientY < midY) {
            tr.parentNode.insertBefore(dragSrcEl, tr);
        } else {
            tr.parentNode.insertBefore(dragSrcEl, tr.nextSibling);
        }
    });
    tbody.addEventListener('drop', function (e) {
        e.preventDefault();
        saveOrder();
    });
    function saveOrder() {
        var ids = [];
        tbody.querySelectorAll('tr').forEach(function (tr) {
            ids.push(tr.dataset.id);
        });
        var form = document.createElement('form');
        form.method = 'POST';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids';
        input.value = JSON.stringify(ids);
        form.appendChild(input);
        document.body.appendChild(form);
        fetch('post_types/reorder', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'ids=' + encodeURIComponent(JSON.stringify(ids))
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) showToast('Đã cập nhật thứ tự');
            });
    }
});