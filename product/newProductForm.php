<?php
    require 'productHeader.php';

    $query  = "SELECT * From Ingredient ORDER BY Name";
    $results = mysqli_query($connect, $query);
    $to_encode =array();
    while($row = mysqli_fetch_assoc($results)) {
        $to_encode[] = $row;
    }
    json_encode($to_encode);
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
        
        
        var ingredient = document.createElement("select");
        ingredient.name = "ingredient" + rowCount.value;
        ingredient.id = "ingredient" + rowCount.value;
        ingredient.className = "ui fluid search dropdown";
        var data = <?php echo json_encode($to_encode)?>;


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
        var data = <?php echo json_encode($to_encode)?>;
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
                
            }
        }
        return true;
    }
</script>

<div class="ui segment container items">

    <h2 class="ui dividing header">Entering New Product</h2>
    <form action='product/newProduct.php' method="POST" class="ui form" onsubmit="return validateRecipe()">
        <input type="hidden" id ="rowCount" name = "rowCount" value="0">
        <div class="fields">
            <div class="four wide field">
                <label for="name">Product Name</label>
                <input type="text" name="name" placeholder="Product Name" required>
            </div>
            <div class="two wide field">
                <label for="price">Price</label>
                <input type="number" 
                       name="price" 
                       value="0" 
                       min="0" max="9999" 
                       required
                       pattern="^\d+(?:\.\d{1,2})?$"
                       step="0.01">
            </div>
            <div class="four wide field">
                <label for="foodCost">Food Cost -<i> Leave blank to calculate from recipe</i></label>
                <input type="number" 
                       name="foodCost" 
                       value="0" 
                       min="0" max="9999"
                       pattern="^\d+(?:\.\d{1,2})?$"
                       step="0.01">
            </div>
            <div class="four wide field">
                <label for="timeCost">Time to produce -<i> in minutes</i></label>
                <input type="number" name="timeCost" value="0" min="0" max="9999">
            </div>
        </div>
        <h3 class="ui dividing header">Recipe</h3>
        <div class="fields">
            <div class="two wide field">
                <label for="itemsProduced">Quantity Produced</label>
                <input type="number" name="itemsProduced" placeholder="Quantity" value="1" min="1" max="9999">
            </div>
            <div class="fourteen wide field">
                <label for="name">Comment</label>
                <textarea name="comment" rows="3"></textarea>
            </div>
        </div>
        <div id="recipeForm">
        
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
        <button type="submit" class="ui fluid green button">Submit New Product</button>
    </form>
</div>