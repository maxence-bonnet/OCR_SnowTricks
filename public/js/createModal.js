const modalTemplate = document.querySelector('#modalTemplate');

const modalText = 'Attention, vous êtes sur le point de supprimer définitivement';

const loadModalButtons = () => {
    let modalButtons = document.querySelectorAll('.btn-modal-delete');
    for (button of modalButtons) {
        initializeModal(button);
    }
}

const initializeModal = (button) => {
    button.addEventListener('click', function() {
        if ("object" !== typeof(this.modal)) {
            let modalSkeleton = createModalSkeleton(this);
            let collectionItem = button.closest('.collectionItem');
            bindAjaxDelete(modalSkeleton.querySelector('#confirmButton'), button.dataset.href, button.dataset.token, collectionItem);
            document.body.append(modalSkeleton);
            this.modal = new bootstrap.Modal(document.querySelector('#' + button.dataset.target));
        }
        this.modal.show();
    })
}

const createModalSkeleton = (button) => {
    let clone = document.importNode(modalTemplate.content, true);

    clone.querySelector('.modal').setAttribute('id', button.dataset.target);

    let modalBody = clone.querySelector('.modal-body');

    if (button.classList.contains('delete-picture')) {
        modalBody.textContent = modalText + ' une image !';
    } else if (button.classList.contains('delete-video')) {
        modalBody.textContent = modalText + ' une video !';
    } else if (button.classList.contains('delete-video')) {

    }
    
    return clone;
}

const bindAjaxDelete = (button, href, token, targetElement = null) => {
    button.addEventListener('click', async function() {
        let result = await ajaxDelete(href, token);
        if (result === 1 && null !== targetElement) {
            targetElement.remove();
        }
    })
}

async function ajaxDelete (href, token, contenType = 'application/json') {
    return new Promise(function(resolve, reject){
        fetch(href, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-type': contenType
            },
            body: JSON.stringify({'_token': token})
        })
        .then((response) => {
                return response.json()
        })
        .then((json) => {
            if (json.success) {
                resolve(json.success);
            } else if (json.error) {
                reject(json.error);
            }
        })
        .catch((error) => {
            reject(error);
        })        
    })
}
