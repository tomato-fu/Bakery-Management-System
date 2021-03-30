<?php
    require 'productHeader.php';

    $ing_id = null;
    if (isset($_POST['ing_id'])) $ing_id = $_POST['ing_id'];
    else if (isset($_GET['ing_id'])) $ing_id = $_GET['ing_id'];

    $ingResult = null;
    if ($ingStmt = $connect->prepare("SELECT * FROM Ingredient WHERE ID = ?")){
        $ingStmt->bind_param("i", $ing_id);
        $ingStmt->execute();
        $ingResult = $ingStmt->get_result();
        $ingStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

    $productResult = null;
    if ($productStmt = $connect->prepare("CALL fetch_ingredient_products(?)")){
        $productStmt->bind_param("i", $ing_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();
        $productStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }


    $ing  = mysqli_fetch_assoc($ingResult);

    ?>
    <div class="ui segment container items">
        <div class="item">
            <div class="content"> 
                <?php 
                echo "<h1 class='ui dividing header'>".$ing['Name']."</h1>"
                ?>
                <div class="description">
                    <?php
                        echo "<b>Price per KG</b>: ".$ing['PricePerKG']."<br>" 
                    ?>
                </div>
            </div>

            <div class="item">
                <form method="POST" action="product/ingredientEditForm.php" class="formHeadingButton">
                    <button type="submit" class="ui orange fluid button">Edit</button>
                    <input type="hidden" name="ing_id" value="<?php echo $ing_id; ?>">    
                </form>
            </div> 
        </div>

    <?php
    if (mysqli_num_rows($productResult)){ ?>
        <p class='ui dividing header'>'<?php echo $ing['Name']; ?>' appears in these products</p>
        <table class="ui selectable celled compact table">
            <thead>
                <tr>
                    <th class="four wide">Product Name</th>
                    <th class="two wide">Grams in Recipe</th>
                    <th class="three wide">Ingredient Cost per Item</th>
                    <th class="three wide">Ingredient Cost per Recipe</th>
                    <th class="two wide">Product Price</th>
                    <th class="one wide">View</h>         
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($productResult)){
            echo "<tr><td>"
            ."<a href='product/product.php?product_id=".$row['ID']."'>".$row['Name']."</a></td><td>"
            .$row['grams']."</td><td>"
            .round((($row['grams'] * $ing['PricePerKG'])/($row['ItemsProduced'] * 1000)), 2)."</td><td>"
            .round((($row['grams'] * $ing['PricePerKG'])/1000), 2)."</td><td>"
            .$row['price']."</td><td>"
            ."<a class='ui tiny primary button' href='product/product.php?product_id=".$row['ID']."'>View</a></td></tr>";
            //.$row['Grams']."</td><td>"
        }
        ?>
        </tbody>
        </table>
    <?php
    } else {
        echo "<div class='ui segment container items'>It looks like this ingredient isn't used in any products yet.</div>";
    }
    ?>