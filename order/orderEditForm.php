<?php 
    require 'orderHeader.php';

    $order_id = $_POST['order_id'];
    
    $orderResult = null;
    $paymentResult = null;
    $custResult = null;
    $productResult = null;
    $detailResult = null;

    // Fetch order information
    if ($orderStmt = $connect->prepare("CALL fetch_single_order(?)")){
        $orderStmt->bind_param('i', $order_id);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        $orderStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    // Fetch payment information
    if ($paymentStmt = $connect->prepare("CALL fetch_single_payment(?)")){
        $paymentStmt->bind_param('i', $order_id);
        $paymentStmt->execute();
        $paymentResult = $paymentStmt->get_result();
        $paymentStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($custStmt = $connect->prepare("SELECT Username, ID FROM Customer")){
        $custStmt->execute();
        $custResult = $custStmt->get_result();
        $custStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($productStmt = $connect->prepare("SELECT Name, ID, Price FROM Product ORDER BY Name")){
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    if ($detailStmt = $connect->prepare("CALL fetch_single_order_details(?)")){
        $detailStmt->bind_param("i", $order_id);
        $detailStmt->execute();
        $detailResult = $detailStmt->get_result();
        $detailStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $products =array();
    while($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }

    $payment = mysqli_fetch_assoc($paymentResult);
    $order = mysqli_fetch_assoc($orderResult);
    ?>
<script language = 'javascript'>
    fields = [];
    index = 0;
    function addField() {

        var cols = [document.createElement("td"), document.createElement("td"), document.createElement("td")];
        var row = document.createElement("tr");

        var rowCount = document.getElementById("rowCount");
        rowCount.value = ++index;
        var container = document.getElementById("orderForm");
        
        var product = document.createElement("select");
        product.name = "product" + rowCount.value;
        product.id = "product" + rowCount.value;
        product.className = "ui fluid search dropdown";
        var data = <?php echo json_encode($products)?>;

        for (var i = 0; i < data.length; i++) {
            var option = document.createElement("option");
            option.value = data[i].ID;
            option.text = data[i].Name;
            option.id = "option" + rowCount.value;
            product.appendChild(option);
        }
        
        var quantity = document.createElement("input");
        quantity.type = "number";
        quantity.name = "quantity" + rowCount.value;
        quantity.id   = "quantity" + rowCount.value;
        quantity.value = "1";
        quantity.min = "0";
        quantity.max = "999999";
        quantity.placeholder = "quantity";

        var comment = document.createElement("input");
        comment.type = "text";
        comment.name = "comment" + rowCount.value;
        comment.id   = "comment" + rowCount.value;

        cols[0].appendChild(product);
        cols[1].appendChild(quantity);
        cols[2].appendChild(comment);
        row.appendChild(cols[0]);
        row.appendChild(cols[1]);
        row.appendChild(cols[2]);
        container.appendChild(row);
        fields.push(row);
    }

    function removeField(){
        var rowCount = document.getElementById("rowCount");
        if (fields.length > 0){
            fields.pop().remove();    
            rowCount.value = --index;
        }
    }

    function validateOrder(){
        var data = <?php echo json_encode($products) ?>;
        var rowCount = document.getElementById("rowCount");

        current = [];
        var currOption = null;
        if (rowCount.value > 1){
            for (var i = 1; i <= rowCount.value; i++){
                currOption = document.getElementById("product"+i);
                if(current.includes(currOption.value)) {
                    alert("Please remove any duplicate products and try again.")
                    return false;
                }
                current.push(currOption.value);
                console.log(current);
            }
        }
        return true;
    }
</script>
    <div class="ui segment container items"> 
        <h1 class="ui dividing header">Editing Order #<?php echo $order_id; ?></h1>
        <form class="ui form" action="order/editOrder.php" method="POST">
            <input type="hidden" id ="rowCount" name = "rowCount" value="0">
            <input type="hidden" name="order_id" value='<?php echo $order_id;?>'>
            <div class="inline fields">
                <div class="field">
                    <label>Customer Username</label>
                    <select class="ui fluid search dropdown" name="customer">
                        <?php 
                            while($row = mysqli_fetch_assoc($custResult)){
                                if ($row['ID'] == $order['Customer_ID']){
                                    echo "<option value=".$row['ID']." selected>".$row['Username']."</option>";
                                } else {
                                    echo "<option value=".$row['ID'].">".$row['Username']."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Date Placed</label>
                    <input type='date' name='datePlaced' required
                    value=<?php echo $order['DatePlaced']; ?>>
                </div>
                <div class="field">
                    <label>Pickup Time</label>
                    <?php if (isset($order['PickupTime'])){
                        $pickupTime = str_replace(" ", "T", $order['PickupTime']);
                        $pickupTime = substr($pickupTime, 0, strlen($pickupTime) - 3);
                        echo "<input type='datetime-local' name='pickupTime' value='".$pickupTime."'>";
                    } else {
                        echo "<input type='datetime-local' name='pickupTime'>";
                    }
                    ?>
                    
                </div>
                <div class="field">
                    <label>Fulfilled</label>
                    <select name='fulfilled'> 
                        <option value='1' <?php if($order['Fulfilled'] == 1) echo "selected"; ?>>Yes</option>
                        <option value='0' <?php if($order['Fulfilled'] == 0) echo "selected"; ?>>No</option>
                    </select>
                </div>
                <div class="field">
                    <label>Payment Amount</label>
                        <input type='number' 
                               name='amount' 
                               min='0' max='9999' 
                               value='<?php if(isset($payment['Amount'])) echo $payment['Amount']; ?>'
                               pattern="^\d+(?:\.\d{1,2})?$"
                               step="0.01"
                               >
                </div>
                <div class="field">
                    <label>Payment Type</label>
                    <select name='type'>
                        <option value='1' 
                            <?php if(isset($payment['Type']) && $payment['Type'] == "Cash") 
                                echo "selected"; ?>>
                                    Cash</option>
                        <option value='2' 
                            <?php if(isset($payment['Type']) && $payment['Type'] == "WeChat") 
                                echo "selected"; ?>>
                                    WeChat</option>
                        <option value='3' 
                            <?php if(isset($payment['Type']) && $payment['Type'] == "Reward Points") 
                                echo "selected"; ?>>
                                    Reward Points</option>
                        <option  
                        <?php   if(!isset($payment['Type'])) echo "selected"; ?>>
                        </option> 
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Comment</label>
                <textarea rows="3" name="comment"><?php if (isset($order['Comment'])) echo $order['Comment']; ?></textarea>
            </div>
            <div class="field">
                
            </div>
            <table class="ui celled compact table">
            <thead>
                <tr>
                <th class="four wide">Product</th>
                <th class="two wide">Quantity</th>
                <th class="ten wide">Comment</th>
                </tr>
            </thead>
            <tbody id="orderForm">
            <script language = 'javascript'>
            <?php
            $rowCount = 1;
            while ($row = mysqli_fetch_assoc($detailResult)){
            ?>
                addField();
                var product = document.getElementById("product"+<?php echo $rowCount; ?>);
                product.value = <?php echo $row['product_id']; ?>;
                var quantity = document.getElementById("quantity"+<?php echo $rowCount; ?>);
                quantity.value = <?php echo $row['Quantity']; ?>;
                var comment = document.getElementById("comment"+<?php echo $rowCount; ?>);
                comment.value = <?php echo isset($row['Comment']) ? '"'.$row['Comment'].'"' : '""'; ?>;
            <?php
                $rowCount++;
            }
            ?>
         </script>
            </tbody>
            </table>
            <div class="inline fields">
            <button type ="button" class="ui compact icon blue button " onclick="addField()">
                <i class="plus icon">
                </i> Add Ingredient
            </button>
            <button type ="button" class="ui compact icon red button " onclick="removeField()">
                <i class="minus icon">
                </i> Remove Ingredient
            </button>
        </div>
            <div class="field">
                <button type="submit" class="ui fluid orange button">Confirm Edit</button>
            </div>
        </form>
    </div>