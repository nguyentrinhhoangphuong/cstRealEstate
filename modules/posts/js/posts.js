document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.status-toggle').forEach(function (el) {
        el.addEventListener('click', function () {
            var id = this.dataset.id;
            var self = this;
            fetch('posts/toggle_status/' + id, {
                method: 'POST'
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        self.dataset.status = data.status;
                        self.textContent = data.label;
                        self.className = 'status-toggle post-update-status ' + (data.status === 'publish' ? 'posts-publish' : 'posts-draft');
                        showToast(`${data.title} đã chuyển sang ${data.label}`, 'top-right');
                    }
                });
        });
    });
});


document.getElementById('typeFilter').addEventListener('change', filterPosts);
document.getElementById('statusFilter').addEventListener('change', filterPosts);
function filterPosts() {
    var type = document.getElementById('typeFilter').value;
    var status = document.getElementById('statusFilter').value;
    var params = [];
    if (type) params.push('type=' + type);
    if (status) params.push('status=' + status);
    var url = 'posts/manage';
    if (params.length) url += '?' + params.join('&');
    window.location.href = url;
}