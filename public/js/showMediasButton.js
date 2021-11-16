var defaultStepTimer = 1000;

const loadShowButtons = () => {
    let showMediasButton = document.querySelector('#showMedias');
    let mediasList = document.querySelectorAll('.collectionItem');

    if (mediasList.length > 0) {
        showMediasButton.addEventListener('click', async function () {
            updateButtonText(showMediasButton, await toggleMedias(mediasList));
        });
    }
}

window.addEventListener('load', loadShowButtons);

const updateButtonText = (button, mediasStatus) => {
    if (mediasStatus === 'hidden') {
        button.textContent = 'Afficher les médias';
    } else if (mediasStatus === 'shown') {
        button.textContent = 'Masquer les médias';
    }
}

async function toggleMedias (mediasList) {
    if (!mediasList[0].classList.contains('d-none')) {
        mediasList = Array.from(mediasList).reverse();
    }
    for (media of mediasList) {
        mediasStatus = await toggleElement(media, 200);
        await timer(200); // matching with opactity transition end
    }
    return mediasStatus;
}

async function toggleElement (element = null, stepTimer = defaultStepTimer) {
    if (null !== element) {
        if (element.classList.contains('d-none')) {
            // smooth show
            element.classList.toggle('d-none');
            await timer(stepTimer);
            element.classList.toggle('smooth-show');
            return 'shown';          
        } else {
            // smooth hide
            element.classList.toggle('smooth-show');
            await timer(stepTimer);
            element.classList.toggle('d-none');
            return 'hidden';
        }
    }
}

async function timer(ms = 1000) {
    return new Promise(resolve => setTimeout(resolve, ms))
}