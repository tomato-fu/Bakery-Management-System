<?php 
    require 'orderHeader.php';

    $custResult = null;
    if ($custStmt = $connect->prepare("SELECT Username, ID FROM Customer")){
        $custStmt->execute();
        $custResult = $custStmt->get_result();
        $custStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $productResult = null;
    if ($productStmt = $connect->prepare("SELECT Name, ID, Price FROM Product ORDER BY Name")){
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $products =array();
    while($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }
                        
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
        var data = <?php echo json_encode($products)?>;
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
        <form class="ui form" action="order/newOrder.php" method="POST" onsubmit="return validateOrder()">
            <input type="hidden" id ="rowCount" name = "rowCount" value="0">
            <div class="inline fields">
                <div class="field">
                    <label>Customer Username</label>
                    <select class="ui fluid search dropdown" name="customer" required>
                        <?php 
                            while($row = mysqli_fetch_assoc($custResult)){
                                echo "<option value=".$row['ID'].">".$row['Username']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Date Placed</label>
                    <input type='date' name='datePlaced' required
                    value=<?php $t = getdate(); echo $t['year']."-".$t['mon']."-".$t['mday']; ?>>
                </div>
                <div class="field">
                    <label>Pickup Time</label>
                    <input type='datetime-local' name='pickupTime' required>
                </div>
                <div class="field">
                    <label>Payment Amount</label>
                        <input type='number' 
                               name='amount' 
                               min='0' max='99999'
                               pattern="^\d+(?:\.\d{1,2})?$"
                               step="0.01"
                               >
                </div>

                <div class="field">
                    <label>Payment Type</label>
                    <select name='type'>
                        <option selected></option> 
                        <option value='1'>Cash</option>
                        <option value='2'>WeChat</option>
                        <option value='3'>Reward Points</option>
                    </select>
                </div>
            </div>
            

            <div class="field">
                <label>Comment</label>
                <textarea rows="3" name="comment"></textarea>
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
            </tbody>
            </table>
        <div class="inline fields">
            <button type ="button" class="ui compact icon blue button " onclick="addField()">
                <i class="plus icon">
                </i> Add Product
            </button>
            <button type ="button" class="ui compact icon red button " onclick="removeField()">
                <i class="minus icon">
                </i> Remove Product
            </button>
        </div>
        <div class="field">
                    <button type="submit" class="ui fluid green button">Submit</button>
        </div>

        </form>
    </div>

    