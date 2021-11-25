const modalDeleteTemplate = document.querySelector('#modalDeleteTemplate');

const modalText = 'Attention, vous êtes sur le point de supprimer définitivement ';

// requires several button attributes : 
// data-form="{formId}" for trick deletion OR data-element-remove="{{elementId}} for collection item remove
// data-modal="{modalId}"
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
    if (button.dataset.elementRemove) {
        document.querySelector('#' + button.dataset.elementRemove).remove();
    } else if (button.dataset.form) {
        document.querySelector('#' + button.dataset.form).submit(); 
    }
}

window.addEventListener('load', () => {
    if (null !== modalDeleteTemplate) {
        let modalButtons = document.querySelectorAll('.btn-modal-delete');
        for (button of modalButtons) {
            initializeDeleteModal(button);
        }       
    }
});
