
function loadAvatarForm() {
    let fileInput = document.querySelector('#user_avatar_file');
    let img = document.querySelector('.user-picture > img');
    let submitButton = document.querySelector('#submitChanges');
    let cancelButton = document.querySelector('#cancelChanges');
    bindReset(cancelButton, img, fileInput);
    initializePicturePreview(img, fileInput);
    let pictureSrcObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function() {
            if (fileInput.files.length) {
                showFormButton(submitButton);
                showFormButton(cancelButton);
            } else {
                hideFormButton(submitButton);
                hideFormButton(cancelButton);
            }
        });
    });

    pictureSrcObserver.observe(img, {
        attributes: true,
        attributeFilter: ['src']
    })
}

const initializePicturePreview = (img, input) => {
    input.onchange = () => {
        const [file] = input.files;
        if (file) {
            img.src = URL.createObjectURL(file);
        }
    };
}

const bindReset = (cancelButton, img, input) => {
    let originalSource = img.src;
    cancelButton.addEventListener('click', (e) => {   
        if (img.src !== originalSource) {
            img.src = originalSource;
        }
        input.value = '';
    });
}

const showFormButton = (button) => {
    button.classList.remove('d-none');
}

const hideFormButton = (button) => {
    button.classList.add('d-none');
}

window.addEventListener('load', loadAvatarForm);
