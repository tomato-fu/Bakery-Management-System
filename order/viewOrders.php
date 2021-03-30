<?php 
    require 'orderHeader.php';
    
    if (isset($_POST['startDate']) && $_POST['startDate']) $startDate = $_POST['startDate'];
    else $startDate = "1980-01-01";
    if (isset($_POST['endDate']) && $_POST['endDate']) $endDate = $_POST['endDate'];
    else {
        $today = getdate();
        $endDate = $today['year']."-".$today['mon']."-".$today['mday'];
    }

    $queryType = null;
    if (isset($_POST['queryType'])) $queryType = $_POST['queryType'];
    else if (isset($_GET['queryType'])) $queryType= $_GET['queryType'];

    $searchResult = null;
    if ($queryType == 0 || $queryType == 1){   
        if ($searchStmt = $connect->prepare("CALL fetch_select_orders_in_range(?,?,?)")) {
            $searchStmt->bind_param("iss", $queryType, $startDate, $endDate);
            $searchStmt->execute();
            $searchResult = $searchStmt->get_result();
            $searchStmt->close();  
        } else {
            $error = $connect->errno . ' ' . $connect->error;
            echo $error;
        }
    } else {
        if ($searchStmt = $connect->prepare("CALL fetch_orders_in_range(?,?)")) {
            $searchStmt->bind_param("ss", $startDate, $endDate);
            $searchStmt->execute();
            $searchResult = $searchStmt->get_result();
            $searchStmt->close();  
        } else {
            $error = $connect->errno . ' ' . $connect->error;
            echo $error;
        }
    }
    ?>
    <div class="ui container segment">
    <div class="ui item segment">
        <form class="ui form" action="order/viewOrders.php" method="POST">
            <input type="hidden" name="queryType" value="<?php echo $queryType; ?>">
            <div class="inline fields">
                <div class="field">
                    <label for="startDate">Start Date</label>
                    <input type="date" name="startDate"
                    value="<?php if (isset($_POST['startDate'])) echo $_POST['startDate']; ?>"
                    >
                </div>
                <div class="field">
                    <label for="endDate">End Date</label>
                    <input type="date" name="endDate"
                    value="<?php if (isset($_POST['endDate'])) echo $_POST['endDate']; ?>"
                    >
                </div>
                <div class="field">
                    <button type="submit" class="ui small green button">Search</button>
                </div>
                
            </div>
        </form>
    </div>
        <?php
    if (mysqli_num_rows($searchResult)){ ?>
        <h2 class="ui dividing header center aligned">Orders</h2>
        <table class="ui selectable compact celled table">
            <thead>
                <tr>
                <th class="one wide">#</th>
                <th class="two wide">Username</th>
                <th>Pickup Time</th>
                <th>Date Placed</th>
                <th class="one wide">Fulfilled</th>
                <th>Comment</th>
                <th class="one wide">View</th>
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($searchResult)){
            $datePlaced = explode(" ", $row['DatePlaced']);
            $fulfilled = "";
            if ($row['Fulfilled'] == "1") $fulfilled = "Yes";
            else $fulfilled = "No";

            echo "<tr><td>"
            .$row["order_id"]."</td><td>"
            ."<a href='customer/customer.php?cust_id=".$row['Customer_ID']."'>".$row['Username']."</a></td><td>"
            .$row['PickupTime']."</td><td>"
            .$datePlaced[0]."</td><td>"
            .$fulfilled."</td><td>"
            .$row['Comment']."</td><td>"
            ."<a class='ui tiny primary button' href='order/singleOrder.php?order_id=".$row['order_id']."'>View</a></td></tr>";
        }
   } else {
    ?>
        <div class="ui item segment">
            <div>
                There does not seem to be any orders included in this search...
            </div>
        </div>
    <?php
   }
    ?> 
        </tbody>
        </table>
    </div> 