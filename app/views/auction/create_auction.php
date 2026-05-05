<?php 
$page_title = "Create Auction";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-plus-circle"></i>
            <h2>Create New Auction</h2>
            <p>List your item for the world to bid on</p>
        </div>
        
        <form action="index.php?action=create-auction-process" method="POST" enctype="multipart/form-data" id="createAuctionForm">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Auction Title *</label>
                <input type="text" name="title" required placeholder="e.g., Vintage Rolex Watch 1989">
                <small>Be specific and descriptive</small>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description *</label>
                <textarea name="description" rows="6" required placeholder="Describe your item in detail... Condition, history, features, etc."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Collectibles">Collectibles</option>
                        <option value="Art">Art</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Vehicles">Vehicles</option>
                        <option value="Fashion">Fashion</option>
                        <option value="Jewelry">Jewelry</option>
                        <option value="Sports">Sports Memorabilia</option>
                        <option value="Books">Books & Magazines</option>
                        <option value="Music">Music & Instruments</option>
                        <option value="Toys">Toys & Hobbies</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-peso-sign"></i> Starting Price (₱) *</label>
                    <input type="number" name="starting_price" step="0.01" min="1.00" required placeholder="0.00">
                    <small>Minimum ₱1.00</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-chart-line"></i> Bid Increment (₱)</label>
                    <input type="number" name="bid_increment" step="0.01" value="1.00" placeholder="1.00">
                    <small>Minimum bid increase amount (default: ₱1.00)</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Auction Duration *</label>
                    <select name="duration_hours" required>
                        <option value="24">24 hours</option>
                        <option value="48">48 hours</option>
                        <option value="72">72 hours</option>
                        <option value="168">7 days</option>
                        <option value="336">14 days</option>
                        <option value="720">30 days</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-image"></i> Item Image</label>
                <div class="image-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to browse or drag & drop image</p>
                    <small>Upload JPG, PNG, GIF - Max 5MB</small>
                </div>
                <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                <div class="image-preview" id="imagePreview"></div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Item Location (Optional)</label>
                <input type="text" name="location" placeholder="City, State, Country">
                <small>Where is the item located?</small>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-truck"></i> Shipping Options</label>
                <select name="shipping">
                    <option value="seller">Seller pays shipping</option>
                    <option value="buyer">Buyer pays shipping</option>
                    <option value="free">Free shipping</option>
                    <option value="local">Local pickup only</option>
                </select>
            </div>
            
            <div class="form-checkbox">
                <input type="checkbox" id="terms" required>
                <label for="terms">I confirm that this item is legal and I have the right to sell it</label>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Create Auction</button>
                <a href="index.php?action=dashboard" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        
        <div class="form-info">
            <h4><i class="fas fa-info-circle"></i> Auction Tips</h4>
            <ul>
                <li><i class="fas fa-camera"></i> High-quality photos increase bids by 40%</li>
                <li><i class="fas fa-file-alt"></i> Detailed descriptions build buyer trust</li>
                <li><i class="fas fa-chart-line"></i> Lower starting prices attract more bidders</li>
                <li><i class="fas fa-chart-line"></i> Bid increment determines how much each bid must increase by</li>
                <li><i class="fas fa-calendar"></i> Weekend endings get more last-minute bids</li>
                <li><i class="fas fa-tag"></i> Choose the right category for better visibility</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Get elements
const imageUploadArea = document.getElementById('imageUploadArea');
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const form = document.getElementById('createAuctionForm');

// Use a flag to prevent multiple triggers
let isOpening = false;

// Handle click on upload area - triggers file input ONLY ONCE
imageUploadArea.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Prevent multiple triggers
    if (isOpening) return;
    isOpening = true;
    
    // Trigger file input
    imageInput.click();
    
    // Reset flag after a short delay
    setTimeout(() => {
        isOpening = false;
    }, 500);
});

// Handle file selection
imageInput.addEventListener('change', function(e) {
    // Reset the flag
    isOpening = false;
    
    const file = this.files[0];
    if (file) {
        // Clear any existing preview first
        imagePreview.innerHTML = '';
        previewImage(file);
    }
});

