<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

// Handle item removal BEFORE any HTML output
if (isset($_GET['remove'])) {
    $id_to_remove = $_GET['remove'];
    $user_id = $_SESSION['user_id'] ?? 0;

    // NEW: Delete from Database
    if ($user_id > 0) {
        $del = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND course_id = ?");
        $del->bind_param("ii", $user_id, $id_to_remove);
        $del->execute();
    }

    if (isset($_SESSION['cart'][$id_to_remove])) {
        unset($_SESSION['cart'][$id_to_remove]);
    }
    header("Location: /cart.php");
    exit();
}

$page_title = 'Your Cart';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /learning-dashboard.php");
    exit();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Shopping Cart</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cart</li>
                </ol>
            </nav>
        </div>
    </div>
</section>

<section class="cart-section mt-5 mb-5">
    <div class="container">
        <?php if (empty($cart_items)): ?>
            <div class="text-center p-5" style="border: 2px dashed #e2e8f0; border-radius: 15px;">
                <h3>Your cart is empty</h3>
                <a href="courses.php" class="btn btn-primary mt-3">Explore Courses</a>
            </div>
        <?php else: ?>
            <div class="dashboard-wrapper" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                
                <div class="cart-table-container" style="border: 2px solid #10b981; border-radius: 15px; padding: 25px; overflow-x: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                <th style="padding: 15px;">Product</th>
                                <th style="padding: 15px;">Price</th>
                                <th style="padding: 15px;">Qty</th>
                                <th style="padding: 15px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $id => $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td data-label="Product" style="padding: 15px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <a href="cart.php?remove=<?php echo $id; ?>" style="color: #ef4444; font-size: 1.25rem; font-weight: bold; text-decoration: none; flex-shrink:0;">&times;</a>
                                        <span style="font-weight: 500; word-break: break-word;"><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td data-label="Price" style="padding: 15px;">₹<?php echo number_format($item['price'], 2); ?></td>
                                <td data-label="Qty" style="padding: 15px;"><?php echo $item['quantity']; ?></td>
                                <td data-label="Subtotal" style="padding: 15px; font-weight: 700;">₹<?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="text" placeholder="Coupon code" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; flex: 1; min-width: 140px;">
                        <button class="btn btn-outline" style="border: 1px solid #10b981; color: #10b981; white-space: nowrap;">Apply coupon</button>
                    </div>
                </div>

                <div class="cart-totals-container" style="border: 2px solid #10b981; border-radius: 15px; padding: 25px; height: fit-content;">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Cart Totals</h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span>Subtotal</span>
                        <strong>₹<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 15px; margin-bottom: 20px;">
                        <span>Total</span>
                        <strong style="font-size: 1.2rem; color: #10b981;">₹<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-block" style="background: #10b981; border: none; display: block; text-align: center;">Proceed to Checkout</a>
                </div>

            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>