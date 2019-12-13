window.onload = init;

function init() {
    window.addEventListener("paste", function(event) {
        let items = (event.clipboardData || event.originalEvent.clipboardData).items;
        let blob = null;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf("image") === 0) {
                blob = items[i].getAsFile();
            }
        }

        if (blob !== null) {
            let reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById("pastedImage").src = event.target.result;
                document.getElementById("imageData").value = document.getElementById("pastedImage").src;
            };
            reader.readAsDataURL(blob);
            document.getElementById("imageActions").style.display = "block";
        }
    });

    document.getElementById("resetImage").addEventListener("click", function() {
        document.getElementById("pastedImage").src = "";
        document.getElementById("imageActions").style.display = "none";
    });
}