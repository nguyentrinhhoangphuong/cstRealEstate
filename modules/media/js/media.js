(function () {
    var uploadArea = document.getElementById('uploadArea');
    var fileInput = document.getElementById('fileInput');
    var uploadPreviews = document.getElementById('uploadPreviews');
    var uploadPrompt = document.getElementById('uploadPrompt');
    var uploadFooter = document.getElementById('uploadFooter');
    var fileCount = document.getElementById('fileCount');

    if (uploadArea) {
        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });

        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function () {
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                showPreviews(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                showPreviews(this.files);
            }
        });
    }

    function showPreviews(files) {
        uploadPrompt.style.display = 'none';
        uploadPreviews.innerHTML = '';
        for (var i = 0; i < files.length; i++) {
            (function (file) {
                var div = document.createElement('div');
                div.className = 'preview-item';
                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        div.innerHTML = '<img src="' + e.target.result + '" alt=""><span class="preview-name">' + file.name + '</span>';
                    };
                    reader.readAsDataURL(file);
                } else {
                    div.innerHTML = '<div class="preview-icon">' + file.name.split('.').pop().toUpperCase() + '</div><span class="preview-name">' + file.name + '</span>';
                }
                uploadPreviews.appendChild(div);
            })(files[i]);
        }
        if (fileCount) fileCount.textContent = files.length + ' file(s)';
        if (uploadFooter) uploadFooter.style.display = 'flex';
    }

    var newFolderBtn = document.getElementById('newFolderBtn');
    var newFolderForm = document.getElementById('newFolderForm');
    var cancelFolderBtn = document.getElementById('cancelFolderBtn');

    if (newFolderBtn) {
        newFolderBtn.addEventListener('click', function (e) {
            e.preventDefault();
            newFolderForm.style.display = 'block';
            newFolderBtn.style.display = 'none';
        });
    }

    if (cancelFolderBtn) {
        cancelFolderBtn.addEventListener('click', function () {
            newFolderForm.style.display = 'none';
            newFolderBtn.style.display = 'inline-flex';
        });
    }

    var renameLinks = document.querySelectorAll('.rename-folder');
    for (var i = 0; i < renameLinks.length; i++) {
        renameLinks[i].addEventListener('click', function (e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            var currentName = this.getAttribute('data-name');
            var newName = prompt('Rename folder:', currentName);
            if (newName && newName !== currentName) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'media/rename_folder/' + id, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) location.reload();
                };
                xhr.send('name=' + encodeURIComponent(newName));
            }
        });
    }

    var dragMedia = null;

    var mediaItems = document.querySelectorAll('.media-item[draggable]');
    for (var i = 0; i < mediaItems.length; i++) {
        mediaItems[i].addEventListener('dragstart', function (e) {
            dragMedia = this.getAttribute('data-id');
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
        });
        mediaItems[i].addEventListener('dragend', function () {
            this.classList.remove('dragging');
            dragMedia = null;
        });
    }

    var folderDrops = document.querySelectorAll('.folder-drag');
    for (var i = 0; i < folderDrops.length; i++) {
        (function (el) {
            var link = el.querySelector('.folder-link');
            if (!link) return;
            var folderId = link.getAttribute('data-folder');

            el.addEventListener('dragover', function (e) {
                if (dragMedia) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    el.classList.add('drag-over');
                }
            });

            el.addEventListener('dragleave', function () {
                el.classList.remove('drag-over');
            });

            el.addEventListener('drop', function (e) {
                e.preventDefault();
                el.classList.remove('drag-over');
                if (dragMedia) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'media/move_to_folder', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) location.reload();
                    };
                    xhr.send('media_id=' + dragMedia + '&folder_id=' + folderId);
                }
            });
        })(folderDrops[i]);
    }
})();
