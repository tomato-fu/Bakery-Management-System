<?php  
    require "saleHeader.php";

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

?>

<div class = "ui container segment">
    <div class="ui item segment">
        <form class="ui form" action="sale/viewReport.php" method="POST">
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
if (mysqli_num_rows($reportResult)) {
    ?>
    <h2 class="ui dividing header center aligned">Sales Reports</h2>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th class="two wide">Date</th>
                <th>Comment</th>
                <th>Revenue</th>
                <th>Profit</th>
                <th class="one wide">View</th>
                </tr>
            </thead>
            <tbody>
    <?php   
        while ($row = mysqli_fetch_assoc($reportResult)){
            $date = explode(" ", $row['Date']);

            echo "<tr><td>"
            .$date[0]."</td><td>"
            .((strlen($row['Comment']) > 50) ? substr($row['Comment'], 0, 50)."..." : $row['Comment'])."</td><td>"
            .$row['revenue']."</td><td>"
            .$row['profit']."</td><td>"
            ."<a class='ui tiny primary button' href='sale/singleSale.php?report_id=".$row['report_id']."&headerType=1'>"."View</a></td></tr>";

        }

    }
    ?>
    </tbody>
    </table>
</div>

<?php 
    require '../footer.php';
?>

