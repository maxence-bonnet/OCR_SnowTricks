const loadEditButtons = () => {
    let editButtons = document.querySelectorAll('.btn-edit');

    for (button of editButtons) {
        bindToForm(button);
    }
}

const bindToForm = (button) => {
    if (button.classList.contains('edit-picture')) {
        bindToPictureForm(button);
    } else if (button.classList.contains('edit-video')) {
        bindToVideoForm(button);
    }
}

const bindToPictureForm = (button) => {
    let alternateTextInput = document.querySelector('#' + button.dataset.id + '_alternateText');
    let fileInput = document.querySelector('#' + button.dataset.id + '_file');

    let originalSource = document.querySelector('#' + button.dataset.id + '_card > img').src;
    let originalAlternateText = alternateTextInput.value;
    let cancelButton = document.querySelector('#' + button.dataset.id + '_cancel');
    button.addEventListener('click', () => {   
        toggleDisplay();    
    })
    cancelButton.addEventListener('click', () => {
        toggleDisplay();
        resetEditPictureForm(originalSource, originalAlternateText, button);  
    })
    function toggleDisplay() {
        alternateTextInput.parentNode.classList.toggle('d-none');
        fileInput.parentNode.classList.toggle('d-none');
        cancelButton.classList.toggle('d-none');
    }
}

const bindToVideoForm = (button) => {
    let sourceInput = document.querySelector('#' + button.dataset.id + '_source');
    let originalSource = sourceInput.value;
    console.log(originalSource);
    let cancelButton = document.querySelector('#' + button.dataset.id + '_cancel');
    button.addEventListener('click', () => {   
        toggleDisplay();
    })
    cancelButton.addEventListener('click', () => {
        toggleDisplay();
        resetEditVideoForm(originalSource, button);
    })
    function toggleDisplay() {
        sourceInput.parentNode.classList.toggle('d-none');
        cancelButton.classList.toggle('d-none');
    }
}

const resetEditPictureForm = (originalSource, originalAlternateText, button) => {
    if (document.querySelector('#' + button.dataset.id + '_card > img').src !== originalSource) {
        document.querySelector('#' + button.dataset.id + '_card > img').src  = originalSource;
    }
    document.querySelector('#' + button.dataset.id + '_file').value = '';
    document.querySelector('#' + button.dataset.id + '_alternateText').value = originalAlternateText;
}

const resetEditVideoForm = (originalSource, button) => {
    if (document.querySelector('#' + button.dataset.id + '_card > iframe').src !== originalSource) {
        document.querySelector('#' + button.dataset.id + '_card > iframe').src = originalSource;
    }
    document.querySelector('#' + button.dataset.id + '_source').value = originalSource;
}
