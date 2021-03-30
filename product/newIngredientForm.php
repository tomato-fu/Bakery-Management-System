<?php
    require 'productHeader.php';
?>

<div class="ui segment container items">
    <h2 class="ui dividing header">Entering New Ingredient</h2>
    <form action='product/newIngredient.php' method="POST" class="ui form">
        <div class="fields">
            <div class="four wide field">
                <label for="name">Ingredient Name</label>
                <input type="text" name="name" placeholder="Ingredient Name" required>
            </div>
            <div class="three wide field">
                <label for="price">Price per KG</label>
                <input  type="number" 
                        name="price" 
                        pattern="^\d+(?:\.\d{1,2})?$"
                        step="0.01"
                        placeholder="Price" 
                        required 
                        min="0" 
                        max="99999">
            </div>
        </div>
        <button type="submit" class="ui fluid green button">Submit New Ingredient</button>
    </form>
</div>