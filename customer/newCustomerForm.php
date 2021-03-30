<?php
    require 'customerHeader.php';
?>

<div class="ui segment container items">
    <h2 class="ui dividing header">Entering New Customer</h2>
    <form action='customer/newCustomer.php' method="POST" class="ui form">
        <div class="fields">
            <div class="three wide field">
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Username">
            </div>
            <div class="three wide field">
                <label for="wechat_id">WeChatID</label>
                <input type="text" name="wechat_id" placeholder="WeChatID">
            </div>
            <div class="ten wide field">
                <label for="comment">Comment</label>
                <textarea rows='3' name="comment"></textarea>
            </div>
        </div>
        <button type="submit" class="ui fluid green button">Submit New Customer</button>
    </form>
</div>