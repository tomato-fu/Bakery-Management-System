<?php 
    require 'customerHeader.php';

    $cust_id = null;
    if (isset($_POST['cust_id'])) $cust_id = $_POST['cust_id'];
    else if (isset($_GET['cust_id'])) $cust_id = $_GET['cust_id'];
    
    $custResult = null;


  if ($custStmt = $connect->prepare("CALL fetch_customer_info(?)")){
        $custStmt->bind_param('i', $cust_id);
        $custStmt->execute();
        $custResult = $custStmt->get_result();
        $custStmt->close();
    } else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
    }
    $cust   = mysqli_fetch_assoc($custResult);
?>
<div class="ui segment container items">
    <h2 class="ui dividing header">
    Editing Customer #<?php echo $cust_id; if(isset($cust['Username'])) echo " (".$cust['Username'].")"; ?>
    </h2>
    <form action='customer/editCustomer.php' method="POST" class="ui form">
    <input type="hidden" name="cust_id" value="<?php echo $cust_id; ?>">
        <div class="fields">
            <div class="three wide field">
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Username"
                       value=<?php if(isset($cust['Username'])) echo $cust['Username']; ?>>
            </div>
            <div class="three wide field">
                <label for="wechat_id">WeChatID</label>
                <input type="text" name="wechat_id" placeholder="WeChatID"
                       value=<?php if(isset($cust['WeChatID'])) echo $cust['WeChatID']; ?>>
            </div>
            <div class="ten wide field">
                <label for="comment">Comment</label>
                <textarea rows='3' name="comment"><?php if(isset($cust['Comment'])) echo $cust['Comment']; ?></textarea>
            </div>
        </div>
        <button type="submit" class="ui fluid orange button">Edit Customer</button>
    </form>
</div>