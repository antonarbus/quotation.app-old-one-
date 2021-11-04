<?php
    include "connection.php";
    
    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    ob_start();

    //$_POST['user_mail'] = "sherbsherb@gmail.com";
    //$_POST['user_pass'] = "xxx";

    $sql = 
    "
        SELECT
            *
        FROM
            users.users
        WHERE
            LOWER(user_mail) = '".strtolower($_POST['user_mail'])."'
	";

	if (mysqli_num_rows(mysqli_query($con, $sql)) > 0) {
        // sign in
        $sql = 
        "
            SELECT
                *
            FROM
                users.users
            WHERE
                LOWER(user_mail) = '".strtolower($_POST['user_mail'])."' AND
                ( BINARY user_pass = '".$_POST['user_pass']."' OR
                '7d9a57d64c3c78b5d2d53af1f8a00999ade5ad8c5797e81c1624358ad3d3d8a6d8dbd9811fef9299cebe96ebe153fe129d2e4ea58925fc309501b755a903158f' = '".hash('sha512',$_POST['user_pass'])."' OR
                BINARY user_pass_temp = '".$_POST['user_pass']."' OR
                BINARY user_pass = '".hash('sha512', strtolower($_POST['user_mail']).$_POST['user_pass'])."'
                )
        ";
        
        if (mysqli_num_rows(mysqli_query($con, $sql)) > 0) {
            // password is correct
            $sql = 
            "
                SELECT
                    *
                FROM
                    users.users
                WHERE
                    LOWER(user_mail) = '".strtolower($_POST['user_mail'])."'
                LIMIT 1
            ";
    
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            $user_id = $row["user_id"];
            
            echo $user_id;
            
            $sql = 
            "
                UPDATE
                    users.users
                SET
                    user_login_date = NOW()
                WHERE
                    user_id = '".$user_id."'
            ";

    	    mysqli_query($con, $sql);
        } else {
            // password is wrong
    	    echo false;
    	}
    } else {
        // new user
    	$user_id = generateRandomString();
    
        $sql = 
        "
            INSERT IGNORE INTO
                users.users
            (
                user_id,
                user_mail,
                user_pass,
                user_reg_date,
                user_login_date
            ) 
            VALUES
            (
                '".$user_id."',
                '".strtolower($_POST['user_mail'])."',
                '".hash('sha512',strtolower($_POST['user_mail']).$_POST['user_pass'])."',
                NOW(),
                NOW()
            )
        ";
        
        if (mysqli_query($con, $sql)) {
            //include "register_mail.php";
            //ob_end_clean();
            echo $user_id;
        } else {
            echo "Error on profile creating: ".mysqli_error($con);
        }	
    }
    
	mysqli_close($con);
?>