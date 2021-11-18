const modalImageTemplate = document.querySelector('#modalImageTemplate');

function loadModalImages() {
    if (null !== modalImageTemplate) {
        let images = document.querySelectorAll('.collectionItem img');
        for (img of images) {
            initializeImageModal(img);
        }       
    }
}

const initializeImageModal = (img) => {
    img.addEventListener('click', function() {
        if ("object" !== typeof(this.modal)) {
            let modalSkeleton = createImageModal(this, modalImageTemplate);
            document.body.append(modalSkeleton);
            this.modal = new bootstrap.Modal(document.querySelector('#' + img.dataset.modal));

            let pictureSrcObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function() {
                    updateModalImage(img)
                });
            });
        
            pictureSrcObserver.observe(img, {
                attributes: true,
                attributeFilter: ['src']
            })
        }
        this.modal.show();
    });
}

const createImageModal = (img, template) => {
    let clone = document.importNode(template.content, true);
    clone.querySelector('.modal').setAttribute('id', img.dataset.modal);
    clone.querySelector('.modal-img-text').textContent = img.alt;
    clone.querySelector('.modal-img').src = img.src;
    clone.querySelector('.modal-img').alt = img.alt;
    return clone;
}

const updateModalImage = (img) => {
    let modal = document.querySelector('#' + img.dataset.modal);
    if (null !== modal) {
        modal.querySelector('.modal-img').src = img.src;
    }
}

window.addEventListener('load', loadModalImages());
