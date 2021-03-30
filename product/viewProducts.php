<?php 
    require 'productHeader.php';

    $productResult = null;

    if ($productStmt = $connect->prepare("SELECT * FROM Product")){
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

?>
<div class="ui segment container items">
  <?php  if (mysqli_num_rows($productResult)){ ?>
        <table class="ui selectable compact celled table">
            <thead>
                <tr>
                    <th class="one wide">#</th>
                    <th>Name</th>
                    <th class="two wide">Price</th>
                    <th class="two wide">Food cost</th>
                    <th class="two wide">Time to produce</th>
                    <th class="one wide">View</th>
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($productResult)) {
            
            echo "<tr><td>"
            .$row['ID']."</td><td>"
            ."<a href='product/product.php?product_id=".$row['ID']."'>".$row['Name']."</a></td><td>"
            .$row['Price']."</td><td>"
            .$row['FoodCost']."</td><td>"
            .$row['TimeCost']." min</td><td>"
            ."<a class='ui tiny primary button' href='product/product.php?product_id=".$row['ID']."'>View</a></td></tr>";
        }}
        ?>
        </tbody>
        </table>
</div>