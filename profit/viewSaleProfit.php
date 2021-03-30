<?php 
    require 'profitHeader.php';
    $query_profit = "CALL view_sale_profit();";
    $results = mysqli_query($connect, $query_profit);
    

?>

<div class="ui container segment">
        <?php
    if (mysqli_num_rows($results)){ ?>
        <h2 class="ui dividing header center aligned">Sales Profit Analysis</h2>
        <table class="ui selectable celled table">
            <thead>
                <tr>
                <th>Date</th>
                <th>Summary</th>
                <th>Profit</th>
                <th>view</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $delimit = "</td><td>";
        while ($row = mysqli_fetch_assoc($results)){
            $datePlaced = explode(" ", $row['Date']);

            echo "<tr><td>"
            .$datePlaced[0].$delimit
            .$row['summary'].$delimit
            .$row['Profit'].$delimit.
            "<form method='POST' action='sale/singleSale.php'>
                <input type='hidden' name='Sales_ID' value=".$row['sale_ID'].">
                <input type='hidden' name='check' value='0'>
                <button type='submit' class='ui tiny primary button'>"."View</button></form></td></tr>"
            ;
        }
    } 
    ?> 
        </tbody>
        </table>
    </div> 
<?php 
    require '../footer.php';
?>