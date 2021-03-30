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

    $ing = mysqli_fetch_assoc($ingResult);
?>

<div class="ui segment container items">
    <h2 class="ui dividing header">Editing '<?php if(isset($ing['Name'])) echo $ing['Name']; ?>'</h2>
    <form action='product/newIngredient.php' method="POST" class="ui form">
    <input type="hidden" name="ing_id" value="<?php if(isset($ing['ID'])) echo $ing['ID']; ?>">
        <div class="fields">
            <div class="four wide field">
                <label for="name">Ingredient Name</label>
                <input type="text" 
                       name="name" 
                       placeholder="Ingredient Name" 
                       value="<?php if(isset($ing['Name'])) echo $ing['Name']; ?>"
                       required>
            </div>
            <div class="three wide field">
                <label for="price">Price per KG</label>
                <input type="number"
                       pattern="^\d+(?:\.\d{1,2})?$"
                       step="0.01" 
                       name="price" 
                       placeholder="0.00"
                       value="<?php if(isset($ing['PricePerKG'])) echo $ing['PricePerKG']; ?>"
                       min="0.00" 
                       max="99999.99"
                       required
                       >
            </div>
        </div>
        <button type="submit" class="ui fluid green button">Edit Ingredient</button>
    </form>
</div>