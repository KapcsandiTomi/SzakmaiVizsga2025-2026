function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => {
        form.classList.remove("active");
    });
    const targetForm = document.getElementById(formId);
    if (targetForm) {
        targetForm.classList.add("active");
    } else {
        console.warn(`Form with ID "${formId}" not found.`);
    }
}

$(window).scroll(function () {
          if ($(this).scrollTop() > 250) {
              $('.sticky-top').addClass('sticky-nav').css('top', '0px');
          } else {
              $('.sticky-top').removeClass('sticky-nav').css('top', '-100px');
          }
        });



