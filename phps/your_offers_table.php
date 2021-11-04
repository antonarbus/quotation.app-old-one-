<?php
	include "connection.php";	

	$user_id = mysqli_real_escape_string($con, $_POST['user_id']); //safer way of retriving data
	$offer_id = $_POST['offer_id'];

	$sql = 
	"	SELECT 
			*,
			DATEDIFF(CURDATE(), offer_init_date) AS daysFromCreation,
			DATEDIFF(CURDATE(), offer_update_date) AS daysFromUpdate,
			DATEDIFF(offer_order_date, CURDATE()) AS daysToOrder,
			DATEDIFF(CURDATE(), offer_shared_date) AS daysFromShare,
			DATEDIFF(offer_expiry_date, CURDATE()) AS daysToExpiry
		FROM 
			offers.offers
		WHERE 
			user_id = '".$user_id."' AND offer_is_ver_latest = 'yes'
		ORDER BY 
			offer_update_date DESC
	";

	if (mysqli_query($con, $sql)) {
		$result = mysqli_query($con, $sql);
	
		if (mysqli_num_rows($result) > 0) {
			$num = 0;
			while($row = mysqli_fetch_assoc($result)) {	
?>	
				<tr
					<?php
						// highlight active offer
						if (substr($offer_id, 0, 5) == substr($row["offer_id"], 0, 5)) echo "class='current-offer-tr'";
					?>
				>
					<td class="row-num"><span><?php echo ++$num;?></span></td>
					<td class="offer-open"><a href="https://quotationtool.org/?id=<?php echo $row["offer_id"];?>">Open</a></td>
					<td class="offer-id"><?php echo substr($row['offer_id'], 0, 5);?></td>
					<td class="offer-name"><?php echo $row["offer_name"];?></td>
					<td class="offer-to"><?php echo $row["offer_to"];?></td>
					<td class="offer-from"><?php echo $row["user_mail"];?></td>
					<td class="offer-ver"><?php echo $row["offer_ver"];?></td>
					<td class="offer-price"><?php echo $row["offer_price_no_vat"];?></td>
					<td class="offer-comment"><?php echo $row["offer_comment"];?></td>
					<td class="offer-init-date">
						<div>
							<?php 
								echo date("d.m.Y", strtotime($row["offer_init_date"]));
							?>
						</div>
						<div class="small-grey">
							<?php 
								echo $row["daysFromCreation"]." days ago";
							?>
						</div>
					</td>
					<td class="offer-update-date">
						<div>
							<?php 
								echo date("d.m.Y", strtotime($row["offer_update_date"]));
							?>
						</div>
						<div class="small-grey">
							<?php 
								echo $row["daysFromUpdate"]." days ago";
							?>
						</div>
					</td>
					<td class="offer-order-date">
						<div>
							<?php 
								echo date("d.m.Y", strtotime($row["offer_order_date"]));
							?>
						</div>
						<div class="small-grey">
							<?php
								if ($row['daysToOrder'] < 0) {
									echo "check status";
								} else {
									echo "expect in ".$row["daysToOrder"]." days";
								}
							?>
						</div>
					</td>
					<td class="offer-shared-date">
						<div>
							<?php
								if ($row['offer_shared_date'] != '0000-00-00 00:00:00') {
									echo date("d.m.Y", strtotime($row["offer_shared_date"]));
								} else {
									echo "share";
								}
							?>
						</div>
						<div class="small-grey">
							<?php
								if ($row['offer_shared_date'] != '0000-00-00 00:00:00') {
									echo $row["daysFromShare"]." days ago";
								}
							?>
						</div>
					</td>
					<td class="offer-expiry-date">
						<div>
							<?php
								if ($row['offer_expiry_date'] != '0000-00-00') {
									echo date("d.m.Y", strtotime($row["offer_expiry_date"]));
								}
							?>
						</div>
						<div class="small-grey">
							<?php
								if ($row['offer_expiry_date'] != '0000-00-00') {
									if ($row['daysToExpiry'] < 0) {
										echo "expired";
									} else {
										echo "in ".$row["daysToExpiry"]." days";
									}
								}
							?>
						</div>
					</td>
					<td class="offer-delete"><span class="red-hover">⨉</span></td>
				</tr>
<?php	
			}
		} 
	} 
	else {
		echo "Error on search: " . mysqli_error($con);
	}


	mysqli_close($con);	
?>