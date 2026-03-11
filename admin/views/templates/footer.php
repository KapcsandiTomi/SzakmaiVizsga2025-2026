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


function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = 'index.php?page=users&action=delete&id=' + userId;
    }
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
});
</script>
</body>
</html>