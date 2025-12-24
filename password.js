// Jelszó erősség ellenőrzés függvény
function checkPasswordStrength() {
    const password = document.getElementById('registerPassword');
    if (!password) return;
    
    const passwordValue = password.value;
    const registerButton = document.getElementById('registerButton');
    
    // Ellenőrzések
    const hasLength = passwordValue.length >= 6;
    const hasUppercase = /[A-Z]/.test(passwordValue);
    const hasNumber = /[0-9]/.test(passwordValue);
    const hasSpecial = /[!@#$%^&*()\-_=+{};:,<.>]/.test(passwordValue);
    
    // Indikátorok frissítése
    updateIndicator('length', hasLength);
    updateIndicator('uppercase', hasUppercase);
    updateIndicator('number', hasNumber);
    updateIndicator('special', hasSpecial);
    
    // Gomb engedélyezése/letiltása
    const allValid = hasLength && hasUppercase && hasNumber && hasSpecial;
    if (registerButton) {
        registerButton.disabled = !allValid;
    }
    
    // Hiányzó mező stílusának frissítése
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

// Real-time jelszó ellenőrzés
document.addEventListener('DOMContentLoaded', function() {
    const registerPassword = document.getElementById('registerPassword');
    if (registerPassword) {
        registerPassword.addEventListener('input', checkPasswordStrength);
        checkPasswordStrength(); 
    }
});

// Űrlap váltás eseménykezelő
document.addEventListener('click', function(e) {
    if (e.target.matches('a[onclick*="showForm"]')) {
        e.preventDefault();
        const match = e.target.getAttribute('onclick').match(/showForm\('(.+?)'\)/);
        if (match) {
            showForm(match[1]);
        }
    }
});

// Általános űrlap váltás funkció
function showForm(formId) {
    // Elrejti az összes űrlapot
    document.querySelectorAll('.form-box').forEach(form => {
        form.classList.remove('active');
    });
    
    // Megjeleníti a kiválasztott űrlapot
    const targetForm = document.getElementById(formId);
    if (targetForm) {
        targetForm.classList.add('active');
        
        // Regisztrációs űrlap esetén reseteljük a jelszó ellenőrzést
        if (formId === 'register-form') {
            setTimeout(() => {
                checkPasswordStrength();
                const registerPassword = document.getElementById('registerPassword');
                if (registerPassword) registerPassword.focus();
            }, 100);
        }
        
        // Elfelejtett jelszó űrlap esetén fókusz az első mezőre
        if (formId === 'forgot-form') {
            setTimeout(() => {
                const firstInput = document.querySelector('#forgot-form input');
                if (firstInput) firstInput.focus();
            }, 100);
        }
        
        // Bejelentkezési űrlap esetén fókusz az email mezőre
        if (formId === 'login-form') {
            setTimeout(() => {
                const emailInput = document.querySelector('#login-form input[type="email"]');
                if (emailInput) emailInput.focus();
            }, 100);
        }
    }
}

// Hiányzó mezők automatikus fókuszálása
document.addEventListener('DOMContentLoaded', function() {
    // Az első hiányzó mező fókuszálása
    setTimeout(() => {
        const firstMissingField = document.querySelector('.missing-field');
        if (firstMissingField) {
            firstMissingField.focus();
        }
    }, 300);
});

// Input mezők automatikus hiányzó mező jelölésének eltávolítása
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('missing-field') && this.value.trim() !== '') {
                this.classList.remove('missing-field');
            }
        });
    });
});

// Password mutat/nem mutat gombok
document.addEventListener('DOMContentLoaded', function() {
    // Password gombok
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

//FORM validálása
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

//Error kiirása
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error');
    
    if (field && errorDiv) {
        field.classList.add('missing-field');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

//Error törlése a jóról
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error');
    
    if (field && errorDiv) {
        field.classList.remove('missing-field');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
    }
}