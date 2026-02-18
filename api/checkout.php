<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'];
}

// Extract first and last name from session full_name if it exists
$full_name = $_SESSION['full_name'] ?? '';
$name_parts = explode(' ', $full_name);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

$page_title = 'Checkout';
require_once 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Checkout</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>
</section>
<style>
    :root {
        --brand-red: #ff4d4d;
        --brand-green: #10b981;
        --border-gray: #e2e8f0;
    }

    .checkout-wrapper {
        background-color: #fff;
        padding: 60px 0;
    }

    /* Force Grid Layout for Side-by-Side View */
    .checkout-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 40px;
        align-items: start;
    }

    /* FIX: Explicit Top Red Borders */
    .billing-form-container, 
    .order-review-container, 
    .payment-method-container,
    .coupon-container {
        border: 1px solid var(--border-gray) !important;
        border-top: 3px solid var(--brand-red) !important; /* This creates the visible top margin */
        background: #fff;
        border-radius: 12px;
        margin-bottom: 30px;
        padding: 35px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    h4 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 25px;
        font-size: 1.3rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 10px;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid var(--border-gray) !important;
        background-color: #f8fafc !important;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        height: auto;
    }

    /* Order Table Styling */
    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th { text-align: left; color: #94a3b8; font-size: 0.85rem; padding-bottom: 15px; border-bottom: 1px solid var(--border-gray); text-transform: uppercase; }
    .order-table td { padding: 20px 0; border-bottom: 1px solid var(--border-gray); color: #1e293b; }
    
    .total-text { font-size: 1.4rem; font-weight: 800; color: #1e293b; }

    #razorpay-button {
        background: var(--brand-green) !important;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 8px;
        padding: 18px;
        width: 100%;
        border: none;
        color: white;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    #razorpay-button:hover { background: #059669 !important; transform: translateY(-2px); }

    @media (max-width: 991px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>

<div class="checkout-wrapper">
    <div class="container">
        <div class="checkout-grid">
            
            <div class="billing-side">
                <div class="billing-form-container">
                    <h4>Billing Details</h4>
                    <form id="billingForm">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">First Name *</label>
                                <input type="text" id="billing_fname" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Last Name *</label>
                                <input type="text" id="billing_lname" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">Company Name (optional)</label>
                                <input type="text" id="billing_company" class="form-control">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">Country / Region *</label>
                                <select class="form-select"><option value="IN">India</option></select>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">Street address *</label>
                                <input type="text" id="billing_address_1" class="form-control mb-2" placeholder="House number and street name">
                                <input type="text" id="billing_address_2" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">Town / City *</label>
                                <input type="text" id="billing_city" class="form-control">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">State *</label>
                                <select id="billing_state" class="form-select">
                                    <option value="AP">Andhra Pradesh</option>
                                    <option value="TS">Telangana</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">PIN Code *</label>
                                <input type="text" id="billing_pincode" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Phone *</label>
                                <input type="text" id="billing_phone" class="form-control">
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label">Email Address *</label>
                                <input type="email" id="billing_email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="order-notes-container">
                    <label class="form-label">Order notes (optional)</label>
                    <textarea class="form-control" id="order_notes" rows="4" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                </div>
            </div>

            <div class="order-side">
                <div class="order-review-container">
                    <h4>Your Order</h4>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td style="color: #475569;"><?php echo htmlspecialchars($item['name']); ?> × 1</td>
                                <td style="text-align: right; font-weight: 700;">₹<?php echo number_format($item['price'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td style="padding-top: 20px;">Subtotal</td>
                                <td style="padding-top: 20px; text-align: right; font-weight: 700;">₹<?php echo number_format($total_amount, 2); ?></td>
                            </tr>
                            <tr style="border-top: 2px solid var(--border-color);">
                                <td style="font-size: 1.2rem; font-weight: 700; padding-top: 25px;">Total</td>
                                <td style="text-align: right; font-size: 1.2rem; font-weight: 700; padding-top: 25px;">₹<?php echo number_format($total_amount, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="coupon-container">
                    <p class="mb-0">Have a coupon? <a href="#" style="color: var(--brand-red);">Click here to enter your code</a></p>
                </div>

                <div class="payment-method-container">
                    <div class="razorpay-header mb-4">
                        <p class="fw-bold mb-2">Credit Card/Debit Card/NetBanking/EMI</p>
                        <div class="p-3 border rounded d-flex align-items-center bg-light">
                            <img src="https://razorpay.com/assets/razorpay-glyph.svg" width="20" class="me-2">
                            <span class="fw-bold small text-muted">Pay by Razorpay</span>
                        </div>
                    </div>
                    <p class="small text-muted mb-4">Pay securely using Credit/Debit card, NetBanking, or UPI through Razorpay.</p>
                    
                    <button type="button" id="razorpay-button" class="btn btn-success w-100 border-0 shadow-sm">
                        <i class="fas fa-lock me-2"></i> PLACE ORDER
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('razorpay-button').onclick = function(e){
    // Basic validation to ensure fields are filled before opening Razorpay
    if(!document.getElementById('billing_fname').value || !document.getElementById('billing_email').value) {
        alert("Please fill in the required billing details.");
        return;
    }

    var options = {
        "key": "rzp_test_1DP5mmOlF5G5ag", 
        "amount": "<?php echo ($total_amount * 100); ?>", 
        "currency": "INR",
        "name": "Adya3 Solutions",
        "description": "Course Purchase",
        "handler": function (response){
            window.location.href = "process-payment.php?payment_id=" + response.razorpay_payment_id;
        },
        "prefill": {
            "name": document.getElementById('billing_fname').value + " " + document.getElementById('billing_lname').value,
            "email": document.getElementById('billing_email').value,
            "contact": document.getElementById('billing_phone').value
        },
        "theme": { "color": "#10b981" } 
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>

<?php require_once 'includes/footer.php'; ?>