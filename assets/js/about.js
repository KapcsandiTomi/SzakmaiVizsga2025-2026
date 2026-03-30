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

function calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}


const ember1 = new Date(2006, 8, 10); 
const ember2 = new Date(2006, 4, 21); 
const ember3 = new Date(2006, 6, 28); 

const age1 = calculateAge(ember1);
const age2 = calculateAge(ember2);
const age3 = calculateAge(ember3);

document.getElementById("age1").textContent = age1;
document.getElementById("age2").textContent = age2;
document.getElementById("age3").textContent = age3;