<?php
    include "connection.php";

    $sql = 
    "   UPDATE 
            offers.offers
        SET 
            offer_html = '".addslashes($_POST['offer_html'])."',
            offer_html_shared = '".addslashes($_POST['offer_html_shared'])."',
            offer_update_date = '".addslashes($_POST['offer_update_date'])."',
            offer_shared_date = '".addslashes($_POST['offer_shared_date'])."',
            offer_expiry_date = '".addslashes($_POST['offer_expiry_date'])."'
        WHERE 
            offer_id = '".$_POST['offer_id']."'
    "; 

    $now_date = new DateTime("now");
    $next_date = new DateTime($_POST['offer_expiry_date']);
    $diff = date_diff($now_date, $next_date); // difference between the dates and set a variable $diff
    $diff_in_days = $diff -> format('%r%a'); // https://www.php.net/manual/en/dateinterval.format.php
    
    $arr = array(
        'offer_update_date' => date("d.m.Y", strtotime($_POST['offer_update_date'])),
        'offer_link' => "https://quotationtool.org?id=" . $_POST['offer_id'],
        'days_dif' => $diff_in_days + 1,
    );

    if (mysqli_query($con, $sql)) {
        echo json_encode($arr);
    } else {
        echo "Error on creating initial offer: ".mysqli_error($con);
    }

	mysqli_close($con);
?>