// Drag and drop handlers
imageUploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = 'var(--primary-gold)';
    this.style.background = 'rgba(255, 215, 0, 0.05)';
});

imageUploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = 'var(--border-color)';
    this.style.background = 'transparent';
});

imageUploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.style.borderColor = 'var(--border-color)';
    this.style.background = 'transparent';
    
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        // Clear existing preview
        imagePreview.innerHTML = '';
        imageInput.files = e.dataTransfer.files;
        previewImage(file);
    } else {
        alert('Please drop an image file');
    }
});

// Preview image function
function previewImage(file) {
    // Check file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Image size must be less than 5MB');
        resetImageInput();
        return;
    }
    
    // Check file type
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        resetImageInput();
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        imagePreview.innerHTML = `
            <div style="position: relative; display: inline-block; margin-top: 1rem;">
                <img src="${e.target.result}" alt="Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid var(--primary-gold);">
                <button type="button" onclick="removeImage()" style="position: absolute; top: -10px; right: -10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;">×</button>
            </div>
        `;
        imageUploadArea.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

// Reset image input
function resetImageInput() {
    imageInput.value = '';
    imagePreview.innerHTML = '';
    imageUploadArea.style.display = 'flex';
}

// Remove image function
window.removeImage = function() {
    resetImageInput();
};

// Form validation
form.addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value.trim();
    const description = document.querySelector('textarea[name="description"]').value.trim();
    const category = document.querySelector('select[name="category"]').value;
    const startingPrice = parseFloat(document.querySelector('input[name="starting_price"]').value);
    const bidIncrement = parseFloat(document.querySelector('input[name="bid_increment"]').value);
    
    if (!title) {
        e.preventDefault();
        alert('Please enter an auction title');
        return false;
    }
    
    if (!description) {
        e.preventDefault();
        alert('Please enter a description');
        return false;
    }
    
    if (!category) {
        e.preventDefault();
        alert('Please select a category');
        return false;
    }
    
    if (isNaN(startingPrice) || startingPrice < 1) {
        e.preventDefault();
        alert('Starting price must be at least ₱1.00');
        return false;
    }
    
    if (isNaN(bidIncrement) || bidIncrement < 0.01) {
        e.preventDefault();
        alert('Bid increment must be at least ₱0.01');
        return false;
    }
    
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Please confirm that you have the right to sell this item');
        return false;
    }
    
    return true;
});
</script>

<style>
.form-container {
    display: flex;
    justify-content: center;
    padding: 2rem;
}

.form-card {
    background: var(--dark-elevated);
    border-radius: 24px;
    padding: 2rem;
    max-width: 800px;
    width: 100%;
    border: 1px solid var(--border-color);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-header i {
    font-size: 3rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.form-header h2 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
}

.form-group label i {
    color: var(--primary-gold);
    margin-right: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 14px;
    transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--text-muted);
    font-size: 11px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 1rem 0;
}

.form-checkbox input {
    width: 18px;
    height: 18px;
    accent-color: var(--primary-gold);
}

.button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.image-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    background: var(--dark-card);
}

.image-upload-area:hover {
    border-color: var(--primary-gold);
    background: rgba(255, 215, 0, 0.05);
}

.image-upload-area i {
    font-size: 3rem;
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.image-upload-area p {
    margin-bottom: 0.5rem;
}

.image-preview {
    margin-top: 1rem;
    text-align: center;
}

.form-info {
    margin-top: 2rem;
    padding: 1.2rem;
    background: var(--dark-card);
    border-radius: 16px;
    border: 1px solid var(--border-color);
}

.form-info h4 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.form-info ul {
    list-style: none;
    padding-left: 0;
}

.form-info ul li {
    padding: 5px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-info ul li i {
    color: var(--primary-gold);
    width: 25px;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, #FFD700 0%, #DAA520 100%);
    color: #000;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-gold);
    border: 1px solid var(--primary-gold);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    background: rgba(255, 215, 0, 0.1);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
    
    .form-card {
        padding: 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .button-group {
        flex-direction: column;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>