<?php 
    require '../header.php';
    
    $dates = array();


    if (isset($_POST['startDate']) && $_POST['startDate']) $startDate = $_POST['startDate'];
    else $startDate = "1980-01-01";
    if (isset($_POST['endDate'])  && $_POST['endDate']) $endDate = $_POST['endDate'];
    else {
        $today = getdate();
        $endDate = $today['year']."-".$today['mon']."-".$today['mday'];
    }

    $reportResult = null;
    if ($reportStmt = $connect->prepare("CALL fetch_sale_profit_in_range(?,?)")) {
    $reportStmt->bind_param("ss", $startDate, $endDate);
    $reportStmt->execute();
    $reportResult = $reportStmt->get_result();
    $reportStmt->close();  
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $orderResult = null;
    if ($orderStmt = $connect->prepare("CALL fetch_order_profit_in_range(?,?)")) {
    $orderStmt->bind_param("ss", $startDate, $endDate);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    $orderStmt->close();  
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    while ($row = mysqli_fetch_assoc($reportResult)){
        $date = explode(" ", $row['Date']);
        $dates[$date[0]] = array(
            "report_revenue" => $row['revenue'],
            "report_profit"  => $row['profit'],
            "report_id"      => $row['report_id']
        );
    }

    while ($row = mysqli_fetch_assoc($orderResult)){
        $date = explode(" ", $row['DatePlaced']);
        if (!isset($dates[$date[0]])){
            $dates[$date[0]] = array();
        }
        $dates[$date[0]]["order_revenue"] = $row['revenue'];
        $dates[$date[0]]["order_profit"] = $row['profit'];
    }
    
?>
<div class="ui container segment">
    <h2 class="ui dividing header center aligned">Profit Analysis</h2>
    <div class="ui item segment">
        <form class="ui form" action="profit/viewTotalProfit.php" method="POST">
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
    if (count($dates)){ ?>
        <h3 class="ui dividing header">
        Viewing:
        <?php 
            if (isset($_POST['startDate'])) echo " ".$startDate;
            else echo " * ";
            echo " — ";
            if (isset($_POST['endDate'])) echo " ".$endDate;
            else echo "Present"
        ?>
        </h3>
        <?php
        $rows = array();
        $keys = array_keys($dates);
        rsort($keys); 
        $total_order_revenue = 0;
        $total_order_profit = 0;
        $total_report_revenue = 0;
        $total_report_profit = 0;
        foreach($keys as $key){
            $order_revenue = isset($dates[$key]["order_revenue"]) ? $dates[$key]["order_revenue"] : '0';
            $total_order_revenue += $order_revenue;
            $order_profit = isset($dates[$key]["order_profit"]) ? $dates[$key]["order_profit"] : '0';
            $total_order_profit += $order_profit;
            $report_revenue = isset($dates[$key]["report_revenue"]) ? $dates[$key]["report_revenue"] : '0';
            $total_report_revenue += $report_revenue;
            $report_profit = isset($dates[$key]["report_profit"]) ? $dates[$key]["report_profit"] : '0';
            $total_report_profit += $report_profit;

            $isReport = ($report_revenue == 0 && $report_profit == 0);
            $isOrder = ($order_revenue == 0 && $order_profit == 0);

            $rows[] = "<tr><td>"
            .$key."</td><td>"
            .$order_revenue."</td><td>"
            .$order_profit."</td><td>"
            .$report_revenue."</td><td>"
            .$report_profit."</td><td>"
            .($order_revenue + $report_revenue)."</td><td>"
            .($order_profit + $report_profit)."</td><td>"
            ."<form method='POST' action='sale/singleSale.php'>
                <input type='hidden' name='report_id' value=".(isset($dates[$key]["report_id"]) ? $dates[$key]["report_id"] : 0).">
                <div ".($isReport ? 'data-tooltip="No sales report for this date." data-position="left center"' : '').">
                <button type='submit' class='ui fluid tiny primary button ".($isReport ? "disabled' disabled": "'").">
                View Report</button></div></form></td><td>"
            ."<form method='POST' action='order/viewOrders.php'>
                <input type='hidden' name='queryType' value='-1'>
                <input type='hidden' name='startDate' value=".$key.">
                <input type='hidden' name='endDate' value=".$key.">
                <div ".($isOrder ? 'data-tooltip="No orders on this date." data-position="right center"' : '').">
                <button type='submit' class='ui fluid tiny primary button ".($isOrder ? "disabled' disabled": "'").">
                View Orders</button></div></form></td></tr>";
        }?>
        <div class="item ui segment">
            <div class="content"> 
                <div class="description">
                    <?php
                        echo "<b>Total Order Revenue</b>: ".$total_order_revenue."<br>"
                        ."<b>Total Order Profit</b>: ".$total_order_profit."<br>"
                        ."<b>Total In-Store Revenue</b>: ".$total_report_revenue."<br>"
                        ."<b>Total In-Store Profit</b>: ".$total_report_profit."<br>"
                        ."<b>Total Revenue</b>: ".($total_order_revenue + $total_report_revenue)."<br>"
                        ."<b>Total Profit</b>: ".($total_order_profit + $total_report_revenue)."<br>";
                    ?>
                </div>
            </div>
        </div>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Date</th>
                <th>Order Revenue</th>
                <th>Order Profit</th>
                <th>In-Store Revenue</th>
                <th>In-Store Profit</th>
                <th>Total Revenue</th>
                <th>Total Profit</th>
                <th class="two wide">View Sales Report</th>
                <th class="two wide">View Orders</th>
                </tr>
            </thead>
            <tbody>
        <?php
        foreach($rows as $row){
            echo $row;
        }
    } else {
        ?>
            <div class="ui item segment">
                <div>
                    There does not seem to be any information included in this search...
                </div>
            </div>
        <?php
       }
    ?> 
        </tbody>
        </table>
    </div> 
<?php 
    require '../footer.php';
?>