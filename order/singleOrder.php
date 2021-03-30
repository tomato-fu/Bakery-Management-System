<?php 
    require 'orderHeader.php';

    $order_id = null;
    if (isset($_POST['order_id'])) $order_id = $_POST['order_id'];
    else if (isset($_GET['order_id'])) $order_id = $_GET['order_id'];
    
    $orderResult = null;
    $paymentResult = null;
    $detailsResult = null;

    // Fetch order information
    if ($orderStmt = $connect->prepare("CALL fetch_single_order(?)")){
        $orderStmt->bind_param('i', $order_id);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        $orderStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    // Fetch payment information
    if ($paymentStmt = $connect->prepare("CALL fetch_single_payment(?)")){
        $paymentStmt->bind_param('i', $order_id);
        $paymentStmt->execute();
        $paymentResult = $paymentStmt->get_result();
        $paymentStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    // Fetch products on order
    if ($detailsStmt = $connect->prepare("CALL fetch_single_order_details(?)")){
    $detailsStmt->bind_param('i', $order_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    $detailsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $payment = mysqli_fetch_assoc($paymentResult);
    $order = mysqli_fetch_assoc($orderResult);    
    
    ?>

<div class="ui segment container items">
  <div class="item">
    <div class="content"> 
    <?php 
      echo "<p class='ui dividing header'>Order #".$order_id."</p>"
    ?>
      <div class="description">
        <?php
            $ful = "";
            if ($order['Fulfilled'] == "1") $ful = "Yes";
            else $ful = "No";
            echo "<b>Customer</b>: "
            ."<a href='customer/customer.php?cust_id=".$order['Customer_ID']."'>".$order['Username']."</a><br>"
            ."<b>Fulfilled</b>: ".$ful."<br>"
            ."<b>Pickup Time</b>: ".$order['PickupTime']."<br>"
            ."<b>Comment</b>: ".$order['Comment'];
        ?>
      </div>
      <div class="extra">
        <?php 
            echo "Order placed ".$order['DatePlaced'];
        ?>
      </div>
    </div>

    <div class="item">
        <form method="POST" action="order/orderEditForm.php" class="formHeadingButton">
            <button type="submit" class="ui orange fluid button">Edit</button>
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">    
        </form>
        <?php 
            if ($order['Fulfilled'] == 0){
                echo"
                    <form method='POST' action='order/fulfill.php' class='formHeadingButton'>
                        <button type='submit' class='ui green fluid button'>Fulfill</button>
                        <input type='hidden' name='order_id' value='".$order_id."'>
                        <input type='hidden' name='setValue' value='1'>    
                    </form>
                ";
            }
        ?>
    </div>

    <div class="item ui segment">
        <div class="content"> 
            <p class='ui dividing header'>Payment Info</p>
            <div class="description">
                <?php
                    if (isset($payment['Amount']) && isset($payment['Type'])){
                        echo "Amount: ".$payment['Amount']."<br>"
                        ."Type: ".$payment['Type'];
                    } else {
                        echo "It looks like no payment has been made yet.";
                    }
                ?>
            </div>
        </div>
    </div>  
</div>

<?php
$subtotal = 0;
    if (mysqli_num_rows($detailsResult)){ ?>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Comment</th>
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($detailsResult)){
            $productTotal = ($row['PriceAtSale'] * $row['Quantity']);
            $subtotal += $productTotal;
            echo "<tr><td>"
            ."<a href='product/product.php?product_id=".$row['product_id']."'>".$row['Name']."</a></td><td>"
            .$row['PriceAtSale']."</td><td>"
            .$row['Quantity']."</td><td>"
            .$productTotal."</td><td>"
            .$row['Comment']."</td></tr>";
        }
        ?>
        </tbody>
        </table>
        <div class="item ui segment">
            <div class="content"> 
                <p class='ui dividing header'>Order Summary</p>
                <div class="description">
                    <?php
                        $tax = $subtotal * 0.08;
                        echo "Subtotal: ".$subtotal."<br>"
                        ."Tax: ".$tax."<br>"
                        ."Total: ".($subtotal + $tax);
                    ?>
                </div>
            </div>
        </div>
    <?php
    } else {
        echo "<div class='ui segment container items'>It looks like no products are in this order</div>";
    }
    ?>
            
</div>