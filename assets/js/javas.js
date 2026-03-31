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

document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const modal = document.createElement('div');
    modal.id = 'logoutConfirmModal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;align-items:center;justify-content:center;z-index:10000;padding:16px;';

    const card = document.createElement('div');
    card.style.cssText = 'width:min(420px,100%);background:#fff;border-radius:16px;padding:22px;box-shadow:0 24px 64px rgba(2,6,23,.3);text-align:center;border:1px solid #e2e8f0;';
    card.innerHTML = '<div style="font-size:40px;line-height:1;margin-bottom:10px;">🔒</div>' +
        '<h3 style="margin:0 0 8px 0;font-size:24px;color:#0f172a;">Logout 🚪</h3>' +
        '<p style="margin:0 0 18px 0;color:#475569;font-size:16px;">Are you sure you want to log out? 👀</p>' +
        '<div style="display:flex;gap:10px;justify-content:center;">' +
        '<button type="button" id="logoutCancelBtn" style="padding:10px 16px;border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:10px;font-weight:600;cursor:pointer;">Stay 😊</button>' +
        '<button type="button" id="logoutConfirmBtn" style="padding:10px 16px;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:10px;font-weight:700;cursor:pointer;">Log out 🚪</button>' +
        '</div>';

    modal.appendChild(card);
    body.appendChild(modal);

    let pendingHref = null;
    const closeModal = function () {
        modal.style.display = 'none';
        pendingHref = null;
    };

    body.addEventListener('click', function(event) {
        const link = event.target.closest('a[href*="logout.php"]');
        if (link) {
            event.preventDefault();
            pendingHref = link.getAttribute('href');
            modal.style.display = 'flex';
        }
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.getElementById('logoutCancelBtn').addEventListener('click', closeModal);
    document.getElementById('logoutConfirmBtn').addEventListener('click', function () {
        if (pendingHref) {
            window.location.href = pendingHref;
        }
    });
});



