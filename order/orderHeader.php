<?php
    require '../header.php';
?>

<div class="ui container segment center aligned">
    <a class="ui big blue button" href="order/viewOrders.php?queryType=-1">All Orders</a>
    <a class="ui big blue button" href="order/viewOrders.php?queryType=0">Unfulfilled Orders</a>
    <a class="ui big blue button" href="order/viewOrders.php?queryType=1">Fulfilled Orders</a>
    <a class="ui big green button" href="order/newOrderForm.php?">Enter New Order</a>
</div>
