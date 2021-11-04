<?php
    include "connection.php";
    
    function randomStr($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
   
    // save new offer 
    // new
    if ($_POST['saveMode'] == "new") {

        $offer_id = randomStr()."1";

        $sql = 
        "   INSERT IGNORE INTO
                offers.offers
                (
                    offer_id,
                    offer_ver,
                    offer_is_ver_latest,
                    offer_name,
                    offer_to,
                    offer_price_no_vat,
                    offer_html,
                    offer_comment,
                    offer_init_date,
                    offer_update_date,
                    offer_order_date,
                    user_id,
                    user_mail
                ) 
            VALUES
                (
                    '".$offer_id."',
                    1,
                    'yes',
                    '".$_POST['offer_name']."',
                    '".$_POST['offer_to']."',
                    '".$_POST['offer_price_no_vat']."',
                    '".addslashes($_POST['offer_html'])."',
                    '".$_POST['offer_comment']."',
                    '".$_POST['offer_init_date']."',
                    '".$_POST['offer_update_date']."',
                    '".$_POST['offer_order_date']."',
                    '".$_POST['user_id']."',
                    '".$_POST['user_mail']."'
                )
        "; //'".date_format(date_create($_POST['offer_update_date']), "Y-m-d")."',

        $arr = array(
            'offer_id' => $offer_id,
            'offer_ver' => 1,
            'offer_update_date' => date("d.m.Y", strtotime($_POST['offer_update_date']))
        );

        if (mysqli_query($con, $sql)) {
            echo json_encode($arr);
        } else {
            echo "Error on creating initial offer: ".mysqli_error($con);
        }

    }
    // update 
    else if ($_POST['saveMode'] == "update") {
        $sql = 
        "   UPDATE 
                offers.offers
            SET 
                offer_name = '".addslashes($_POST['offer_name'])."',
                offer_to = '".addslashes($_POST['offer_to'])."',
                offer_price_no_vat = '".addslashes($_POST['offer_price_no_vat'])."',
                offer_html = '".addslashes($_POST['offer_html'])."',
                offer_comment = '".addslashes($_POST['offer_comment'])."',
                offer_update_date = '".addslashes($_POST['offer_update_date'])."',
                offer_order_date = '".addslashes($_POST['offer_order_date'])."'
            WHERE 
                offer_id = '".$_POST['offer_id']."' AND 
                user_id = '".$_POST['user_id']."'
        "; 
        
        $arr = array(
            'offer_id' => $_POST['offer_id'],
            'offer_ver' => $_POST['offer_ver'],
            'offer_update_date' => date("d.m.Y", strtotime($_POST['offer_update_date']))
        );

        if (mysqli_query($con, $sql)) {
            echo json_encode($arr);
        } else {
            echo "Error on creating initial offer: ".mysqli_error($con);
        }

    } 
    // next
    else if ($_POST['saveMode'] == "next") {
        // init date from this offer
        $sql = 
        "   SELECT 
                offer_init_date
			FROM 
                offers.offers
			WHERE
                offer_id = '".$_POST['offer_id']."'
		";
	
        $offer_init_date = mysqli_fetch_assoc(mysqli_query($con, $sql))["offer_init_date"];

        // find latest version of this offer 
        $sql = 
        "   SELECT 
                offer_ver
			FROM 
                offers.offers
			WHERE
                LEFT(offer_id, 5) = LEFT('".$_POST['offer_id']."', 5) AND 
                offer_is_ver_latest = 'yes'
		";
        
        // next version number
        $offer_ver = mysqli_fetch_assoc(mysqli_query($con, $sql))["offer_ver"] + 1;

        // create new offer id for new offer
        $offer_id = substr($_POST['offer_id'], 0, 5).$offer_ver;

        // latest offer is not the latest anymore
        $sql = 
        "   UPDATE 
                offers.offers
            SET 
                offer_is_ver_latest = 'no'
            WHERE 
                LEFT(offer_id, 5) = LEFT('".$_POST['offer_id']."', 5) AND 
                offer_is_ver_latest = 'yes' AND
                user_id = '".$_POST['user_id']."'
        ";

        mysqli_query($con, $sql);

        // add new offer with some info from previous offer
        $sql = 
        "   INSERT IGNORE INTO
                offers.offers
                (
                    offer_id,
                    offer_ver,
                    offer_is_ver_latest,
                    offer_name,
                    offer_to,
                    offer_price_no_vat,
                    offer_html,
                    offer_comment,
                    offer_init_date,
                    offer_update_date,
                    offer_order_date,
                    user_id,
                    user_mail
                ) 
            VALUES
                (
                    '".$offer_id."',
                    '".$offer_ver."',
                    'yes',
                    '".addslashes($_POST['offer_name'])."',
                    '".addslashes($_POST['offer_to'])."',
                    '".addslashes($_POST['offer_price_no_vat'])."',
                    '".addslashes($_POST['offer_html'])."',
                    '".addslashes($_POST['offer_comment'])."',
                    '".$offer_init_date."',
                    '".addslashes($_POST['offer_update_date'])."',
                    '".addslashes($_POST['offer_order_date'])."',
                    '".addslashes($_POST['user_id'])."',
                    '".addslashes($_POST['user_mail'])."'
                )
        ";

        $arr = array(
            'offer_id' => $offer_id,
            'offer_ver' => $offer_ver,
            'offer_update_date' => date("d.m.Y", strtotime($_POST['offer_update_date'])),
            'offer_init_date' => $offer_init_date
        );

        if (mysqli_query($con, $sql)) {
            echo json_encode($arr);
        } else {
            echo "Error on creating initial offer: ".mysqli_error($con);
        }
    } 
    
	mysqli_close($con);
?>