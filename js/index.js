window.onload = init;

const protocol = document.location.protocol;
const hostname = document.location.hostname;
const webroot = protocol + '//' + hostname;

// TODO: change this to get valid extensions from API
const validExtensions = ['png', 'jpg', 'jpeg'];

function init() {
    const uploadForm = document.querySelector('#upload-form');
    const uploadInput = document.querySelector('#upload-input');
    const fileNameDisplay = document.querySelector('#file-name');
    const imagePreview = document.querySelector('#paste-preview');

    uploadInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            changeDisplay('#upload-submit', 'inline-block');
            changeDisplay('#paste-instructions', 'none');
            fileNameDisplay.textContent = this.files[0].name;
        }
    });

    uploadForm.addEventListener('submit', event => {
        event.preventDefault();
        if (uploadInput.files && uploadInput.files[0]) {
            const file = uploadInput.files[0];
            const formData = new FormData();
            formData.append('userfile', file);

            fetch(webroot + '/booru-api/upload.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                return response.json();
            }).then(response => {
                const node = document.createElement('p');
                if (response['error']) {
                    node.textContent = response['error'];
                }
                else {
                    node.textContent = 'Upload successful!';
                }
                uploadForm.append(node);
            });
        }
    })

    window.addEventListener('paste', event => {
        let items = (event.clipboardData || event.originalEvent.clipboardData).items;
        let image = null;
        let extension = null;

        // get image from clipboard
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') === 0) {
                image = items[i].getAsFile();
                extension = items[i].type.split('/')[1];
            }
        }

        // TODO: display errors to user (no image or invalid extension)
        if (image !== null) {
            // get image as data URL for preview
            const reader = new FileReader();
            reader.onload = function(event) {
                imagePreview.src = event.target.result;

                // wrap image in FileList object to insert into uploadInput
                const imageFile = dataURLtoFile(event.target.result, 'upload.' + extension);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(imageFile);

                uploadInput.files = dataTransfer.files;
                changeDisplay('#upload-submit', 'inline-block');
                fileNameDisplay.textContent = uploadInput.files[0].name;
            }
            reader.readAsDataURL(image);
            changeDisplay('#paste-preview-area', 'block');
            changeDisplay('#paste-instructions', 'none');
        }
    });
}

function changeDisplay(selector, displayType) {
    document.querySelector(selector).style.display = displayType;
}

function dataURLtoFile(dataurl, filename) {
    let arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
    
    while(n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], filename, {type:mime});
}