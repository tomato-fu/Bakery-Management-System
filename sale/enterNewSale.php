<?php
    require "./saleHeader.php";
    
    $rowCount = $_POST['rowCount'];
    $date = $_POST['Date'];
    $comment = $_POST['Comment'];
    
    if ($reportInsertStmt = $connect->prepare("INSERT INTO `Sales_Report`
                                    (Date,Comment) 
                                    VALUES(?,?)")) {
        $reportInsertStmt->bind_param("ss",$date, $comment);
        $reportInsertStmt->execute();
        $report_id = $reportInsertStmt->insert_id;
        $reportInsertStmt->close();  
     }
     else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }
 
     $new_stmt = $connect->prepare("CALL insert_new_report_details(?,?,?,?,?,?,?)");
    $new_stmt->bind_param("iiiiidd", $Sales_Report_ID, $product_id, $StartQuantity, $QuantitySold, $QuantityTrashed, $Price, $FoodCost);

    $x=1;
    while ($x <= (int)$rowCount) {
        $p_id = $_POST['product'.(string)$x];

        $productStmt = $connect->prepare("CALL fetch_single_product(?)");
        $productStmt->bind_param("i",$p_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();

        $product = mysqli_fetch_assoc($productResult);
        $Price = $product['Price'];
        $FoodCost = $product['FoodCost'];
       
        $Sales_Report_ID = $report_id;
        $product_id = $p_id;
        $StartQuantity = $_POST['startQuantity'.(string)$x];
        $QuantitySold = $_POST['soldQuantity'.(string)$x];
        $QuantityTrashed = $_POST['trashedQuantity'.(string)$x];
        $new_stmt->execute();
        $x++;
    }

    $new_stmt->close();
    $connect->close();
    header("Location: singleSale.php?report_id=".$report_id);