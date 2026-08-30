document.addEventListener('DOMContentLoaded', function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  function handleNotificationClick(link) {
    const notificationItem = link.closest('[data-notification-id]');
    if (!notificationItem) return;

    const notificationId = notificationItem.dataset.notificationId;
    const redirectUrl = link.dataset.url || link.getAttribute('href');

    fetch(`/notifications/${notificationId}/mark-as-read`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          notificationItem.remove();
          updateBadgeCount(-1);
          if (redirectUrl && redirectUrl !== '#') {
            window.location.href = redirectUrl;
          }
        }
      })
      .catch(() => {
        if (redirectUrl && redirectUrl !== '#') {
          window.location.href = redirectUrl;
        }
      });
  }

  function updateBadgeCount(delta) {
    const badgeDot = document.querySelector('.badge-notifications');
    const legacyBadge = document.querySelector('.dgtcp-notification-badge');

    if (legacyBadge) {
      const newCount = Math.max(0, parseInt(legacyBadge.textContent || '0', 10) + delta);
      legacyBadge.textContent = newCount;
      if (newCount === 0) legacyBadge.style.display = 'none';
    }

    if (badgeDot && delta < 0) {
      const items = document.querySelectorAll('.dropdown-notifications-item, .dgtcp-notification-item');
      if (items.length === 0) badgeDot.remove();
    }
  }

  document.querySelectorAll('.notification-link, .dgtcp-notification-link').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      handleNotificationClick(this);
    });
  });

  const markAllAsReadBtn = document.getElementById('markAllAsRead');
  if (markAllAsReadBtn) {
    markAllAsReadBtn.addEventListener('click', function(e) {
      e.preventDefault();

      fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.querySelectorAll('.dropdown-notifications-item, .dgtcp-notification-item').forEach(item => {
              item.remove();
            });

            document.querySelector('.badge-notifications')?.remove();
            const legacyBadge = document.querySelector('.dgtcp-notification-badge');
            if (legacyBadge) legacyBadge.style.display = 'none';

            const list = document.querySelector('.dropdown-notifications-list ul, .dgtcp-notification-list');
            if (list) {
              list.innerHTML = '<li class="list-group-item text-center py-4 text-body-secondary">Toutes les notifications ont été marquées comme lues</li>';
            }
          }
        })
        .catch(error => console.error('Erreur notifications:', error));
    });
  }
});
