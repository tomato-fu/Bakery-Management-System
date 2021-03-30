<?php 
// Sale 
    require 'profitHeader.php';
    $date = $_POST['date'];
    

    if ($saleProfitStmt = $connect->prepare("CALL get_all_sales_ID(?)")){
        $saleProfitStmt->bind_param('s', $date);
        $saleProfitStmt->execute();
        $saleProfitResult = $saleProfitStmt->get_result();
        $saleProfitStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

   
  

if (mysqli_num_rows($saleProfitResult)) {

    $sale_id = mysqli_fetch_assoc($saleProfitResult)['sale_ID'];
    $saleResult = null;
    $detailsResult = null;

    if ($saleStmt = $connect->prepare("CALL view_single_sale(?)")){
        $saleStmt->bind_param('i', $sale_id);
        $saleStmt->execute();
        $saleResult = $saleStmt->get_result();
        $saleStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    

    if ($detailsStmt = $connect->prepare("CALL view_single_sale_details(?)")){
    $detailsStmt->bind_param('i', $sale_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    $detailsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

  

    $sale = mysqli_fetch_assoc($saleResult);
    $dateplaced = explode(" ", $sale['Date']);
    echo "
    <div class='ui segment container items'>
    <div class='item'>
    <div class='content'> 
    <p class='ui dividing header'>Sales</p>
    <div class='description'>
    <p> Date: $dateplaced[0]</p>
     </div></div></div>";


    if (mysqli_num_rows($detailsResult)) {
        ?>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Product</th>
                <th>Price</th>
                <th>StartQuantity</th>
                <th>QuantitySold</th>
                <th>QuantityTrashed</th>
                <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($detailsResult)){
 
            echo "<tr><td>"
            .$row['Name']."</td><td>"
            .$row['Price_sale']."</td><td>"
            .$row['StartQuantity']."</td><td>"
            .$row['QuantitySold']."</td><td>"
            .$row['QuantityTrashed']."</td><td>"
            .$row['Amount']."</td></tr>";
        }
    }
} 

?>
        </tbody>
    </table>
</div>



<?php

//orders
if ($orderProfitStmt = $connect->prepare("CALL get_all_orders_ID(?)")){
    $orderProfitStmt->bind_param('s', $date);
    $orderProfitStmt->execute();
    $orderProfitResult = $orderProfitStmt->get_result();
    $orderProfitStmt->close();
} else {
    $error = $connect->errno . ' ' . $connect->error;
    echo $error;
}

if (mysqli_num_rows($orderProfitResult)) {
    
    echo "
    <div class='ui segment container items'>
    <div class='item'>
    <div class='content'> 
    <p class='ui dividing header'>Orders</p>
    <div class='description'>
    <p> Date:$date</p>
    </div></div></div>";

while ($row = mysqli_fetch_assoc($orderProfitResult)) {
    $order_id = $row['order_ID'];
    
    $detailsResult = null;

    
    

    if ($detailsStmt = $connect->prepare("CALL view_single_order_details(?)")){
    $detailsStmt->bind_param('i', $order_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    $detailsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

  

    
    
   

      

    


    if (mysqli_num_rows($detailsResult)) {
        ?>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Sales</th>
                
                </tr>
            </thead>
            <tbody>
        <?php
        while ($row_order = mysqli_fetch_assoc($detailsResult)){
            $total_summary = $row_order['PriceAtSale'] * $row_order['Quantity'];
            echo "<tr><td>"
            .$row_order['Name']."</td><td>"
            .$row_order['PriceAtSale']."</td><td>"
            .$row_order['Quantity']."</td><td>"
            
            .$total_summary."</td></tr>";
        }
    } 
}
}
?>
        </tbody>
    </table>
</div>

    <div class="ui segment container items">
        <div class='item'>
        <div class="content"> 
            <p class='ui dividing header'>Summary</p>
            <div class="description">
                <?php
                $summary  =$_POST['summary'];
                $profit = $_POST['profit'];
                    echo "Profit: ".$profit."<br>"

                    ."Total: ".$summary;
                ?>
            </div>
        </div>
    </div>
</div>
  

