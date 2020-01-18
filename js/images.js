window.onload = init;

const protocol = document.location.protocol;
const hostname = document.location.hostname;
const webroot = protocol + '//' + hostname;

function init() {
    const imagesContainer = document.querySelector('#images-container');
    const searchForm = document.querySelector('#search-form');
    const searchFormInput = document.querySelector('#search-form-input');

    const images = GET['search'] ? search(GET['search']) : getAllImages();
    images.then(result => {
        addImages(result, imagesContainer);
    });

    searchForm.addEventListener('submit', event => {
        event.preventDefault();
        window.location.assign(webroot + '/booru/images.php?search=' + searchFormInput.value)
    })
}

async function search(searchString) {
    const response = await fetch(webroot + '/booru-api/search.php?search=' + searchString);
    return response.json();
}

async function getAllImages() {
    const response = await fetch(webroot + '/booru-api/search.php?all=true');
    return response.json();
}

function addImage(imagePath, imageID, container) {
    const node = document.createElement('img');
    const link = document.createElement('a');
    link.href = webroot + '/booru/editor.php?img_id=' + imageID;
    node.src = webroot + '/booru/img/' + imagePath;
    link.appendChild(node);
    container.appendChild(link);
}

function addImages(images, container) {
    if (images.length) {
        images.forEach(image => {
            addImage(image['img_path'], image['img_id'], container);
        });
    }
    else {
        let node = document.createElement('p');
        node.textContent = 'No images found';
        container.appendChild(node);
    }
}