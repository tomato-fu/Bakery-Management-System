<?php
    require "productHeader.php";
    
    
    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $foodCost = $_POST['foodCost'];
    $timeCost = $_POST['timeCost'];
    $product_id = null;

    $itemsProduced = $_POST['itemsProduced'];
    $comment       = $_POST['comment'];
    
    if ($productStmt = $connect->prepare("INSERT INTO Product (Name, Price, FoodCost, TimeCost) 
                                    VALUES(?,?,?,?)")) {
        $productStmt->bind_param("sddi",$name, $price, $foodCost, $timeCost);
        $productStmt->execute();
        $product_id = $productStmt->insert_id;
        $productStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
    
    if ($recipeStmt = $connect->prepare("INSERT INTO Recipe 
                                    VALUES(?,?,?)")) {
        $recipeStmt->bind_param("iis",$product_id, $itemsProduced, $comment);
        $recipeStmt->execute();
        $recipeStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
    
    $rowCount = $_POST['rowCount'];

    if ($recipeIngredientStmt = $connect->prepare("INSERT INTO Recipe_Ingredient
                                       VALUES (?,?,?)")){
    $recipeIngredientStmt->bind_param("iii", $product_id, $ing_id, $grams);
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

        // $error = $connect->errno . ' ' . $connect->error."<br>";
        // echo $error;
    }

    $recipeIngredientStmt->close();
    //$connect->close();
    
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