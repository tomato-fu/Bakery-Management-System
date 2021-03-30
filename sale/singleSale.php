<?php 
    
    $headerType = 1;
    if(isset($_POST['headerType'])) $headerType = $_POST['headerType'];
    else if(isset($_GET['headerType'])) $headerType = $_GET['headerType'];

    if ($headerType == 1) require './saleHeader.php';
    else require '../profit/profitHeader.php';
  
    if(isset($_POST['report_id'])) $report_id = $_POST['report_id'];
    else if(isset($_GET['report_id'])) $report_id = $_GET['report_id'];

    $saleResult = null;
    $detailsResult = null;

    if ($saleStmt = $connect->prepare("CALL fetch_single_sale(?)")){
        $saleStmt->bind_param('i', $report_id);
        $saleStmt->execute();
        $saleResult = $saleStmt->get_result();
        $saleStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    
    if ($detailsStmt = $connect->prepare("CALL fetch_single_sale_details(?)")){
    $detailsStmt->bind_param('i', $report_id);
    $detailsStmt->execute();
    $detailsResult = $detailsStmt->get_result();
    $detailsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $sale = mysqli_fetch_assoc($saleResult);
    echo $connect -> error; 
    ?>

<div class="ui segment container items">
  <div class="item">
    <div class="content"> 
    <?php 
      $date = explode(" ", $sale['Date']);
      echo "<p class='ui dividing header'>Sales Report #".$report_id." — ".$date[0]."</p>";
    ?>
      <div class="description container">
        <?php
            echo "<b>Comment</b>: ".$sale['Comment'];
        ?>
      </div>
    </div>



    <form method="POST" action="sale/saleEdit.php">
        <button type="submit" class="ui green button">Edit</button>
        <?php
        echo "<input type='hidden' name='edit' value=".$report_id.">";
        ?>
    </form>
    
    <form method="POST" action="sale/saleDelete.php" onsubmit="return confirm('Are you sure you want to delete this sales report?');">
        <button type="submit" class="ui red button">Delete</button>
        <?php
        echo "<input type='hidden' name='delete' value=".$report_id.">";
        ?> 
    </form>
</div>


<?php
$subtotal = 0;
$totalLost = 0;
$totalFoodCost = 0;
    if (mysqli_num_rows($detailsResult)) {
        ?>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Product Name</th>
                <th class="one wide">Start Count</th>
                <th class="one wide">Sold</th>
                <th class="one wide">Price per item</th>
                <th class="one wide">Revenue</th>
                <th class="one wide">Cost per item</th>
                <th class="one wide">Food Cost</th>
                <th class="one wide">Trashed</th>
                <th class="one wide">Lost Revenue</th>
                <th class="two wide">Profit</th>
                </tr>
            </thead>
            <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($detailsResult)){
            $productTotal = $row['PriceAtSale'] * $row['QuantitySold'];
            $lostRev = $row['FoodCostAtSale'] * $row['QuantityTrashed'];
            $foodCost = $row['FoodCostAtSale'] * $row['QuantitySold'];
            $totalFoodCost += $foodCost;
            $subtotal += $productTotal;
            $totalLost += $lostRev;
            echo "<tr><td>"
            ."<a href='product/product.php?product_id=".$row['product_id']."'>".$row['Name']."</a></td><td>"
            .$row['StartQuantity']."</td><td>"
            .$row['QuantitySold']."</td><td>"
            .$row['PriceAtSale']."</td><td>"
            .$productTotal."</td><td>"
            .$row['FoodCostAtSale']."</td><td>"
            .$foodCost."</td><td>"
            .$row['QuantityTrashed']."</td><td>"
            .$lostRev."</td><td>"
            .($productTotal - $lostRev - $foodCost)."</td></tr>";
        }
    } 
    ?>
        </tbody>
    </table>
    <div class="item ui segment">
        <div class="content"> 
            <p class='ui dividing header'>Report Summary</p>
            <div class="description">
                <?php
                    echo "<b>Total Revenue</b>: ".$subtotal."<br>"
                    ."<b>Total Lost Revenue</b>: ".$totalLost."<br>"
                    ."<b>Total Profit</b>: ".($subtotal - $totalLost - $totalFoodCost);
                ?>
            </div>
        </div>
    </div>
</div>

            
    