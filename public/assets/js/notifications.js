document.addEventListener('DOMContentLoaded', function() {
    // Marquer une notification comme lue et rediriger
    document.querySelectorAll('.dgtcp-notification-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationItem = this.closest('.dgtcp-notification-item');
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
                    const countElement = document.querySelector('.dgtcp-notification-badge');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent);
                        const newCount = Math.max(0, currentCount - 1);
                        countElement.textContent = newCount;

                        // Cacher le badge si plus de notifications
                        if (newCount === 0) {
                            countElement.style.display = 'none';
                        }
                    }

                    // Rediriger vers la page concernée
                    if (redirectUrl && redirectUrl !== '#') {
                        window.location.href = redirectUrl;
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors du marquage de la notification:', error);
                // Rediriger quand même si l'API échoue
                if (redirectUrl && redirectUrl !== '#') {
                    window.location.href = redirectUrl;
                }
            });
        });
    });

    // Marquer toutes les notifications comme lues
    const markAllAsReadBtn = document.getElementById('markAllAsRead');
    if (markAllAsReadBtn) {
        markAllAsReadBtn.addEventListener('click', function(e) {
            e.preventDefault();

            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer toutes les notifications de la liste
                    document.querySelectorAll('.dgtcp-notification-item').forEach(item => {
                        item.remove();
                    });

                    // Cacher le badge de notification
                    const countElement = document.querySelector('.dgtcp-notification-badge');
                    if (countElement) {
                        countElement.style.display = 'none';
                    }

                    // Afficher un message de confirmation
                    const notificationList = document.querySelector('.dgtcp-notification-list');
                    if (notificationList) {
                        notificationList.innerHTML = `
                            <li class="dgtcp-notification-item">
                                <div class="dgtcp-empty-state">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <p>Toutes les notifications ont été marquées comme lues</p>
                                </div>
                            </li>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors du marquage de toutes les notifications:', error);
            });
        });
    }
});
