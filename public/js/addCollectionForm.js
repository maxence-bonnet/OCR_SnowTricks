const loadAddSubformButtons = () => {
    let addFormButtons = document.querySelectorAll('.addSubformButton');
    for (button of addFormButtons) {
        initialize(button);
    }
}

const loadEditItemPreview = () => {
    let collectionHolders = document.querySelectorAll('.collectionHolder');
    for (collectionHolder of collectionHolders) {
        let itemsCollection = collectionHolder.querySelectorAll('.collectionItem');
        index = 0;
        for (editForm of itemsCollection) {
            initializePreview(collectionHolder.parentNode.id, index, editForm);
            index++;
        }
    }
}

const initialize = (button) => {
    let subFormPrototype = getSubformPrototype(button.dataset.target);
    let collectionHolder = getCollectionHolder(button.dataset.target);
    collectionHolder.data = {index: getCollectionLength(collectionHolder)};
    button.addEventListener('click', () => {
        let newSubform = embedNewSubform(collectionHolder, subFormPrototype);
        initializePreview(button.dataset.target, collectionHolder.data.index, newSubform);
        initializeSubformRemove(newSubform);
        collectionHolder.data.index++;
    });
}

const getCollectionHolder = (target) => {
    return document.querySelector(`#${target} > .collectionHolder`);
}

const getCollectionLength = (collectionHolder) => {
    return collectionHolder.querySelectorAll('.collectionItem').length;
}

const getSubformPrototype = (target) => {
    return document.querySelector(`#${target}-form-prototype`).dataset.prototype;
}

const embedNewSubform = (collectionHolder, subFormPrototype) => {
    newForm = createNewSubform(collectionHolder.data.index, subFormPrototype)
    collectionHolder.appendChild(newForm);
    return newForm;
}

const createNewSubform = (index, subFormPrototype) => {
    let newForm = document.createElement('div')
    newForm.classList.add('collectionItem', 'subform', 'col', 'd-flex', 'flex-column');
    newForm.innerHTML = subFormPrototype.replace(/__name__/g, index);
    // esthetic
    newForm.querySelector('fieldset').classList.remove('mb-3');
    return newForm;
}

const initializePreview = (target, index, subform) => {
    if (target === 'trick-pictures') {
        initializePicturePreview(index, subform);
    } else if (target === 'trick-videos') {
        initializeVideoPreview(index, subform);
    }
}

const initializePicturePreview = (index, form) => {
    let img = form.querySelector('#trick_pictures_' + index + '_card > img');
    let input = form.querySelector('input[type="file"]');
    input.onchange = () => {
        const [file] = input.files;
        if (file) {
            img.src = URL.createObjectURL(file);
        }
    };
}

const initializeVideoPreview = (index, form) => {
    let iframe = form.querySelector('#trick_videos_' + index + '_card > iframe');
    let input = form.querySelector('input[type="text"]');
    input.addEventListener('change', (e) => {
        iframe.src = buildYoutubeURL(e.target.value);
        setTimeout(() => {
            input.value = buildYoutubeURL(e.target.value);
        }, 1000)
    });
}

const initializeSubformRemove = (subform) => {
    subform.querySelector('button').addEventListener('click', function() {
        this.closest('.collectionItem').remove();
    }) 
}

const buildYoutubeURL = (string) => {
    if (string.match(/https:\/\/www.youtube.com/)) {
        if (string.match(/https:\/\/www.youtube.com\/embed\//)) {
            return string;
        }
        if (string.match(/https:\/\/www.youtube.com\/watch\?v=/)) {
            return string.replace(/\/watch\?v=/, '\/embed\/')
        }
    }
    if (string.match(/https:\/\/youtu.be/)) {
        return string.replace(/youtu.be/,'youtube.com\/embed\/')
    }
    return '';
}
