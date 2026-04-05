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
                <textarea name="description" rows="6" placeholder="Describe your item in detail... Condition, history, features, etc."></textarea>
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
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Starting Price ($) *</label>
                    <input type="number" name="starting_price" step="0.01" required placeholder="0.00">
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
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-image"></i> Item Images</label>
                <div class="image-upload-area" id="imageUploadArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag & drop images here or click to browse</p>
                    <input type="file" name="images[]" multiple accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-secondary" onclick="document.querySelector('input[type=file]').click()">Select Images</button>
                </div>
                <div class="image-preview" id="imagePreview"></div>
                <small>Upload up to 5 images (JPG, PNG, GIF)</small>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Item Location</label>
                <input type="text" name="location" placeholder="City, State">
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
                <li>High-quality photos increase bids by 40%</li>
                <li>Detailed descriptions build buyer trust</li>
                <li>Lower starting prices attract more bidders</li>
                <li>Weekend endings get more last-minute bids</li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>