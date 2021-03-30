<?php 
    require_once('productHeader.php');

    $ingredientResult = null;

    if ($ingredientStmt = $connect->prepare("SELECT * FROM Ingredient")){
        $ingredientStmt->execute();
        $ingredientResult = $ingredientStmt->get_result();
        $ingredientStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }

?>
<div class="ui segment container items">
  <?php  if (mysqli_num_rows($ingredientResult)){ ?>
        <table class="ui selectable compact celled table">
            <thead>
                <tr>
                    <th class="one wide">#</th>
                    <th class="tweleve wide">Name</th>
                    <th class="two wide">Price / KG</th>
                    <th class="one wide">View</th>
                </tr>
            </thead>
            <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($ingredientResult)) {
            
            echo "<tr><td>"
            .$row['ID']."</td><td>"
            ."<a href='product/ingredient.php?ing_id=".$row['ID']."'>".$row['Name']."</a></td><td>"
            .$row['PricePerKG']."</td><td>"
            ."<a class='ui tiny primary button' href='product/ingredient.php?ing_id=".$row['ID']."'>View</a></td></tr>";
        }}
        ?>
        </tbody>
        </table>
</div>