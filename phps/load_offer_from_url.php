<?php
    include "connection.php";

    #ACTION: all exteral data should be done via "prepared statement"

    $offer_id = mysqli_real_escape_string($con, $_POST['offer_id']); //safer way of retriving data
    
    $query = 
    "   SELECT 
            *,
            DATEDIFF(offer_expiry_date, CURDATE()) AS daysToExpiry
        FROM 
            offers.offers 
        WHERE 
            offer_id = '".$offer_id."'
    ";

    if (mysqli_num_rows(mysqli_query($con, $query)) > 0) {
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);

        if ($_POST['user_id'] == $row['user_id']) {
            // give full version with costs if you are the owner
            $html = $row["offer_html"];
        } else {
            // you are not the owner, let's check if the offer is shared 
            if ($row["offer_shared_date"] == "0000-00-00 00:00:00") {
                echo "offer is not shared";
                mysqli_close($con);
                exit();
            } else {
                // offer is shared, but maybe it is already outdated
                // check if expiry date is in the future
                
                //$now_date = new DateTime("now");
                //$next_date = new DateTime($row["offer_expiry_date"]);
                //$diff = date_diff($now_date, $next_date); // difference between the dates and set a variable $diff
                //$diff_in_days = $diff -> format('%r%a'); // https://www.php.net/manual/en/dateinterval.format.php
                // if ($diff_in_days >= 0)

                if ($row["daysToExpiry"] >= 0) {
                    // give shared version without costs
                    $html = $row["offer_html_shared"];
                } else {
                    echo "expired";
                    mysqli_close($con);
                    exit();
                }
            }
        }

        $arr = array(
            'offer_html' => $html,
            'offer_ver' => $row["offer_ver"],
            'offer_name' => $row["offer_name"],
            'offer_to' => $row["offer_to"],
            'offer_price_no_vat' => $row["offer_price_no_vat"],
            'offer_comment' => $row["offer_comment"],
            'offer_order_date' => $row["offer_order_date"]
        );
        echo json_encode($arr);

    } else {
        echo 'no such offer';
    }	

	mysqli_close($con);
?>