<?php
    require "productHeader.php";
    
    
    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $foodCost = $_POST['foodCost'];
    $timeCost = $_POST['timeCost'];
    $product_id = $_POST['product_id'];

    $itemsProduced = $_POST['itemsProduced'];
    $comment       = $_POST['comment'];

    if ($deleteStmt = $connect->prepare("DELETE FROM Recipe_Ingredient
                                         WHERE Recipe_ID = ?")) {
        $deleteStmt->bind_param("i", $product_id);
        $deleteStmt->execute();
        $deleteStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }

    
    if ($productStmt = $connect->prepare("UPDATE Product
                                          SET Name = ?, Price = ?, FoodCost = ?, TimeCost = ?
                                          WHERE ID = ?")) {
        $productStmt->bind_param("sddii",$name, $price, $foodCost, $timeCost, $product_id);
        $productStmt->execute();
        $productStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
    
    if ($recipeStmt = $connect->prepare("UPDATE Recipe 
                                         SET ItemsProduced = ?, Comment = ?
                                         WHERE product_id = ?")) {
        $recipeStmt->bind_param("isi", $itemsProduced, $comment, $product_id);
        $recipeStmt->execute();
        $recipeStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
    
    $rowCount = $_POST['rowCount'];

    if ($recipeIngredientStmt = $connect->prepare("INSERT INTO Recipe_Ingredient
                                                   VALUES (?,?,?)
                                                   ON DUPLICATE KEY UPDATE
                                                   Grams = ?")){
    $recipeIngredientStmt->bind_param("iiii", $product_id, $ing_id, $grams, $grams);
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
    

    $i = 1;
    while ($i <= (int)$rowCount) {
       
        $ing_id = $_POST['ingredient'.(string)$i];
        $grams = $_POST['grams'.(string)$i];

        $recipeIngredientStmt->execute();
        $i++;

        $error = $connect->errno . ' ' . $connect->error;
            echo $error;
    }

    $recipeIngredientStmt->close();
    
    // Calculate and update food cost if field is left blank
    if ($foodCost == 0){
        $costResult = null;
        if ($costStmt = $connect->prepare("CALL fetch_product_foodcost(?)")) {
            $costStmt->bind_param("i",$product_id);
            $costStmt->execute();
            $costResult = $costStmt->get_result();
            $costStmt->close();  
        } else {
            $error = $connect->errno . ' ' . $connect->error;
            echo $error;
        }

        $cost = mysqli_fetch_assoc($costResult);
        $foodCost = $cost['FoodCost'];

        if ($costStmt = $connect->prepare("UPDATE Product SET FoodCost = ? WHERE ID = ?")) {
            $costStmt->bind_param("di",$foodCost, $product_id);
            $costStmt->execute();
            $costStmt->close();  
        } else {
            $error = $connect->errno . ' ' . $connect->error;
            echo $error;
        }
    }

    header("Location: product.php?product_id=".$product_id);