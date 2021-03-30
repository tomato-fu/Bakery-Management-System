<?php 
    require 'orderHeader.php';

    $customer_id = $_POST['customer'];
    $pickupTime = explode("T",$_POST['pickupTime']);
    $datePlaced = $_POST['datePlaced'];
    $orderComment = $_POST['comment'];
    $payAmount = $_POST['amount'];
    $payType = $_POST['type'];

    if (isset($_POST['pickupTime']))$pickupTime = $pickupTime[0]." ".$pickupTime[1].":00";
    $order_id = null;
    $fulfilled = 0;

    if ($orderInsertStmt = $connect->prepare("INSERT INTO `Order`
                                    (Customer_ID, DatePlaced, PickupTime, Fulfilled, Comment) 
                                    VALUES(?,?,?,?,?)")){
        $orderInsertStmt->bind_param("issis", $customer_id, $datePlaced, $pickupTime, $fulfilled, $orderComment);
        $orderInsertStmt->execute();
        $order_id = $orderInsertStmt->insert_id;
        $orderInsertStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($payInsertStmt = $connect->prepare("INSERT INTO `Payment`
                                    (order_id, Payment_Type_ID, Amount) 
                                    VALUES(?,?,?)")){
        $payInsertStmt->bind_param("iii", $order_id, $payType, $payAmount);
        $payInsertStmt->execute();
        $payInsertStmt->close();
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