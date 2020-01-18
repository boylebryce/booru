window.onload = init;

const protocol = document.location.protocol;
const hostname = document.location.hostname;
const webroot = protocol + '//' + hostname;

function init() {
    const params            = new URLSearchParams(document.location.href.split('?')[1]);
    const imageID           = params.get('img_id');
    const imageDisplay      = document.querySelector('#image-display');
    const imageControls     = document.querySelector('#image-controls');
    const addTagsForm       = document.querySelector('#add-tags-form');
    const addTagsInput      = document.querySelector('#add-tags-input');
    const currentTagList    = document.querySelector('#current-tags');
    const deleteTagsButton  = document.querySelector('#delete-tags-submit');
    const deleteImageButton = document.querySelector('#delete-image-button');

    loadImageData(imageID, imageControls, imageDisplay, currentTagList);

    addTagsForm.addEventListener('submit', event => {
        event.preventDefault();

        const formData = new FormData();
        formData.append('img_id', imageID);
        formData.append('tags', addTagsInput.value);

        fetch('/booru/include/add_tags.php', {
            method: 'POST',
            body: formData,
        }).then(response => {
            loadImageData(imageID, imageControls, imageDisplay, currentTagList);
        });
    });

    deleteTagsButton.addEventListener('click', event => {
        event.preventDefault();

        let deleteTags = [];
        for (let i = 0; i < currentTagList.childElementCount; ++i) {
            if (currentTagList.children[i].firstChild.checked) {
                deleteTags.push(currentTagList.children[i].firstChild.value);
            }
        }

        const formData = new FormData();
        formData.append('img_id', imageID);
        formData.append('tag_ids', deleteTags);

        fetch('/booru/include/delete_tags.php', {
            method: 'POST',
            body: formData,
        }).then(response => {
            loadImageData(imageID, imageControls, imageDisplay, currentTagList);
        });
    });

    deleteImageButton.addEventListener('click', event => {
        if(confirm("Are you sure you want to delete this image?")) {
            const formData = new FormData();
            formData.append('img_id', imageID);

            fetch('/booru/include/delete_image.php', {
                method: 'POST',
                body: formData,
            }).then(response => {
                return response.json();
            }).then(response => {
                if (response['error']) {
                    const node = document.createElement('p');
                    node.textContent = response['error'];
                }
                else {
                    alert('Image deleted!');
                    window.location.replace('/booru/images.php');
                }
            });
        }
    })
}

async function fetchImage(imageID) {
    const response = await fetch('/booru-api/image.php?img_id=' + imageID);
    return response.json();
}

function loadImageData(imageID, imageControls, imageDisplay, currentTagList) {
    fetchImage(imageID).then(image => {
        // clear current tags on page
        while (currentTagList.firstChild) {
            currentTagList.firstChild.remove();
        }

        if (image['error']) {
            imageControls.style.display = 'none';
            const node = document.createElement('p');
            node.textContent = 'Image ID ' + imageID + ' does not exist.';
            document.querySelector('main').appendChild(node);
        }
        else {
            imageDisplay.src = '/booru/img/' + image.img_path;
            imageDisplay.alt = 'Image with ID number ' + imageID;

            image.tags.forEach(tag => {
                const link = document.createElement('a');
                link.href = '/booru/images.php?search=' + tag.tag_label;
                link.textContent = tag.tag_label;

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.value = tag.tag_id;

                const listItem = document.createElement('li');
                listItem.appendChild(input);
                listItem.appendChild(link);
                currentTagList.appendChild(listItem);
            });
        }
    });
}