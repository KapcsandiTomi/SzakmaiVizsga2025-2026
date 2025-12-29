setTimeout(function() {
        const notice = document.getElementById('notice');
        if (notice) {
            notice.style.transition = 'opacity 0.5s';
            notice.style.opacity = '0';
            setTimeout(() => notice.remove(), 500);
        }
    }, 60000); 