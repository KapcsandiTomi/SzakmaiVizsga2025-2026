function checkPasswordStrength() {
    const password = document.getElementById('registerPassword');
    if (!password) return;
    
    const passwordValue = password.value;
    const registerButton = document.getElementById('registerButton');
    
    const hasLength = passwordValue.length >= 6;
    const hasUppercase = /[A-Z]/.test(passwordValue);
    const hasNumber = /[0-9]/.test(passwordValue);
    const hasSpecial = /[!@#$%^&*()\-_=+{};:,<.>]/.test(passwordValue);
    
    updateIndicator('length', hasLength);
    updateIndicator('uppercase', hasUppercase);
    updateIndicator('number', hasNumber);
    updateIndicator('special', hasSpecial);
    
    const allValid = hasLength && hasUppercase && hasNumber && hasSpecial;
    if (registerButton) {
        registerButton.disabled = !allValid;
    }
    
    if (passwordValue === '') {
        password.classList.add('missing-field');
    } else if (!allValid) {
        password.classList.add('missing-field');
    } else {
        password.classList.remove('missing-field');
    }
}

function updateIndicator(type, isValid) {
    const indicator = document.getElementById(`ind-${type}`);
    const requirement = document.getElementById(`req-${type}`);
    
    if (indicator && requirement) {
        if (isValid) {
            indicator.textContent = '✓';
            indicator.style.color = '#28a745';
            requirement.classList.add('valid');
        } else {
            indicator.textContent = '●';
            indicator.style.color = '#dc3545';
            requirement.classList.remove('valid');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const registerPassword = document.getElementById('registerPassword');
    if (registerPassword) {
        registerPassword.addEventListener('input', checkPasswordStrength);
        checkPasswordStrength(); 
    }
});

document.addEventListener('click', function(e) {
    if (e.target.matches('a[onclick*="showForm"]')) {
        e.preventDefault();
        const match = e.target.getAttribute('onclick').match(/showForm\('(.+?)'\)/);
        if (match) {
            showForm(match[1]);
        }
    }
});

function showForm(formId) {
    document.querySelectorAll('.form-box').forEach(form => {
        form.classList.remove('active');
    });
    
    const targetForm = document.getElementById(formId);
    if (targetForm) {
        targetForm.classList.add('active');
        
        if (formId === 'register-form') {
            setTimeout(() => {
                checkPasswordStrength();
                const registerPassword = document.getElementById('registerPassword');
                if (registerPassword) registerPassword.focus();
            }, 100);
        }
        
        if (formId === 'forgot-form') {
            setTimeout(() => {
                const firstInput = document.querySelector('#forgot-form input');
                if (firstInput) firstInput.focus();
            }, 100);
        }
        
        if (formId === 'login-form') {
            setTimeout(() => {
                const emailInput = document.querySelector('#login-form input[type="email"]');
                if (emailInput) emailInput.focus();
            }, 100);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const firstMissingField = document.querySelector('.missing-field');
        if (firstMissingField) {
            firstMissingField.focus();
        }
    }, 300);
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('missing-field') && this.value.trim() !== '') {
                this.classList.remove('missing-field');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.matches('.toggle-password') || e.target.closest('.toggle-password')) {
            const toggleBtn = e.target.matches('.toggle-password') ? e.target : e.target.closest('.toggle-password');
            const targetId = toggleBtn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input) {
                const icon = toggleBtn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            }
        }
    });
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePassword(password) {
    const hasLength = password.length >= 6;
    const hasUppercase = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*()\-_=+{};:,<.>]/.test(password);
    return hasLength && hasUppercase && hasNumber && hasSpecial;
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error');
    
    if (field && errorDiv) {
        field.classList.add('missing-field');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error');
    
    if (field && errorDiv) {
        field.classList.remove('missing-field');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
    }
}