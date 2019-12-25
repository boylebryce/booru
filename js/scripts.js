window.onload = init;

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

function changeDisplay(selector, displayType) {
    document.querySelector(selector).style.display = displayType;
}

function init() {
    let upload_input = document.querySelector('#upload-input');

    upload_input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            changeDisplay('#upload-submit', 'inline-block');
        }
    });

    window.addEventListener('paste', event => {
        let items = (event.clipboardData || event.originalEvent.clipboardData).items;
        let b64png = null;

        // check all items on clipboard for images and store image in b64png
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') === 0) {
                // image from paste event will be a base64 encoded string
                b64png = items[i].getAsFile();
            }
        }

        // if image found on clipboard, convert to dataURL to display on page
        if (b64png !== null) {
            let reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById("paste-preview").src = event.target.result;

                // use DataTransfer object to wrap image file in FileList object
                const image_file = dataURLtoFile(event.target.result, 'upload.png');
                let my_data = new DataTransfer();
                my_data.items.add(image_file);

                // insert FileList into upload form
                upload_input.files = my_data.files;
                changeDisplay('#upload-submit', 'inline-block');
            }
            reader.readAsDataURL(b64png);
            changeDisplay('#paste-preview-area', 'block');
        }
    });
}