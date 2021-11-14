const modalTemplate = document.querySelector('#modalTemplate');

const modalText = 'Attention, vous êtes sur le point de supprimer définitivement ';

const loadModalButtons = () => {
    if (null !== modalTemplate) {
        let modalButtons = document.querySelectorAll('.btn-modal-delete');
        for (button of modalButtons) {
            initializeModal(button);
        }       
    }
}

// requires several button attributs : 
// data-action="{actionToPerform}"
// data-form="{modalId}" or data-href="{url}" depending on action
// data-target="{modalId}"
// data-token="{csrfToken}"
const initializeModal = (button) => {
    button.addEventListener('click', function() {
        if ("object" !== typeof(this.modal)) {
            let modalSkeleton = createModalSkeleton(this);
            let modalConfirmButton = modalSkeleton.querySelector('#confirmButton')
            bindModalAction(modalConfirmButton, button);
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
        modalBody.textContent = modalText + 'une image !';
    } else if (button.classList.contains('delete-video')) {
        modalBody.textContent = modalText + 'une video !';
    } else if (button.classList.contains('delete-trick')) {
        modalBody.textContent = modalText + 'ce trick ! Vous serez redirigé vers l\'accueil après la suppression.';
    }
    return clone;
}

// elementToRemove (default null) is being removed if delete is successfull
const bindModalAction = (modalConfirmButton, button) => {
    let elementToRemove = null;
    if (button.dataset.elementRemove) {
        elementToRemove = document.querySelector('#' + button.dataset.elementRemove)
    }

    if (button.dataset.action === 'send-form') {
        bindSendForm(modalConfirmButton, button.dataset.form);
    } else if (button.dataset.action === 'ajax') {
        bindAjaxDelete(modalConfirmButton, button.dataset.href, button.dataset.token, elementToRemove);
    }
}

const bindSendForm = (button, formId) => {
    let form = document.querySelector('#' + formId);
    button.addEventListener('click', () => {
        if (null !== form) {
            form.submit();
        }
    })
}

const bindAjaxDelete = (button, href, token, elementToRemove = null) => {
    button.addEventListener('click', async function() {
        let result = await ajaxDelete(href, token);
        if (result === 1 && null !== elementToRemove) {
            elementToRemove.remove();
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
