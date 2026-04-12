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
        
        <form action="index.php?action=create-auction-process" method="POST" enctype="multipart/form-data">
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
                    <label><i class="fas fa-dollar-sign"></i> Starting Price ($) *</label>
                    <input type="number" name="starting_price" step="0.01" min="1.00" required placeholder="0.00">
                    <small>Minimum $1.00</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-chart-line"></i> Bid Increment ($)</label>
                    <input type="number" name="bid_increment" step="0.01" value="1.00" placeholder="1.00">
                    <small>Minimum bid increase amount</small>
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
                    <p>Drag & drop image here or click to browse</p>
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('imageInput').click()">Select Image</button>
                </div>
                <div class="image-preview" id="imagePreview"></div>
                <small>Upload an image for your item (JPG, PNG, GIF - Max 5MB)</small>
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
                <li><i class="fas fa-calendar"></i> Weekend endings get more last-minute bids</li>
                <li><i class="fas fa-tag"></i> Choose the right category for better visibility</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Image upload preview
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const uploadArea = document.getElementById('imageUploadArea');

// Click to upload
uploadArea.addEventListener('click', () => {
    imageInput.click();
});

// Drag and drop
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--primary-gold)';
    uploadArea.style.background = 'rgba(255, 215, 0, 0.05)';
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--border-color)';
    uploadArea.style.background = 'transparent';
    
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        previewImage(file);
        imageInput.files = e.dataTransfer.files;
    } else {
        alert('Please drop an image file');
    }
});

// File input change
imageInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        previewImage(file);
    }
});

function previewImage(file) {
    // Check file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Image size must be less than 5MB');
        imageInput.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.innerHTML = `
            <div style="position: relative; display: inline-block;">
                <img src="${e.target.result}" alt="Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid var(--primary-gold);">
                <button type="button" onclick="removeImage()" style="position: absolute; top: -10px; right: -10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 14px;">×</button>
            </div>
        `;
        uploadArea.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    imagePreview.innerHTML = '';
    imageInput.value = '';
    uploadArea.style.display = 'flex';
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value.trim();
    const description = document.querySelector('textarea[name="description"]').value.trim();
    const category = document.querySelector('select[name="category"]').value;
    const startingPrice = parseFloat(document.querySelector('input[name="starting_price"]').value);
    
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
        alert('Starting price must be at least $1.00');
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

.image-preview {
    margin-top: 1rem;
    text-align: center;
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
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>