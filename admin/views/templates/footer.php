<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

const statusColors = {
    "Not Processed": "#ff4d4d",
    "Processed": "#ffa500", 
    "Handed to Courier": "#1e90ff",
    "On the Way": "#ffff66",
    "Delivered": "#32cd32"
};


document.querySelectorAll('.status-select').forEach(select => {

    select.style.backgroundColor = statusColors[select.value] || '#ffffff';
    select.style.color = '#000000';
    

    select.addEventListener('change', function() {
        this.style.backgroundColor = statusColors[this.value] || '#ffffff';
        if (this.closest('td')) {
            this.closest('td').style.backgroundColor = statusColors[this.value] || '#ffffff';
        }
    });
});


function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete order #' + orderId + '? This action cannot be undone.')) {
        window.location.href = 'index.php?page=orders&action=delete&id=' + orderId;
    }
}


function ensureDeleteUserToast() {
    let toastEl = document.getElementById('deleteUserToast');
    if (toastEl) {
        return toastEl;
    }

    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1080';
    container.innerHTML = `
        <div id="deleteUserToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-user-times me-2"></i>
                <strong class="me-auto">Delete User</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <p class="mb-3">Are you sure you want to delete the user?</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="cancel">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" data-action="confirm">Delete</button>
                </div>
            </div>
        </div>`;

    document.body.appendChild(container);
    return document.getElementById('deleteUserToast');
}

function deleteUser(userId) {
    const toastEl = ensureDeleteUserToast();
    const toastInstance = bootstrap.Toast.getOrCreateInstance(toastEl);
    const confirmBtn = toastEl.querySelector('[data-action="confirm"]');
    const cancelBtn = toastEl.querySelector('[data-action="cancel"]');

    confirmBtn.onclick = function() {
        toastInstance.hide();
        window.location.href = 'index.php?page=users&action=delete&id=' + userId;
    };

    cancelBtn.onclick = function() {
        toastInstance.hide();
    };

    toastInstance.show();
}

setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 500);
    });
}, 5000);

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('error', function() {
            this.src = '../../letoles.jpg';
            this.alt = 'Default Product Image';
        });
    });

    const logoutLinks = document.querySelectorAll('a[href$="logout.php"]');
    if (!logoutLinks.length) {
        return;
    }

    const modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;align-items:center;justify-content:center;z-index:10000;padding:16px;';

    const card = document.createElement('div');
    card.style.cssText = 'width:min(420px,100%);background:#fff;border-radius:16px;padding:22px;box-shadow:0 24px 64px rgba(2,6,23,.3);text-align:center;border:1px solid #e2e8f0;';
    card.innerHTML = '<div style="font-size:40px;line-height:1;margin-bottom:10px;">🔒</div>' +
        '<h3 style="margin:0 0 8px 0;font-size:24px;color:#0f172a;">Exit</h3>' +
        '<p style="margin:0 0 18px 0;color:#475569;font-size:16px;">Are you sure you want to exit?</p>' +
        '<div style="display:flex;gap:10px;justify-content:center;">' +
        '<button type="button" id="adminLogoutCancelBtn" style="padding:10px 16px;border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:10px;font-weight:600;cursor:pointer;">Cancel</button>' +
        '<button type="button" id="adminLogoutConfirmBtn" style="padding:10px 16px;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:10px;font-weight:700;cursor:pointer;">Exit</button>' +
        '</div>';

    modal.appendChild(card);
    document.body.appendChild(modal);

    let pendingHref = null;
    const closeModal = function() {
        modal.style.display = 'none';
        pendingHref = null;
    };

    logoutLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            pendingHref = link.getAttribute('href');
            modal.style.display = 'flex';
        });
    });

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.getElementById('adminLogoutCancelBtn').addEventListener('click', closeModal);
    document.getElementById('adminLogoutConfirmBtn').addEventListener('click', function() {
        if (pendingHref) {
            window.location.href = pendingHref;
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const maintenanceToggle = document.getElementById('maintenanceToggle');
    if (!maintenanceToggle) return;
    
    maintenanceToggle.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        fetch('/Szakmai/handler/maintenance_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=toggle'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
                this.disabled = false;
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error toggling maintenance mode');
            this.disabled = false;
            location.reload();
        });
    });
});
</script>
</body>
</html>
