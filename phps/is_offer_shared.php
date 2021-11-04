<?php
    include "connection.php";

    $offer_id = mysqli_real_escape_string($con, $_POST['offer_id']); //safer way of retriving data
        
    $query = 
    "   SELECT 
            offer_expiry_date 
        FROM 
            offers.offers 
        WHERE 
            offer_id = '".$_POST['offer_id']."'
    ";

    if (mysqli_num_rows(mysqli_query($con, $query)) > 0) {
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);

        $now_date = new DateTime("now");
        $next_date = new DateTime($row["offer_expiry_date"]);
        $diff = date_diff($now_date, $next_date); // difference between the dates and set a variable $diff
        $diff_in_days = $diff -> format('%r%a'); // https://www.php.net/manual/en/dateinterval.format.php

        if ($diff_in_days >= 0 ) {
            $arr = array(
                //'offer_expiry_date' => date("Y-m-d", strtotime($row["offer_expiry_date"])),
                'offer_expiry_date' => $row["offer_expiry_date"],
                'offer_link' => "http://quotationtool.org?id=".$_POST['offer_id'],
                'days_dif' => $diff_in_days + 1,
            );
            echo json_encode($arr);
        } else {
            echo false;
        }
    } else {
        echo false;
    }
   
	mysqli_close($con);
?>