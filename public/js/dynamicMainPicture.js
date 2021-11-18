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

window.addEventListener('load', loadPicturesRadios());
