document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.read-more-btn');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const hidden = this.previousElementSibling; 

            hidden.classList.toggle('active');

            this.textContent = hidden.classList.contains('active')
                ? 'Show Less'
                : 'Read More';
        });
    });
});

// === AGE CALCULATION ===
function calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

const birthDate = new Date(2006, 0, 1);
const age = calculateAge(birthDate);

// age IDs MUST exist
document.getElementById("age").textContent = age;
document.getElementById("age2").textContent = age;
document.getElementById("age3").textContent = age;
