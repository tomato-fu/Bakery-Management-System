<?php
    require 'customerHeader.php';

    $username = $_POST['username'];
    $wechat_id = $_POST['wechat_id'];
    $comment  = $_POST['comment'];
    $cust_id = $_POST['cust_id'];

    if ($stmt = $connect->prepare("UPDATE Customer
                                   SET Username = ?, WeChatID = ?, Comment = ?
                                   WHERE ID = ?")){
        $stmt->bind_param("sssi", $username, $wechat_id, $comment, $cust_id);                              
        $stmt->execute();
        $stmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }

    header("Location: customer.php?cust_id=".$cust_id);