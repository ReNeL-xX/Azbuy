/* ============================================
   AZBUY - MAIN JAVASCRIPT
   Premium Auction Platform
   ============================================ */

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Countdown Timers
function initCountdowns() {
    const countdowns = document.querySelectorAll('.countdown, .countdown-small, .timer');
    
    countdowns.forEach(el => {
        let timeLeft = parseInt(el.dataset.time);
        if (isNaN(timeLeft)) return;
        
        function update() {
            if (timeLeft <= 0) {
                el.innerHTML = 'Ended';
                return;
            }
            
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            if (el.classList.contains('countdown-small')) {
                el.innerHTML = `${hours}h ${minutes}m`;
            } else {
                el.innerHTML = `${hours.toString().padStart(2, '0')}h ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s`;
            }
            
            timeLeft--;
        }
        
        update();
        setInterval(update, 1000);
    });
}

// Tab switching
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
    
    document.querySelectorAll('.tabs .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Filter items
function filterItems(status) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (status === 'active') {
            row.style.display = row.classList.contains('active-row') ? '' : 'none';
        } else if (status === 'ended') {
            row.style.display = row.classList.contains('ended-row') ? '' : 'none';
        } else {
            row.style.display = '';
        }
    });
    
    document.querySelectorAll('.items-tabs .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    if (event && event.target) event.target.classList.add('active');
}

// Filter bids
function filterBids(status) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (status === 'active') {
            row.style.display = row.classList.contains('active-row') ? '' : 'none';
        } else if (status === 'winning') {
            row.style.display = row.classList.contains('winning-row') ? '' : 'none';
        } else if (status === 'lost') {
            row.style.display = row.classList.contains('lost-row') ? '' : 'none';
        } else if (status === 'won') {
            row.style.display = row.classList.contains('payment-row') ? '' : 'none';
        } else {
            row.style.display = '';
        }
    });
}

// Settings tabs
function initSettingsTabs() {
    const tabs = document.querySelectorAll('.settings-nav a');
    if (!tabs.length) return;
    
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            const tabId = tab.dataset.tab;
            
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            document.querySelectorAll('.settings-tab').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        });
    });
}

// Payment modal
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) modal.style.display = 'flex';
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) modal.style.display = 'none';
}

function processPayment() {
    showNotification('Payment processed successfully!', 'success');
    closePaymentModal();
    setTimeout(() => location.reload(), 1500);
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '300px';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// REMOVED: initImageUpload function - handled inline in create_auction.php
// This was causing the file manager to open twice

// Payment method selection
function initPaymentMethods() {
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    const cardDetails = document.querySelector('.card-details');
    
    paymentRadios?.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'card') {
                if (cardDetails) cardDetails.style.display = 'block';
            } else {
                if (cardDetails) cardDetails.style.display = 'none';
            }
        });
    });
}

// Thumbnail gallery
function initThumbnails() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image img');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            thumbnails.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            const imgSrc = thumb.querySelector('img').src;
            if (mainImage) mainImage.src = imgSrc;
        });
    });
}

// Search and filter functionality
function initSearchFilter() {
    const searchInput = document.querySelector('.search-box input');
    const categoryFilter = document.getElementById('category-filter');
    const sortFilter = document.getElementById('sort-filter');
    const auctionCards = document.querySelectorAll('.auction-card');
    
    function filterAuctions() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const category = categoryFilter?.value || '';
        
        auctionCards.forEach(card => {
            const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const cardCategory = card.querySelector('.category')?.textContent.toLowerCase() || '';
            
            let show = true;
            if (searchTerm && !title.includes(searchTerm)) show = false;
            if (category && !cardCategory.includes(category)) show = false;
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    if (searchInput) searchInput.addEventListener('input', filterAuctions);
    if (categoryFilter) categoryFilter.addEventListener('change', filterAuctions);
}


// Initialize everything on load
document.addEventListener('DOMContentLoaded', () => {
    initCountdowns();
    initSettingsTabs();
    // initImageUpload(); // REMOVED - causing double file manager popup
    initPaymentMethods();
    initThumbnails();
    initSearchFilter();
    
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.auction-card, .feature-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease ${index * 0.05}s backwards`;
    });
});

// Smooth scroll for anchor links (only if they exist)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Notification functions
function loadNotifications() {
    fetch('index.php?action=get-notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationUI(data.notifications, data.unread_count);
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function updateNotificationUI(notifications, unreadCount) {
    const countElement = document.getElementById('notificationCount');
    const listElement = document.getElementById('notificationList');
    
    // Always update the badge count
    if (countElement) {
        if (unreadCount > 0) {
            countElement.innerText = unreadCount;
            countElement.style.display = 'inline-block';
            // Add pulse animation
            countElement.style.animation = 'pulse 0.5s ease';
            setTimeout(() => {
                if (countElement) countElement.style.animation = '';
            }, 500);
        } else {
            countElement.style.display = 'none';
        }
    }
    
    if (listElement) {
        if (notifications.length === 0) {
            listElement.innerHTML = '<div style="padding: 12px; text-align: center; color: var(--text-muted);">No notifications</div>';
        } else {
            listElement.innerHTML = notifications.map(notif => `
                <div class="notification-item" data-id="${notif.id}" data-read="${notif.is_read}" style="padding: 12px; border-bottom: 1px solid var(--border-color); ${notif.is_read ? 'opacity: 0.7;' : 'background: rgba(255,215,0,0.05);'}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1; cursor: pointer;" onclick="markNotificationRead(${notif.id})">
                            <strong style="color: var(--primary-gold);">${escapeHtml(notif.title)}</strong>
                            <div style="font-size: 12px; margin-top: 4px;">${escapeHtml(notif.message)}</div>
                            <small style="color: var(--text-muted);">${formatDate(notif.created_at)}</small>
                        </div>
                        <button onclick="deleteNotification(${notif.id})" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 14px; margin-left: 10px; padding: 5px;" title="Delete notification">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
    }
}

function deleteNotification(notificationId) {
    if (confirm('Remove this notification?')) {
        fetch('index.php?action=delete-notification', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            } else {
                alert('Failed to delete notification');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteAllNotifications() {
    if (confirm('Delete all notifications? This cannot be undone.')) {
        fetch('index.php?action=delete-all-notifications', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            } else {
                alert('Failed to delete notifications');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function markNotificationRead(notificationId) {
    fetch('index.php?action=mark-notification-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload notifications to update the badge count
            loadNotifications();
        }
    });
}

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
        loadNotifications();
    } else {
        dropdown.style.display = 'none';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    return `${days} day${days > 1 ? 's' : ''} ago`;
}

// Load notifications on page load to show badge count
document.addEventListener('DOMContentLoaded', function() {
    // Load notifications to get the badge count
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const bell = document.querySelector('.notification-bell');
    const dropdown = document.getElementById('notificationDropdown');
    if (bell && dropdown && !bell.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});