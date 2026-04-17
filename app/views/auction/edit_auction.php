<?php 
$page_title = "Edit Auction";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

ob_start();
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h2>Edit Auction</h2>
            <p>Update your auction listing</p>
        </div>
        
        <form action="index.php?action=update-auction" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="auction_id" value="<?php echo $auction['id']; ?>">
            
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Auction Title *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($auction['title']); ?>" placeholder="e.g., Vintage Rolex Watch 1989">
                <small>Be specific and descriptive</small>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description *</label>
                <textarea name="description" rows="6" required placeholder="Describe your item in detail..."><?php echo htmlspecialchars($auction['description']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Electronics" <?php echo $auction['category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                        <option value="Collectibles" <?php echo $auction['category'] == 'Collectibles' ? 'selected' : ''; ?>>Collectibles</option>
                        <option value="Art" <?php echo $auction['category'] == 'Art' ? 'selected' : ''; ?>>Art</option>
                        <option value="Furniture" <?php echo $auction['category'] == 'Furniture' ? 'selected' : ''; ?>>Furniture</option>
                        <option value="Vehicles" <?php echo $auction['category'] == 'Vehicles' ? 'selected' : ''; ?>>Vehicles</option>
                        <option value="Fashion" <?php echo $auction['category'] == 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
                        <option value="Jewelry" <?php echo $auction['category'] == 'Jewelry' ? 'selected' : ''; ?>>Jewelry</option>
                        <option value="Sports" <?php echo $auction['category'] == 'Sports' ? 'selected' : ''; ?>>Sports Memorabilia</option>
                        <option value="Books" <?php echo $auction['category'] == 'Books' ? 'selected' : ''; ?>>Books & Magazines</option>
                        <option value="Music" <?php echo $auction['category'] == 'Music' ? 'selected' : ''; ?>>Music & Instruments</option>
                        <option value="Toys" <?php echo $auction['category'] == 'Toys' ? 'selected' : ''; ?>>Toys & Hobbies</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Extend Auction By</label>
                    <select name="duration_hours">
                        <option value="24">+24 hours</option>
                        <option value="48">+48 hours</option>
                        <option value="72">+72 hours</option>
                        <option value="168">+7 days</option>
                        <option value="336">+14 days</option>
                    </select>
                    <small>Extend the auction end time</small>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-image"></i> Item Image</label>
                <?php if ($auction['image_url']): ?>
                    <div class="current-image" style="margin-bottom: 1rem;">
                        <p>Current Image:</p>
                        <img src="/AzBuy/public/<?php echo $auction['image_url']; ?>" alt="Current image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                        <div class="form-checkbox" style="margin-top: 0.5rem;">
                            <input type="checkbox" name="keep_image" id="keep_image" checked>
                            <label for="keep_image">Keep current image</label>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="image-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Upload new image (leave empty to keep current)</p>
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('imageInput').click()">Select New Image</button>
                </div>
                <div class="image-preview" id="imagePreview"></div>
                <small>Upload a new image (JPG, PNG, GIF - Max 5MB)</small>
            </div>
            
            <div class="form-info" style="margin-top: 1rem;">
                <h4><i class="fas fa-info-circle"></i> Important Notes</h4>
                <ul>
                    <li><i class="fas fa-info-circle"></i> Only active auctions can be edited</li>
                    <li><i class="fas fa-chart-line"></i> Current price and bid history cannot be changed</li>
                    <li><i class="fas fa-clock"></i> Extending the auction time will give more time for bids</li>
                    <li><i class="fas fa-save"></i> Changes will take effect immediately</li>
                </ul>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php?action=my-auctions" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Image upload preview
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const uploadArea = document.getElementById('imageUploadArea');
const keepImageCheckbox = document.getElementById('keep_image');

// When new image is selected, uncheck keep image
if (imageInput) {
    imageInput.addEventListener('change', function() {
        if (this.files.length > 0 && keepImageCheckbox) {
            keepImageCheckbox.checked = false;
        }
        previewImage(this.files[0]);
    });
}

function previewImage(file) {
    if (!file) return;
    
    if (file.size > 5 * 1024 * 1024) {
        alert('Image size must be less than 5MB');
        imageInput.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.innerHTML = `
            <div style="position: relative; display: inline-block; margin-top: 1rem;">
                <img src="${e.target.result}" alt="Preview" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid var(--primary-gold);">
                <button type="button" onclick="removeNewImage()" style="position: absolute; top: -10px; right: -10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-size: 14px;">×</button>
            </div>
        `;
    };
    reader.readAsDataURL(file);
}

function removeNewImage() {
    imagePreview.innerHTML = '';
    imageInput.value = '';
    if (keepImageCheckbox) {
        keepImageCheckbox.checked = true;
    }
}
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

.current-image {
    background: var(--dark-card);
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
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