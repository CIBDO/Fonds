document.addEventListener('DOMContentLoaded', function() {
    // Marquer une notification comme lue et rediriger
    document.querySelectorAll('.notification-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationItem = this.closest('.notification-message');
            const notificationId = notificationItem.dataset.notificationId;
            const redirectUrl = this.dataset.url;  // L'URL vers laquelle rediriger

            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer la notification de la liste
                    notificationItem.remove();
                    
                    // Mettre à jour le compteur
                    const countElement = document.querySelector('.notification-count');
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = Math.max(0, currentCount - 1);
                    
                    // Rediriger vers la page concernée
                    window.location.href = redirectUrl;
                }
            });
        });
    });
});
