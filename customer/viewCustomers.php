<?php 
    require 'customerHeader.php';
    
    $customers = null;

    if ($stmt = $connect->prepare("SELECT * FROM Customer")){
        $stmt->execute();
        $customers = $stmt->get_result();
        $stmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error."<br>";
        echo $error;
    }

    ?>
    <div class="ui container segment">
        <?php
    if (mysqli_num_rows($customers)){ ?>
        <h2 class="ui dividing header center aligned">Customers</h2>
        <table class="ui selectable compact celled table">
            <thead>
                <tr>
                <th class="one wide">#</th>
                <th class="two wide">Username</th>
                <th class="twelve wide">Comment</th>
                <th class="one wide">View</th>
                </tr>
            </thead>
            <tbody>
    <?php
            $delimit = "</td><td>";
        while ($row = mysqli_fetch_assoc($customers)){

            echo "<tr><td>"
            .$row['ID']."</td><td>"
            ."<a href='customer/customer.php?cust_id=".$row['ID']."'>".$row['Username']."</a></td><td>"
            .$row['Comment']."</td><td>"
            ."<a class='ui tiny primary button' href='customer/customer.php?cust_id=".$row['ID']."'>View</a></td></tr>";
            ;
        }
    } 
    ?> 
        </tbody>
        </table>
    </div> 