const fileTempl = document.getElementById("file-template"),
    imageTempl = document.getElementById("image-template"),
    empty = document.getElementById("empty");

let FILES = {};

function addFile(target, file) {
    const isImage = file.type.match("image.*"),
        objectURL = URL.createObjectURL(file);

    const clone = isImage ?
        imageTempl.content.cloneNode(true) :
        fileTempl.content.cloneNode(true);

    clone.querySelector("h1").textContent = file.name;
    clone.querySelector("li").id = objectURL;
    clone.querySelector(".delete").dataset.target = objectURL;
    clone.querySelector(".size").textContent =
        file.size > 1024 ?
            file.size > 1048576 ?
                Math.round(file.size / 1048576) + "mb" :
                Math.round(file.size / 1024) + "kb" :
            file.size + "b";

    isImage &&
        Object.assign(clone.querySelector("img"), {
            src: objectURL,
            alt: file.name
        });

    empty.classList.add("hidden");
    target.prepend(clone);

    FILES[objectURL] = file;
}

const gallery = document.getElementById("gallery"),
    overlay = document.getElementById("overlay");

const hidden = document.getElementById("hidden-input");
document.getElementById("button").onclick = () => hidden.click();
hidden.onchange = (e) => {
    for (const file of e.target.files) {
        addFile(gallery, file);
    }
};

const hasFiles = ({
    dataTransfer: {
        types = []
    }
}) =>
    types.indexOf("Files") > -1;


let counter = 0;

function dropHandler(ev) {
    ev.preventDefault();
    for (const file of ev.dataTransfer.files) {
        addFile(gallery, file);
        overlay.classList.remove("draggedover");
        counter = 0;
    }
}

function dragEnterHandler(e) {
    e.preventDefault();
    if (!hasFiles(e)) {
        return;
    }
    ++counter && overlay.classList.add("draggedover");
}

function dragLeaveHandler(e) {
    1 > --counter && overlay.classList.remove("draggedover");
}

function dragOverHandler(e) {
    if (hasFiles(e)) {
        e.preventDefault();
    }
}

gallery.onclick = ({
    target
}) => {
    if (target.classList.contains("delete")) {
        const ou = target.dataset.target;
        document.getElementById(ou).remove(ou);
        gallery.children.length === 1 && empty.classList.remove("hidden");
        delete FILES[ou];
    }
};

document.getElementById("submit").onclick = async () => {
    const product_id = document.getElementById('product_id')?.value;
    const name = document.getElementById('name').value;
    const price = document.getElementById('price').value;

    if (!name || !price) {
        alert("Name and price are required.");
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);

    Object.values(FILES).forEach((file) => {
        formData.append('files[]', file);
    });

    let url = '/products';
    let method = 'POST';

    if (product_id) {
        url = `/products/${product_id}`;
        formData.append('_method', 'PUT');
    }

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (response.ok && result.success) {
            window.location.href = result.redirect;
        } else {
            alert(result.message || 'Upload failed.');
            console.error(result);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while uploading.');
    }
};

document.getElementById("cancel").onclick = () => {
    while (gallery.children.length > 0) {
        gallery.lastChild.remove();
    }
    FILES = {};
    empty.classList.remove("hidden");
    gallery.append(empty);
};

document.addEventListener('click', function (e) {
    if (e.target.closest('.delete-db-image')) {
        const button = e.target.closest('.delete-db-image');
        const imageId = button.dataset.id;
        if (!imageId) return;

        if (confirm('Are you sure you want to delete this image?')) {
            fetch(`/products/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Remove from DOM
                        const li = button.closest('li');
                        if (li) li.remove();
                    } else {
                        alert('Failed to delete the image.');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Error occurred while deleting.');
                });
        }
    }
});

