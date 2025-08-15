// Descriptions and icons for each option
const descriptions = {
    option1: {
        icon: '<i class="fa-solid fa-shield text-green-500"></i>',
        text: 'Admin Access',
        description: "Manage clinic operations and create client accounts."
    },
    option2: {
        icon: '<i class="fa-solid fa-stethoscope text-green-500"></i>',
        text: 'Veterinary Staff',
        description: "Access patient records and manage appointments."
    },
    option3: {
        icon: '<i class="fa-solid fa-paw text-green-500 rotate-45"></i>',
        text: 'Pet Owner',
        description: "View your pet\'s medical records and appointments"
    }
};

// Set default selected value
let selectedValue = "option3";
document.getElementById('custom-select-trigger').dataset.value = selectedValue;

document.getElementById('custom-select-trigger').addEventListener('click', function () {
    document.getElementById('custom-select-options').classList.toggle('hidden');
});

document.querySelectorAll('#custom-select-options li').forEach(item => {
    item.addEventListener('click', function () {
        // Get the icon HTML and the text
        const iconHTML = this.querySelector('i').outerHTML;
        const text = this.textContent.trim();

        // Set the trigger's HTML to icon + text
        document.getElementById('custom-select-trigger').innerHTML = iconHTML + '<span>' + text + '</span>';
        document.getElementById('custom-select-options').classList.add('hidden');
        // Set selected value
        selectedValue = this.dataset.value;
        document.getElementById('custom-select-trigger').dataset.value = selectedValue;
        // Update description icon and text
        document.getElementById('desc-icon').outerHTML = descriptions[selectedValue].icon.replace('">', '" id="desc-icon">');
        document.getElementById('desc-text').textContent = descriptions[selectedValue].text;
        document.getElementById('desc-description').textContent = descriptions[selectedValue].description;
    });
});

// On page load, set the default description icon and text
document.getElementById('desc-icon').outerHTML = descriptions[selectedValue].icon.replace('">', '" id="desc-icon">');
document.getElementById('desc-text').textContent = descriptions[selectedValue].text;
document.getElementById('desc-description').textContent = descriptions[selectedValue].description;
