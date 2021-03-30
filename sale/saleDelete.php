<?php 
    require 'saleHeader.php';
    $report_id =$_POST['delete'];
  

if ($detailDeleteStmt = $connect->prepare("DELETE FROM `Sales_Report_Details`
                                                WHERE Sales_Report_ID=?")) {
        $detailDeleteStmt->bind_param("i",$report_id);
        $detailDeleteStmt->execute();
        $detailDeleteStmt->close();  
     }
     else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }


    if ($reportDeleteStmt = $connect->prepare("DELETE FROM `Sales_Report`
                                                WHERE ID=?")) {
        $reportDeleteStmt->bind_param("i",$report_id);
        $reportDeleteStmt->execute();
        $reportDeleteStmt->close();  
     }
     else {
        $error = $connect->errno . ' ' . $connect->error;
        echo $error;
     }

     header("Location: viewReport.php");