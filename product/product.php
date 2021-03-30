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


    $product = mysqli_fetch_assoc($productResult);
    $recipe  = mysqli_fetch_assoc($recipeResult);

    ?>
    <div class="ui segment container items">
        <div class="item">
            <div class="content"> 
                <?php 
                echo "<h1 class='ui dividing header'>".$product['Name']."</h1>"
                ?>
                <div class="description">
                    <?php
                        echo "<b>Price</b>: ".$product['Price']."<br>" 
                            ."<b>Food Cost</b>: ".$product['FoodCost']."<br>"
                            ."<b>Time To Produce</b>: ".$product['TimeCost']." min";
                    ?>
                </div>
            </div>

            <div class="item">
                <form method="POST" action="product/productEditForm.php">
                    <button type="submit" class="ui orange fluid button">Edit</button>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">    
                </form>
            </div> 
        </div>

    <?php
    if (mysqli_num_rows($recipeResult)){ ?>
        <p class='ui dividing header'>Recipe</p>
        <div class="content"> 
            <div class="description">
                <?php
                    echo "<b>Makes</b>: ".$recipe['ItemsProduced']."<br>" 
                        ."<b>Comment</b>: ".$recipe['Comment']."<br>"
                ?>
            </div>
        </div>
        <table class="ui selectable celled compact table">
            <thead>
                <tr>
                <th>Ingredient</th>
                <th class="two wide">Grams</th>
                <th class="two wide">Price Per KG</h>         
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($recipeDetailsResult)){
            echo "<tr><td>"
            ."<a href='product/ingredient.php?ing_id=".$row['ID']."'>".$row['Name']."</a></td><td>"
            .$row['grams']."</td><td>"
            .$row['PricePerKG']."</td></tr>";
        }
        ?>
        </tbody>
        </table>
    <?php
    } else {
        echo "<div class='ui segment container items'>It looks like this product has no recipe yet.</div>";
    }
    ?>
