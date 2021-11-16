if ('undefined' === typeof(minTricksShown)) {
    var minTricksShown = 10;
}
if ('undefined' === typeof(maxToggleSteps)) {
    var maxToggleSteps = 5;
}
if ('undefined' === typeof(defaultStepTimer)) {
    var defaultStepTimer = 200; // (ms)
}

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
    updateShowButtons(getHiddenTricks().length, tricksNumber, showMoreButton, showLessButton);
}

window.addEventListener('load', loadShowButtons);

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
        await toggleElement(hiddenTricks[i]);
        hiddenTricksNumber--;
    }
    return hiddenTricksNumber;
}
 
async function showLessTricks (tricks, hiddenTricks, tricksNumber) {
    let hiddenTricksNumber = hiddenTricks.length;
    let shownTricksNumber = tricksNumber - hiddenTricksNumber;
    for (let i = 1; i <= maxToggleSteps; i++) {
        if ((tricksNumber - hiddenTricksNumber) <= minTricksShown) {
            break;
        }
        await toggleElement(tricks[shownTricksNumber - i]);
        hiddenTricksNumber++;
    }
    return hiddenTricksNumber;
}

const updateShowButtons = (hiddenTricksNumber, tricksNumber, showMoreButton, showLessButton) => {
    // if (there are still hidden tricks and [showMoreButton] is hidden)
    // or (there are no more hidden tricks and the [showMoreButton] is displayed)
    // then toggle [showMoreButton]
    updateButtonDisplay(
        showMoreButton, (
            (hiddenTricksNumber > 0 && showMoreButton.classList.contains('d-none'))
            ||
            (hiddenTricksNumber === 0 && !showMoreButton.classList.contains('d-none'))
        )
    );
    // if (number of tricks displayed equals or is less than {minTricksShown} and the [showLessButton] is displayed)
    // or (there are more than {minTricksShown} tricks displayed and the [showLessButton] is hidden)
    // then toggle [showLessButton]
    updateButtonDisplay(
        showLessButton, (
            (tricksNumber - hiddenTricksNumber <= minTricksShown && !showLessButton.classList.contains('d-none'))
            ||
            (tricksNumber - hiddenTricksNumber > minTricksShown && showLessButton.classList.contains('d-none'))
        )
    );
}

const updateButtonDisplay = (button, toggleCondidition) => {
    if (toggleCondidition) {
        toggleElement(button);
    }
}

async function toggleElement (element = null) {
    if (null !== element) {
        if (element.classList.contains('d-none')) {
            element.classList.toggle('d-none');
            await timer(defaultStepTimer);
            element.classList.toggle('smooth-show');
            return 'shown';          
        } else {
            element.classList.toggle('smooth-show');
            await timer(defaultStepTimer);
            element.classList.toggle('d-none');
            return 'hidden';
        }
    }
}

async function timer(ms = 500) {
    return new Promise(resolve => setTimeout(resolve, ms))
}
