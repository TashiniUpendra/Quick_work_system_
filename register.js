document.addEventListener("DOMContentLoaded", function () {

    const roleRadios = document.querySelectorAll('input[name="role"]');
    const workerFields = document.getElementById('workerFields');

    // Show/hide worker fields based on role
    function toggleWorkerFields() {
        const selected = document.querySelector('input[name="role"]:checked');
        if (selected && selected.value === 'WORKER') {
            workerFields.style.display = 'block';
        } else {
            workerFields.style.display = 'none';
        }
    }

    roleRadios.forEach(function (radio) {
        radio.addEventListener('change', toggleWorkerFields);
    });

    // Initial check
    toggleWorkerFields();

});

// Navigate to Step 2
function goToStep2() {
    const form = document.getElementById('registerForm');

    // Validate Step 1 fields
    const name = form.querySelector('[name="name"]').value.trim();
    const email = form.querySelector('[name="email"]').value.trim();
    const phone = form.querySelector('[name="phone"]').value.trim();
    const password = document.getElementById('password').value;
    const repassword = document.getElementById('repassword').value;

    if (name.length < 3) {
        alert("Name must be at least 3 characters.");
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return;
    }

    if (phone.length < 10) {
        alert("Please enter a valid phone number (at least 10 digits).");
        return;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    if (password !== repassword) {
        alert("Passwords do not match.");
        return;
    }

    // Switch to step 2
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');

    document.getElementById('stepInd1').classList.remove('active');
    document.getElementById('stepInd2').classList.add('active');
    document.getElementById('stepLine').classList.add('active');
}

// Navigate back to Step 1
function goToStep1() {
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1').classList.add('active');

    document.getElementById('stepInd2').classList.remove('active');
    document.getElementById('stepInd1').classList.add('active');
    document.getElementById('stepLine').classList.remove('active');
}
