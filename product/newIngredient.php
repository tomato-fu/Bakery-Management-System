<?php
    require 'productHeader.php';

    $name = $_POST['name'];
    $price = $_POST['price'];
    $ing_id = $_POST['ing_id'];

    // If ID is sent via POST, request is an edit
    if (isset($_POST['ing_id'])){
        if ($stmt = $connect->prepare("UPDATE Ingredient
                                       SET Name = ?, PricePerKG = ?
                                       WHERE ID = ?")){
            $stmt->bind_param("sdi", $name, $price, $ing_id);                              
            $stmt->execute();
            $stmt->close();
        } else {
            $error = $connect->errno . ' ' . $connect->error."<br>";
            echo $error;
        }
    // If ID is not set, it's an insert
    } else {
        if ($stmt = $connect->prepare("INSERT INTO Ingredient (Name, PricePerKG)
                                   VALUES(?,?)")){
            $stmt->bind_param("sd", $name, $price);                              
            $stmt->execute();
            $ing_id = $stmt->insert_id;
            $stmt->close();
        } else {
            $error = $connect->errno . ' ' . $connect->error."<br>";
            echo $error;
        }
    }
    

    header("Location: ingredient.php?ing_id=".$ing_id);