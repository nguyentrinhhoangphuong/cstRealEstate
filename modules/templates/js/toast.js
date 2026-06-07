var toastContainer;

function initToast(position) {
    if (toastContainer) return;
    toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container ' + (position ?? 'top-right');
    document.body.appendChild(toastContainer);
}

function showToast(msg, position) {
    if (!toastContainer) initToast(position);
    var el = document.createElement('div');
    el.textContent = msg;
    el.className = 'toast';

    if (toastContainer.classList.contains('bottom-right') || toastContainer.classList.contains('bottom-left')) {
        toastContainer.appendChild(el);
    } else {
        toastContainer.insertBefore(el, toastContainer.firstChild);
    }

    requestAnimationFrame(function () {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });

    setTimeout(function () {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        setTimeout(function () { el.remove(); }, 300);
    }, 3000);
}