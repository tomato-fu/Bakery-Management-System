<?php
    require 'customerHeader.php';

    $username = $_POST['username'];
    $wechat_id = $_POST['wechat_id'];
    $comment  = $_POST['comment'];
    $cust_id = null;

    if ($stmt = $connect->prepare("INSERT INTO Customer (Username, WeChatID, Comment)
                                   VALUES(?,?,?)")){
        $stmt->bind_param("sss", $username, $wechat_id, $comment);                              
        $stmt->execute();
        $cust_id = $stmt->insert_id;
        $stmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }

    header("Location: customer.php?cust_id=".$cust_id);