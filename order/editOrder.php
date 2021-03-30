<?php 
    require 'orderHeader.php';

    $customer_id = $_POST['customer'];
    $pickupTime = explode("T",$_POST['pickupTime']);
    $fulfilled = $_POST['fulfilled'];
    $datePlaced = $_POST['datePlaced'];
    $orderComment = $_POST['comment'];
    $payAmount = $_POST['amount'];
    $payType = $_POST['type'];
    $order_id = $_POST['order_id'];

    $pickupTime = $pickupTime[0]." ".$pickupTime[1].":00";
    $currDate = date('Y-m-d H:i:s', time());
    
    if ($orderInsertStmt = $connect->prepare("UPDATE `Order`
                                              SET Customer_ID = ?, DatePlaced = ?, PickupTime = ?, Fulfilled = ?, Comment = ?
                                              WHERE ID = ?")){
        $orderInsertStmt->bind_param("issisi", $customer_id, $datePlaced, $pickupTime, $fulfilled, $orderComment, $order_id);
        $orderInsertStmt->execute();
        $orderInsertStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }

    if ($payInsertStmt = $connect->prepare("INSERT INTO `Payment` (order_id, Payment_Type_ID, Amount)
                                            VALUES (?,?,?)
                                            ON DUPLICATE KEY UPDATE
                                            Payment_Type_ID = ?, Amount = ?
                                            ")){
        $payInsertStmt->bind_param("iiiii", $order_id, $payType, $payAmount, $payType, $payAmount);
        $payInsertStmt->execute();
        $payInsertStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }
    
    if ($deleteStmt = $connect->prepare("DELETE FROM Order_Details
                                         WHERE order_id = ?")) {
        $deleteStmt->bind_param("i", $order_id);
        $deleteStmt->execute();
        $deleteStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }

    $rowCount = $_POST['rowCount'];
    if ($detailsStmt = $connect->prepare("INSERT INTO Order_Details (order_id, product_id, PriceAtSale, FoodCostAtSale, Quantity, Comment)
                                          VALUES (?,?,?,?,?,?)")){
        $detailsStmt->bind_param("iiddis", $order_id, $product_id, $price, $cost, $quantity, $comment);
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    
    if ($productStmt = $connect->prepare("SELECT Price, FoodCost FROM Product
                                          WHERE ID = ?")){
        $productStmt->bind_param("i", $product_id);
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $i = 1;
    while ($i <= (int)$rowCount) {
       
        $product_id = $_POST['product'.$i];
        $quantity = $_POST['quantity'.$i];
        $comment = $_POST['comment'.$i];

        $productStmt->execute();
        $result = $productStmt->get_result();
        $product = mysqli_fetch_assoc($result);

        $price = $product['Price'];
        $cost = $product['FoodCost'];

        $detailsStmt->execute();
        $i++;

        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }

    $detailsStmt->close();

    header("Location: singleOrder.php?order_id=".$order_id);
  