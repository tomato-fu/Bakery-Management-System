<?php 
    require 'customerHeader.php';

    $cust_id = null;
    if (isset($_POST['cust_id'])) $cust_id = $_POST['cust_id'];
    else if (isset($_GET['cust_id'])) $cust_id = $_GET['cust_id'];
    
    $custResult = null;
    $orderResult = null;
    $pointsResult = null;
    $favResult = null;

    // Fetch customer information
    if ($custStmt = $connect->prepare("CALL fetch_customer_info(?)")){
        $custStmt->bind_param('i', $cust_id);
        $custStmt->execute();
        $custResult = $custStmt->get_result();
        $custStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($favStmt = $connect->prepare("CALL fetch_customer_favorite(?)")){
        $favStmt->bind_param('i', $cust_id);
        $favStmt->execute();
        $favResult = $favStmt->get_result();
        $favStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($pointsStmt = $connect->prepare("CALL fetch_customer_points(?)")){
        $pointsStmt->bind_param('i', $cust_id);
        $pointsStmt->execute();
        $pointsResult = $pointsStmt->get_result();
        $pointsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($orderStmt = $connect->prepare("CALL fetch_customer_orders(?)")){
        $orderStmt->bind_param('i', $cust_id);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        $orderStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $cust   = mysqli_fetch_assoc($custResult);
    $points = mysqli_fetch_assoc($pointsResult);
    $fav    = mysqli_fetch_assoc($favResult);
    
    ?>

<div class="ui segment container items">
  <div class="item">
    <div class="content"> 
    <?php 
      echo "<h1 class='ui dividing header'>".$cust['Username']."</h1>"
    ?>
      <div class="description">
        <?php
            echo "<b>WeChatID</b>: ".$cust['WeChatID']."<br>" 
                ."<b>Comment</b>: ".$cust['Comment'];
        ?>
      </div>
    </div>
    <div class="item ui segment">
        <div class="content"> 
        <h3 class='ui dividing header'>Reward Points</h3>
            <?php 
                if (isset($points['gained'])) echo "Gained: ".$points['gained']."<br>";
                else echo "Gained: 0<br>";
                if (isset($points['spent'])) echo "Spent: ".$points['spent']."<br>";
                else echo "Spent: 0<br>";
                if (isset($points['gained']) && isset($points['spent'])) echo "Remaining: ".($points['gained']-$points['spent']);
                else echo "Remaining: 0";
            ?>
        </div>
    </div>

    <div class="item">
        <form method="POST" action="customer/customerEditForm.php" class="formHeadingButton">
            <button type="submit" class="ui orange fluid button">Edit</button>
            <input type="hidden" name="cust_id" value="<?php echo $cust_id; ?>">    
        </form>
    </div>

    
    
</div>


<?php
    if (mysqli_num_rows($orderResult)){ ?>
    <p class='ui dividing header'>Previous Orders</p>
        <table class="ui selectable celled compact table">
            <thead>
                <tr>
                <th class="one wide">Order#</th>
                <th class="two wide">Date</th>
                <th class="two wide">Amount</h>
                <th class="eight wide">Comment</th>
                <th class="one wide">Fulfilled</th>
                <th class="one wide">View</th>           
                </tr>
            </thead>
            <tbody>
    <?php
        $total = 0;
        while ($row = mysqli_fetch_assoc($orderResult)){
            $date = explode(" ", $row['DatePlaced']);

            $fulfilled = "";
            if ($row['Fulfilled'] == "1") $fulfilled = "Yes";
            else $fulfilled = "No";
            $total += $row['Total'];
            echo "<tr><td>"
            .$row['ID']."</td><td>"
            .$date[0]."</td><td>"
            .$row['Total']."</td><td>"
            .$row['Comment']."</td><td>"
            .$fulfilled."</td><td>"
            ."<a class='ui tiny primary button' href='order/singleOrder.php?order_id=".$row['ID']."'>View</a></td></tr>";
        }
        ?>
        </tbody>
        </table>
        <div class="item ui segment">
            <div class="content"> 
                <p class='ui dividing header'>Summary</p>
                <div class="description">
                    <?php
                        if (isset($fav['product_id'])) {
                            echo "<b>Total Spent</b>: ".$total."<br>"
                            ."<b>Favorite Product</b>: 
                            <a href='product/product.php?product_id=".$fav['product_id']."'>".$fav['favorite']."</a><span> ("
                            .$fav['total']." purchased)</span>";
                        }
                    ?>
                </div>
            </div>
        </div>
    <?php
    } else {
        echo "<div class='ui segment container items'>It looks like this customer has placed no orders yet.</div>";
    }
    ?>
            
</div>