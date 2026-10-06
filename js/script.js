// Format currency
function formatCurrency(amount) {
    return amount.toLocaleString('en-KE');
}

// Display appropriate error
function showError(elementId, show) {
    const errorElement = document.getElementById(elementId);
    if (show) {
        errorElement.style.display = 'block';
    } else {
        errorElement.style.display = 'none';
    }
}

// Basic input validation on blur/input
function validateInput(element) {
    if (element.id === 'fullName') {
        showError('fullNameError', element.value.trim() === '');
    } else if (element.id === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        showError('emailError', !emailRegex.test(element.value));
    } else if (element.id === 'phone') {
        const phoneRegex = /^\d{10}$/;
        showError('phoneError', !phoneRegex.test(element.value));
    } else if (element.id === 'conferenceDays') {
        const days = parseInt(element.value);
        showError('daysError', isNaN(days) || days < 1 || days > 3);
    }
}

// Dynamic behavior: Show/hide workshop options
function showWorkshop() {
    const category = document.getElementById('participationCategory').value;
    const workshopSection = document.getElementById('workshopSection');
    
    if (category === 'Workshop Participant') {
        workshopSection.classList.remove('hidden');
    } else {
        workshopSection.classList.add('hidden');
        // Uncheck workshops when hidden
        document.querySelector('input[name="workshop1"]').checked = false;
        document.querySelector('input[name="workshop2"]').checked = false;
    }
    
    calculateFee();
    
    if (category === '') {
        showError('categoryError', true);
    } else {
        showError('categoryError', false);
    }
}

// Dynamic behavior: Toggle meals and calculate
function toggleMeals() {
    const cateringOptions = document.getElementsByName('catering');
    let cateringSelected = 'No';
    for (let opt of cateringOptions) {
        if (opt.checked) cateringSelected = opt.value;
    }
    
    const mealSection = document.getElementById('mealSection');
    if (cateringSelected === 'Yes') {
        mealSection.classList.remove('hidden');
    } else {
        mealSection.classList.add('hidden');
    }
    
    calculateFee();
}

// Calculate total fee dynamically
function calculateFee() {
    const registrationFee = 1000;
    
    const daysInput = document.getElementById('conferenceDays').value;
    let days = parseInt(daysInput);
    if (isNaN(days) || days < 1 || days > 3) days = 0;
    
    const attendanceFee = days * 500;
    
    let workshopFee = 0;
    const category = document.getElementById('participationCategory').value;
    
    if (category === 'Workshop Participant') {
        const ws1 = document.querySelector('input[name="workshop1"]').checked;
        const ws2 = document.querySelector('input[name="workshop2"]').checked;
        
        if (ws1) workshopFee += 750;
        if (ws2) workshopFee += 1000;
    }
    
    let mealFee = 0;
    const cateringOptions = document.getElementsByName('catering');
    let cateringSelected = 'No';
    for (let opt of cateringOptions) {
        if (opt.checked) cateringSelected = opt.value;
    }
    
    if (cateringSelected === 'Yes') {
        mealFee = days * 300;
    }
    
    const totalFee = registrationFee + attendanceFee + workshopFee + mealFee;
    
    // Update display
    document.getElementById('regFeeDisplay').textContent = formatCurrency(registrationFee);
    document.getElementById('attendanceFeeDisplay').textContent = formatCurrency(attendanceFee);
    document.getElementById('workshopFeeDisplay').textContent = formatCurrency(workshopFee);
    document.getElementById('mealFeeDisplay').textContent = formatCurrency(mealFee);
    document.getElementById('totalFeeDisplay').textContent = formatCurrency(totalFee);
    
    // Hidden field to send to server
    document.getElementById('calculatedTotal').value = totalFee;
    
    // Package Message
    let packageMessage = "Conference Package";
    if (category === "Workshop Participant") packageMessage = "Workshop Package";
    else if (category === "Conference Only") packageMessage = "Standard Conference Package";
    
    if (cateringSelected === 'Yes') packageMessage += " with Catering";
    
    document.getElementById('packageMessage').textContent = "Selected: " + packageMessage;
}

// Form Validation on Submit
function validateForm(event) {
    let isValid = true;
    
    const fullName = document.getElementById('fullName');
    if (fullName.value.trim() === '') {
        showError('fullNameError', true);
        isValid = false;
    }
    
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
        showError('emailError', true);
        isValid = false;
    }
    
    const phone = document.getElementById('phone');
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(phone.value)) {
        showError('phoneError', true);
        isValid = false;
    }
    
    const days = parseInt(document.getElementById('conferenceDays').value);
    if (isNaN(days) || days < 1 || days > 3) {
        showError('daysError', true);
        isValid = false;
    }
    
    const category = document.getElementById('participationCategory').value;
    if (category === '') {
        showError('categoryError', true);
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault(); // Stop form submission
    }
    
    return isValid;
}

// Initial calculation on load
window.onload = function() {
    calculateFee();
};
