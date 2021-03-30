<?php 
    require 'saleHeader.php';
    $date = $_POST['Date'];
    $comment = $_POST['Comment'];
    $report_id = $_POST['report_id'];
    $rowCount = $_POST['rowCount'];
    

    if ($reportEditStmt = $connect->prepare("UPDATE `Sales_Report`
                                    SET Date = ?,Comment = ?
                                    WHERE ID = ?")) {
        $reportEditStmt->bind_param("ssi",$date, $comment,$report_id);
        $reportEditStmt->execute();
        $reportEditStmt->close();  
     } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }

     if ($deleteStmt = $connect->prepare("DELETE FROM Sales_Report_Details
                                          WHERE Sales_Report_ID = ?")) {
        $deleteStmt->bind_param("i", $report_id);
        $deleteStmt->execute();
        $deleteStmt->close();  
    } else {
    $error = $connect->errno . ' ' . $connect->error;
    echo $error;
    }

    $new_stmt = $connect->prepare("CALL insert_new_report_details(?,?,?,?,?,?,?)");
    $new_stmt->bind_param("iiiiidd", $report_id, $product_id, $start, $sold, $trashed, $price, $cost);
    $i=1;
    while ($i <= (int)$rowCount) {
        $product_id = $_POST['product'.(string)$i];
       
        $productStmt = $connect->prepare("CALL fetch_single_product(?)");
        $productStmt->bind_param("i",$product_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();

        $product = mysqli_fetch_assoc($productResult);
        $price = $product['Price'];
        $cost  = $product['FoodCost'];

        $start = $_POST['startQuantity'.(string)$i];
        $sold = $_POST['soldQuantity'.(string)$i];
        $trashed = $_POST['trashedQuantity'.(string)$i];
        $new_stmt->execute();
        $i++;
    }

    $new_stmt->close();

    header("Location: singleSale.php?report_id=".$report_id);
    
     
?>
    