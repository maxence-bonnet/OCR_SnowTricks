const minTricksShown = 10,
      maxToggleSteps = 2,
      defaultTimer = 200;

const loadShowButtons = () => {
    let showMoreButton = document.querySelector('#showMore');
    let showLessButton = document.querySelector('#showLess');
    let tricks = getTricks();
    let tricksNumber = tricks.length;

    showMoreButton.addEventListener('click', async function () {
        let hiddenTricks = getHiddenTricks();
        hiddenTricksNumber = await showMoreTricks(hiddenTricks);
        updateShowButtons(hiddenTricksNumber, tricksNumber, showMoreButton, showLessButton);
    });
    showLessButton.addEventListener('click', async function () {
        let hiddenTricks = getHiddenTricks();
        hiddenTricksNumber = await showLessTricks(tricks, hiddenTricks, tricksNumber);
        updateShowButtons(hiddenTricksNumber, tricksNumber, showMoreButton, showLessButton);
    });
}

const getTricks = () => {
    return document.querySelectorAll('.trick-card-container');
}

const getHiddenTricks = () => {
    return document.querySelectorAll('.trick-card-container.d-none');
}

async function showMoreTricks (hiddenTricks) {
    let hiddenTricksNumber = hiddenTricks.length;
    for (let i = 0; i < maxToggleSteps; i++) {
        if (hiddenTricksNumber <= 0) {
            break;
        }
        await timer();
        toggleElement(hiddenTricks[i]);
        hiddenTricksNumber--;
    }
    return hiddenTricksNumber;
}
 
async function showLessTricks (tricks, hiddenTricks, tricksNumber) {
    let hiddenTricksNumber = hiddenTricks.length;
    let shownTricksNumber = tricksNumber - hiddenTricksNumber;
    for (let i = 1; i <= maxToggleSteps; i++) {
        if (shownTricksNumber <= minTricksShown) {
            break;
        }
        await timer();
        toggleElement(tricks[shownTricksNumber - i])
        shownTricksNumber--;
        hiddenTricksNumber++;
    }
    return hiddenTricksNumber;
}

const updateShowButtons = (hiddenTricksNumber, tricksNumber, showMoreButton, showLessButton) => {
    // SI (il reste des tricks cachés et que le boutton + est caché)
    // OU SI(il n'y a plus de trick cachés et que le boutton + est affiché)
    // -> toggle le button +
    if ((hiddenTricksNumber > 0 && showMoreButton.classList.contains('d-none')) || (hiddenTricksNumber === 0 && !showMoreButton.classList.contains('d-none'))) {
        toggleElement(showMoreButton);
    }    
    // si (le nombre de tricks affichés est de {minTricksShown} ou moins, et que le boutton - est affiché)
    // OU si (il y a plus de {minTricksShown} tricks affichés et le boutton - est caché)
    // -> toggle le button -
    if ((tricksNumber - hiddenTricksNumber <= minTricksShown && !showLessButton.classList.contains('d-none')) || (tricksNumber - hiddenTricksNumber > minTricksShown && showLessButton.classList.contains('d-none'))) {
        toggleElement(showLessButton);
    }
}

const toggleElement = (element = null) => {
    if (null !== element) {
        element.classList.toggle('d-none');
    }
}

async function timer(ms = defaultTimer) {
    return new Promise(resolve => setTimeout(resolve, ms))
}
