const modalDeleteTemplate = document.querySelector('#modalDeleteTemplate');

const modalText = 'Attention, vous êtes sur le point de supprimer définitivement ';

function loadModalButtons() {
    if (null !== modalDeleteTemplate) {
        let modalButtons = document.querySelectorAll('.btn-modal-delete');
        for (button of modalButtons) {
            initializeDeleteModal(button);
        }       
    }
}

// requires several button attributes : 
// data-action="{actionToPerform}"
// data-form="{modalId}" or data-href="{url}" depending on action
// data-modal="{modalId}"
// data-token="{csrfToken}"
const initializeDeleteModal = (button) => {
    button.addEventListener('click', async function() {
        if ("object" !== typeof(this.modal)) {
            let modalSkeleton = createModalSkeleton(this, modalDeleteTemplate);
            this.confirmButton = modalSkeleton.querySelector('#confirmButton')
            document.body.append(modalSkeleton);
            this.modal = new bootstrap.Modal(document.querySelector('#' + button.dataset.modal));
        }
        this.modal.show();
        if ('confirmed' === await modalConfirm(this.confirmButton)) {
            executeButtonAction(button);
        }
    });
}

const createModalSkeleton = (button, template) => {
    let clone = document.importNode(template.content, true);
    clone.querySelector('.modal').setAttribute('id', button.dataset.modal);
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

async function modalConfirm(confirmButton) {
    return new Promise(function(resolve) {
        confirmButton.addEventListener('click', () => {
            resolve('confirmed');
        });
    });
}

const executeButtonAction = (button) => {
    let elementToRemove = null;
    if (button.dataset.elementRemove) {
        elementToRemove = document.querySelector('#' + button.dataset.elementRemove)
    }

    if (button.dataset.action === 'send-form') {
        sendForm(button.dataset.form);
    } else if (button.dataset.action === 'ajax-delete') {
        ajaxDelete(button.dataset.href, button.dataset.token, elementToRemove);
    }
}

const sendForm = (formId) => {
    let form = document.querySelector('#' + formId);
    if (null !== form) {
        form.submit();
    }
}

async function ajaxDelete(href, token, elementToRemove = null) {
    let result = await ajaxRequest(href, token, 'DELETE');
    if (result === 1 && null !== elementToRemove) {
        elementToRemove.remove();
    }
}

async function ajaxRequest(href, token, method, contenType = 'application/json') {
    return new Promise(function(resolve, reject){
        fetch(href, {
            method: method,
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

window.addEventListener('load', loadModalButtons);
