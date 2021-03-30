<?php 
    include_once 'db.php';
?>

<!DOCTYPE html>
<html>
    <head>
    <base href="http://localhost/Bakery/" target="_self">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Bakery App</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/app.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" integrity="sha512-8bHTC73gkZ7rZ7vpqUQThUDhqcNFyYi2xgDgPDHc+GXVGHXq+xPjynxIopALmOPqzo9JZj0k6OqqewdGO3EsrQ==" crossorigin="anonymous" />
        <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js" integrity="sha512-dqw6X88iGgZlTsONxZK9ePmJEFrmHwpuMrsUChjAw1mRUhUITE5QU9pkcSox+ynfLhL15Sv2al5A0LVyDCmtUw==" crossorigin="anonymous"></script>
    </head>
<body style="background-color: #A8D0E6;">
<div class="ui container">
    <div class="ui stackable fluid five item menu">
        <a class="item" href="order/viewOrders.php?queryType=-1"><h3>Orders</h3></a>
        <a class="item" href="customer/viewCustomers.php"><h3>Customers</h3></a>
        <a class="item" href="product/viewProducts.php"><h3>Product Management</h3></a>
        <a class="item" href="profit/viewTotalProfit.php"><h3>Profit Analysis</h3></a>
        <a class="item" href="sale/viewReport.php"><h3>Sales Reports</h3></a>
    </div>
</div>
