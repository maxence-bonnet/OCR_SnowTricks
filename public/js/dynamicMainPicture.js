function loadPicturesRadios() {
    let mainPictureRadios = document.querySelectorAll('.mainPicture-radio');
    let mainPictureImage = document.querySelector('#mainPicture');

    for (let radio of mainPictureRadios) {
        bindRadioToMainPicture(radio, mainPictureImage);
    }
}

const updateMainPicture = (source, target) => {
    target.src = source;
}

const bindRadioToMainPicture = (radio, mainPictureImage) => {
    let radioRelatedPicture = radio.closest('.collectionItem').querySelector('img');
    radio.addEventListener('change', function() {
        if (this.checked) {
            updateMainPicture(radioRelatedPicture.src, mainPictureImage);
            updateMainPictureSubform(this);
        }
    })

    let pictureSrcObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (radio.checked) {
                updateMainPicture(mutation.target.src, mainPictureImage)
            }
        });
    });

    pictureSrcObserver.observe(radioRelatedPicture, {
        attributes: true,
        attributeFilter: ['src']
    });
}

const updateMainPictureSubform = (radio) => {
    document.querySelector('.main-picture-subform').classList.remove('main-picture-subform');
    radio.closest('.subform').classList.add('main-picture-subform');
}

window.addEventListener('load', loadPicturesRadios());
