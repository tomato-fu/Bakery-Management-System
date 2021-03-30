<?php 
    require 'orderHeader.php';

    $order_id = $_POST['order_id'];
    $setValue = $_POST['setValue'];

    if ($fulfillStmt = $connect->prepare("UPDATE `Order` SET Fulfilled = ? WHERE ID = ?")){
        $fulfillStmt->bind_param('ii', $setValue, $order_id);
        $fulfillStmt->execute();
        $fulfillStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    header("Location: singleOrder.php?order_id=".$order_id);