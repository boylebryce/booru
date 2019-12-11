window.onload = init;

function retrieveImageBlobFromClipboard(pasteEvent, callback) {
    // nothing on clipboard
    if (pasteEvent.clipboardData == false) {
        if (typeof(callback) == 'function') {
            callback(undefined);
        }
    }

    let items = pasteEvent.clipboardData.items;

    // ???
    if (items == undefined) {
        if (typeof(callback) == 'function') {
            callback(undefined)
        }
    }

    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') != -1) {
            let blob = items[i].getAsFile();

            if (typeof(callback) == 'function') {
                callback(blob);
            }
        }
    }
}

function init() {


    window.addEventListener('paste', function(event) {
        retrieveImageBlobFromClipboard(event, function(imageBlob) {
            if (imageBlob) {
                let canvas = document.getElementById('img-canvas');
                let ctx = canvas.getContext('2d');

                let img = new Image();

                img.onload = function() {
                    canvas.width = this.width;
                    canvas.height = this.height;
                    ctx.drawImage(img, 0, 0);
                };

                let URLObj = window.URL || window.webkitURL;
                img.src = URLObj.createObjectURL(imageBlob);
            }
        });
    },  false);


}