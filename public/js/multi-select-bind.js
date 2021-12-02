/*
 * Additional JS to run IconicMultiSelect v0.7.0 
*/
const bindMySelect = (select, listSelector) => {
    var list = document.querySelector(listSelector);
    if (null !== list) {
        var optionsList = list.querySelectorAll('li')
        var mySelect = document.querySelector(select);

        for (let option of optionsList) {
            let optionTarget = mySelect.querySelector(`option[value="${option.dataset.value}"]`);
            let optionObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.target.classList.contains('multiselect__options--selected')) {
                        selectOption(optionTarget);
                    } else {
                        unselectOption(optionTarget);
                    }
                });
            });
        
            optionObserver.observe(option, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    } else {
        throw new Error(`multiSelect not found`);
    }
}

const selectOption = (option) => {
    if (option.getAttribute('selected') !== 'selected') {
        option.setAttribute('selected', 'selected');
    }
}

const unselectOption = (option) => {
    if (option.getAttribute('selected') === 'selected') {
        option.removeAttribute('selected', 'selected');
    }
}