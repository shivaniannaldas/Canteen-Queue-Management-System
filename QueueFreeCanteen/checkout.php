<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $years = mysqli_real_escape_string($conn, $_POST['years']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products = array();

    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if(mysqli_num_rows($cart_query) > 0){
        while($cart_item = mysqli_fetch_assoc($cart_query)){
            $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND years = '$years' AND branch = '$branch' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

    if($cart_total == 0){
        $message[] = 'Your cart is empty!';
    } elseif(mysqli_num_rows($order_query) > 0){
        $message[] = 'Order placed already!';
    } else {
        mysqli_query($conn, "INSERT INTO `orders` (user_id, name, number, email, method, years, branch, total_products, total_price, placed_on) VALUES ('$user_id', '$name', '$number', '$email', '$method', '$years', '$branch', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
        mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        $message[] = 'Order placed successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php @include 'header.php'; ?>

<section class="heading">
    <h3>Checkout Order</h3>
    <p> <a href="home.php">Home</a> / Checkout </p>
</section>

<section class="display-order">
    <?php
        $grand_total = 0;
        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
    ?>    
    <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo 'Rs.'.$fetch_cart['price'].'/-'.' x '.$fetch_cart['quantity']; ?>)</span> </p>
    <?php
        }
        } else {
            echo '<p class="empty">Your cart is empty</p>';
        }
    ?>
    <div class="grand-total">Grand Total : <span>Rs.<?php echo $grand_total; ?>/-</span></div>
</section>

<section class="checkout">

    <form action="" method="POST">

        <h3>Place Your Order</h3>

        <div class="flex">
            <div class="inputBox">
                <span>Name :</span>
                <input type="text" name="name" placeholder="Enter your name" required>
            </div>
            <div class="inputBox">
                <span>Roll Number :</span>
                <input type="text" name="number" placeholder="Enter your number" required>
            </div>
            <div class="inputBox">
                <span>Email :</span>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="inputBox">
                <span>Payment Method :</span>
                <select name="method" required>
                    <option value="debit card">Debit card</option>
                    <option value="credit card">Credit card</option>
                    <option value="paypal">Paypal</option>
                    <option value="paytm">Paytm</option>
                    <option value="google pay">Google Pay</option>
                </select>
            </div>
            <div class="inputBox">
                <span>Year :</span>
                <input type="number" name="years" placeholder="Enter your year" required>
            </div>
            <div class="inputBox">
                <span>Branch :</span>
                <input type="text" name="branch" placeholder="Enter your branch" required>
            </div>
        </div>

        <input type="submit" name="order" value="Order Now" class="btn">

    </form>

</section>

<?php @include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
