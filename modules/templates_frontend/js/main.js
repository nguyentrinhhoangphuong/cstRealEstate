// Navbar scroll effect
window.addEventListener('scroll', () => {
    document.getElementById('main-nav')
        .classList.toggle('scrolled', window.scrollY > 20);
});

// Search overlay
function openSearch() {
    const el = document.getElementById('search-overlay');
    el.style.display = 'flex';
    setTimeout(() => document.getElementById('search-input').focus(), 50);
}
function closeSearch() {
    document.getElementById('search-overlay').style.display = 'none';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSearch();
});
document.querySelectorAll('[title="Tìm kiếm"]')
    .forEach(btn => btn.addEventListener('click', openSearch));

// Search form submit
document.getElementById('search-input')?.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = '<?= BASE_URL ?>listings?q=' + encodeURIComponent(this.value.trim());
    }
});
