<?php 
$page_title = "My Wallet";

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

// Get user data and transactions
require_once dirname(__DIR__) . '/../models/User.php';
require_once dirname(__DIR__) . '/../../config/Database.php';

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);
$balance = $userModel->getBalance($_SESSION['user_id']);
$transactions = $userModel->getTransactions($_SESSION['user_id'], 50);
$conn->close();

ob_start();
?>

<div class="wallet-container">
    <div class="wallet-header">
        <h1><i class="fas fa-wallet"></i> My Wallet</h1>
        <p>Manage your funds and track transactions</p>
    </div>
    
    <div class="wallet-balance-card">
        <div class="balance-icon">
            
        </div>
        <div class="balance-info">
            <span class="balance-label">Available Balance</span>
            <span class="balance-amount">₱<?php echo number_format($balance, 2); ?></span>
        </div>
    </div>
    
    <!-- Add Funds Section - Working Form -->
    <div class="add-funds-section">
        <h3><i class="fas fa-plus-circle"></i> Add Funds to Wallet</h3>
        <form action="index.php?action=add-funds" method="POST" class="add-funds-form">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-peso-sign"></i> Amount (₱)</label>
                    <input type="number" name="amount" step="0.01" min="1.00" required placeholder="Enter amount">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Funds
                    </button>
                </div>
            </div>
            <div class="info-note">
                <small><i class="fas fa-info-circle"></i> Demo: Funds will be added instantly to your wallet.</small>
            </div>
        </form>
    </div>
    
    <div class="transactions-section">
        <h2><i class="fas fa-history"></i> Transaction History</h2>
        
        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h3>No Transactions Yet</h3>
                <p>Your transaction history will appear here when you make sales or add funds.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="transaction-<?php echo $transaction['type']; ?>">
                                <td class="transaction-date">
                                    <?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?>
                                </td>
                                <td class="transaction-description">
                                    <?php echo htmlspecialchars($transaction['description']); ?>
                                </td>
                                <td class="transaction-type">
                                    <?php if ($transaction['type'] == 'credit'): ?>
                                        <span class="badge credit">+ Credit</span>
                                    <?php else: ?>
                                        <span class="badge debit">- Debit</span>
                                    <?php endif; ?>
                                </td>
                                <td class="transaction-amount <?php echo $transaction['type']; ?>">
                                    <?php if ($transaction['type'] == 'credit'): ?>
                                        +₱<?php echo number_format($transaction['amount'], 2); ?>
                                    <?php else: ?>
                                        -₱<?php echo number_format($transaction['amount'], 2); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.wallet-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.wallet-header {
    margin-bottom: 2rem;
}

.wallet-header h1 {
    color: var(--primary-gold);
    margin-bottom: 0.5rem;
}

.wallet-header p {
    color: var(--text-secondary);
}

.wallet-balance-card {
    background: linear-gradient(135deg, #1A1A1A 0%, #111111 100%);
    border-radius: 24px;
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    border: 1px solid rgba(255, 215, 0, 0.2);
    margin-bottom: 2rem;
}

.balance-icon i {
    font-size: 3rem;
    color: var(--primary-gold);
}

.balance-info {
    flex: 1;
}

.balance-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.balance-amount {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-gold);
}

/* Add Funds Section */
.add-funds-section {
    background: var(--dark-elevated);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

.add-funds-section h3 {
    color: var(--primary-gold);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.add-funds-section .form-row {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
}

.add-funds-section .form-group {
    flex: 1;
    margin-bottom: 0;
}

.add-funds-section .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
    font-size: 0.85rem;
}

.add-funds-section .form-group label i {
    color: var(--primary-gold);
    margin-right: 6px;
}

.add-funds-section input {
    width: 100%;
    padding: 12px 16px;
    background: var(--dark-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--text-primary);
    font-size: 1rem;
}

.add-funds-section input:focus {
    outline: none;
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
}

.add-funds-section .btn-primary {
    padding: 12px 24px;
    white-space: nowrap;
}

.info-note {
    margin-top: 0.75rem;
}

.info-note small {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.info-note i {
    color: var(--primary-gold);
    margin-right: 4px;
}

.transactions-section h2 {
    color: var(--primary-gold);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.table-responsive {
    overflow-x: auto;
    background: var(--dark-elevated);
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.transactions-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.transactions-table th {
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-gold);
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}

.transactions-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
}

.transactions-table tr:hover {
    background: rgba(255, 215, 0, 0.05);
}

.transaction-date {
    white-space: nowrap;
    font-size: 0.85rem;
}

.transaction-description {
    max-width: 300px;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.badge.credit {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.badge.debit {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.transaction-amount {
    font-weight: 700;
    white-space: nowrap;
}

.transaction-amount.credit {
    color: #28a745;
}

.transaction-amount.debit {
    color: #dc3545;
}

.empty-state {
    text-align: center;
    padding: 4rem;
    background: var(--dark-elevated);
    border-radius: 20px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--primary-gold);
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .wallet-container {
        padding: 1rem;
    }
    
    .wallet-balance-card {
        flex-direction: column;
        text-align: center;
    }
    
    .balance-amount {
        font-size: 2rem;
    }
    
    .add-funds-section .form-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .add-funds-section .btn-primary {
        width: 100%;
        justify-content: center;
    }
    
    .transactions-table th,
    .transactions-table td {
        padding: 0.75rem;
        font-size: 12px;
    }
    
    .transaction-description {
        max-width: 150px;
    }
}

@media (max-width: 480px) {
    .balance-amount {
        font-size: 1.5rem;
    }
    
    .balance-icon i {
        font-size: 2rem;
    }
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__) . '/layouts/header.php';
echo $content;
require_once dirname(__DIR__) . '/layouts/footer.php';
?>