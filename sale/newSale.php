<?php
    require "saleHeader.php";

    $productResult = null;
    if ($productStmt = $connect->prepare("CALL fetch_all_products()")){
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $dateResult = null;
    if ($dateStmt = $connect->prepare("SELECT Date FROM Sales_Report")){
        $dateStmt->execute();
        $dateResult = $dateStmt->get_result();
        $dateStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    
    $data = [];
    while ($info = mysqli_fetch_assoc($productResult)) {
        $data[] = $info;
    }

    $dates = [];
    while ($info = mysqli_fetch_assoc($dateResult)) {
        $dates[] = $info;
    }
?>

<script language = 'javascript'>
    fields = [];
    index = 0;
    function addField() {
        var cols = [document.createElement("td"), document.createElement("td"), 
                    document.createElement("td"), document.createElement("td")];
        var row = document.createElement("tr");

        var rowCount = document.getElementById("rowCount");
        rowCount.value = ++index;
        var container = document.getElementById("reportForm");

        var product = document.createElement("select");
        product.id = "product" + rowCount.value;
        product.name = "product" + rowCount.value;
        product.className = "ui fluid search dropdown";
        var data = <?php echo json_encode($data)?>;
        for (var i = 0; i <data.length; i++) {
            var option = document.createElement("option");
            option.value = data[i].product_id;
            option.text = data[i].Name;
            product.appendChild(option);
        }
        
    
        var startQuantity = document.createElement("input");
        startQuantity.name = startQuantity.id = "startQuantity" + rowCount.value;
        startQuantity.type = "number";
        startQuantity.value = 1;
        startQuantity.min = 0;
        startQuantity.max = 9999;


        var soldQuantity = document.createElement("input");
        soldQuantity.name = soldQuantity.id = "soldQuantity" + rowCount.value;
        soldQuantity.type = "number";
        soldQuantity.value = 0;
        soldQuantity.min = 0;
        soldQuantity.max = 9999;

        var trashedQuantity = document.createElement("input");
        trashedQuantity.name = trashedQuantity.id = "trashedQuantity" + rowCount.value;
        trashedQuantity.type = "number";
        trashedQuantity.value = 0;
        trashedQuantity.min = 0;
        trashedQuantity.max = 9999;


        cols[0].appendChild(product);
        cols[1].appendChild(startQuantity);
        cols[2].appendChild(soldQuantity);
        cols[3].appendChild(trashedQuantity);
        row.appendChild(cols[0]);
        row.appendChild(cols[1]);
        row.appendChild(cols[2]);
        row.appendChild(cols[3]);
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

    function validateReport(){
        var dateJSON = <?php echo json_encode($dates)?>;

        var rowCount = document.getElementById("rowCount");
        if (rowCount.value == 0) {
            alert("Please add at least one product and try again.");
            return false;
        }
        dateSelected = document.getElementById("date");
        for (var i = 0; i < dateJSON.length; i++){
            if (dateSelected.value == dateJSON[i].Date){
                alert('A sales report for that date already exists.');
                return false;
            }
        }
        current = [];
        var currOption = null;
        if (rowCount.value >= 1){
            for (var i = 1; i <= rowCount.value; i++){
                currOption = document.getElementById("product"+i);
                start = parseInt(document.getElementById("startQuantity"+i).value);
                sold = parseInt(document.getElementById("soldQuantity"+i).value);
                trashed = parseInt(document.getElementById("trashedQuantity"+i).value);
                if(current.includes(currOption.value)) {
                    alert("Please remove any duplicate products and try again.");
                    return false;
                }
                else if(start < (sold + trashed)){
                    alert("There are more products sold & trashed than the starting number on row "+i+". Please fix this and try again.");
                    return false;
                }
                current.push(currOption.value);
            }
        }
        return true;
    }
</script>

<div class = "ui segment container">
    <h2 class="ui dividing header">Entering new in-store sales report</h2> 
	<div class = "ui form">
		<form action="sale/enterNewSale.php" method="POST" class="ui form" onsubmit="return validateReport()">
            <input type="hidden" id ="rowCount" name="rowCount" value="0">
			<div class="inline fields">
				<div class="field">
					<label>Date</label>
					<input type="date" name="Date" placeholder="Date" id="date" required>
				</div>
				<div class="field ten wide">
					<label>Comment</label>
					<textarea name="Comment" placeholder="Comment" rows="3"></textarea>
				</div>
			</div>
			<div class="field">
			<h3 class="ui dividing header">Products</h3> 
								
			</div>
			<table class="ui selectable celled table" id ="table">
            <thead>
                <tr>
                <th class="four wide">Product</th>
                <th class="four wide">Start Quantity</th>
                <th class="four wide">Quantity Sold</th>
                <th class="four wide">Quantity Trashed</th>
                </tr>
            </thead>
            <tbody id="reportForm">
            </tbody>
            </table>
            <button type ="button" class="ui compact icon blue button inline" onclick="addField()">
                <i class="plus icon">
                </i> Add Product
            </button>
            <button type ="button" class="ui compact icon red button " onclick="removeField()">
                <i class="minus icon">
                </i> Remove Product
            </button>
			<br><br>
			<button type = "submit" class = "ui fluid green button">Submit</button>
		</form>
	</div>
</div>
