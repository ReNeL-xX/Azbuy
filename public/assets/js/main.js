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

// Place bid function
function placeBid() {
    const amount = document.getElementById('bid_amount')?.value;
    if (!amount || amount <= 0) {
        showNotification('Please enter a valid bid amount', 'error');
        return;
    }
    showNotification(`Bid of $${amount} placed successfully!`, 'success');
    setTimeout(() => location.reload(), 1500);
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

// Image upload preview
function initImageUpload() {
    const uploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.querySelector('input[type="file"]');
    const preview = document.getElementById('imagePreview');
    
    if (!uploadArea) return;
    
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--primary-gold)';
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = 'var(--border-color)';
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--border-color)';
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            previewImages(files);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        previewImages(e.target.files);
    });
    
    function previewImages(files) {
        preview.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '12px';
                img.style.border = '1px solid var(--border-color)';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
}

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

// Auto-refresh bids on auction page
if (window.location.href.includes('view-auction')) {
    setInterval(() => {
        location.reload();
    }, 30000);
}

// Initialize everything on load
document.addEventListener('DOMContentLoaded', () => {
    initCountdowns();
    initSettingsTabs();
    initImageUpload();
    initPaymentMethods();
    initThumbnails();
    initSearchFilter();
    
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.auction-card, .feature-card, .stat-card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease ${index * 0.05}s backwards`;
    });
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});