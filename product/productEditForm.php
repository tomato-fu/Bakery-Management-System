<?php
    require 'productHeader.php';

    $product_id = null;
    if (isset($_POST['product_id'])) $product_id = $_POST['product_id'];
    else if (isset($_GET['product_id'])) $product_id = $_GET['product_id'];

    $productResult = null;
    if ($productStmt = $connect->prepare("SELECT * FROM Product WHERE ID = ?")){
        $productStmt->bind_param("i", $product_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $recipeResult = null;
    if ($recipeStmt = $connect->prepare("SELECT * FROM Recipe WHERE product_id = ?")){
        $recipeStmt->bind_param("i", $product_id);
        $recipeStmt->execute();
        $recipeResult = $recipeStmt->get_result();
        $recipeStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $recipeDetailsResult = null;
    if ($recipeDetailsStmt = $connect->prepare("CALL fetch_product_recipe(?)")){
        $recipeDetailsStmt->bind_param("i", $product_id);
        $recipeDetailsStmt->execute();
        $recipeDetailsResult = $recipeDetailsStmt->get_result();
        $recipeDetailsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $ingredientsResult = null;
    if ($ingredientsStmt = $connect->prepare("SELECT * From Ingredient ORDER BY Name")){
        $ingredientsStmt->execute();
        $ingredientsResult = $ingredientsStmt->get_result();
        $ingredientsStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $ingredients =array();
    while($row = mysqli_fetch_assoc($ingredientsResult)) {
        $ingredients[] = $row;
    }

    $product = mysqli_fetch_assoc($productResult);
    $recipe  = mysqli_fetch_assoc($recipeResult);
?>

<script language = 'javascript'>
    fields = [];
    index = 0;
    function addField() {
        var rowCount = document.getElementById("rowCount");
        rowCount.value = ++index;
        var container = document.getElementById("recipeForm");
        var wrapper = document.createElement("div");
        wrapper.className = "inline fields";
        wrapper.id = "wrapper" + rowCount.value;
        
        var ingredient = document.createElement("select");
        ingredient.name = "ingredient" + rowCount.value;
        ingredient.id = "ingredient" + rowCount.value;
        ingredient.className = "ui fluid search dropdown";
        var data = <?php echo json_encode($ingredients)?>;

        for (var i = 0; i < data.length; i++) {
            var option = document.createElement("option");
            option.value = data[i].ID;
            option.text = data[i].Name;
            option.id = "option" + rowCount.value;
            ingredient.appendChild(option);
        }
        
        var grams = document.createElement("input");
        grams.type = "number";
        grams.name = "grams" + rowCount.value;
        grams.id   = "grams" + rowCount.value;
        grams.value = "1";
        grams.min = "0";
        grams.max = "999999";
        grams.placeholder = "Grams";

        wrapper.appendChild(ingredient);
        wrapper.appendChild(grams);
        container.appendChild(wrapper);
        fields.push(wrapper);
    }

    function removeField(){
        var rowCount = document.getElementById("rowCount");
        if (fields.length > 0){
            fields.pop().remove();    
            rowCount.value = --index;
        }
    }

    function validateRecipe(){
        var data = <?php echo json_encode($ingredients)?>;
        var rowCount = document.getElementById("rowCount");

        current = [];
        var currOption = null;
        if (rowCount.value > 1){
            for (var i = 1; i <= rowCount.value; i++){
                currOption = document.getElementById("ingredient"+i);
                if(current.includes(currOption.value)) {
                    alert("Please remove any duplicate ingredients and try again.")
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

    <h2 class="ui dividing header">Editing '<?php echo $product['Name']; ?>'</h2>
    <form action='product/editProduct.php' method="POST" class="ui form" onsubmit="return validateRecipe()">
        <input type="hidden" name="product_id" value="<?php echo $product['ID']; ?>">
        <input type="hidden" id ="rowCount" name = "rowCount" value="0">
        <div class="fields">
            <div class="four wide field">
                <label for="name">Product Name</label>
                <input type="text" 
                       name="name" 
                       placeholder="Product Name" 
                       value="<?php echo $product['Name']; ?>"
                       required>
            </div>
            <div class="two wide field">
                <label for="price">Price</label>
                <input type="number" 
                       name="price" 
                       value="<?php echo $product['Price']; ?>" 
                       min="0" max="9999" 
                       required
                       pattern="^\d+(?:\.\d{1,2})?$"
                       step="0.01">
            </div>
            <div class="four wide field">
                <label for="foodCost">Food Cost -<i> Leave blank to calculate from recipe</i></label>
                <input type="number" 
                       name="foodCost" 
                       value="<?php if(isset($product['FoodCost'])) echo $product['FoodCost']; ?>" 
                       min="0" max="9999"
                       pattern="^\d+(?:\.\d{1,2})?$"
                       step="0.01">
            </div>
            <div class="four wide field">
                <label for="timeCost">Time to produce -<i> in minutes</i></label>
                <input type="number" 
                       name="timeCost" 
                       value="<?php if(isset($product['TimeCost'])) echo $product['TimeCost']; ?>" 
                       min="0" max="9999">
            </div>
        </div>
        <h3 class="ui dividing header">Recipe</h3>
        <div class="fields">
            <div class="two wide field">
                <label for="itemsProduced">Quantity Produced</label>
                <input type="number" 
                       name="itemsProduced" 
                       placeholder="Quantity" 
                       value="<?php if(isset($recipe['ItemsProduced'])) echo $recipe['ItemsProduced']; ?>" 
                       min="1" max="9999">
            </div>
            <div class="fourteen wide field">
                <label for="comment">Comment</label>
                <textarea name="comment" rows="3"><?php if(isset($recipe['Comment'])) echo $recipe['Comment']; ?></textarea>
            </div>
        </div>
        <div id="recipeForm">
        <script language = 'javascript'>
        <?php
        $rowCount = 1;
        while ($row = mysqli_fetch_assoc($recipeDetailsResult)){
            ?>
                
                    addField();
                    var ingredient = document.getElementById("ingredient"+<?php echo $rowCount; ?>);
                    var grams = document.getElementById("grams"+<?php echo $rowCount; ?>);
                    ingredient.value = <?php echo $row['ID']; ?>;
                    grams.value = <?php echo $row['grams']; ?>;
                
            <?php
            $rowCount++;
        }
         ?>
         </script>
        </div>
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
        <button type="submit" class="ui fluid orange button">Edit Product</button>
    </form>
</div>