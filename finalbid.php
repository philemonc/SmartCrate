<!DOCTYPE html>
<html>
	<header>
		<title>Confirmed Bids</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/biddingpage.css">
		<style>
			h1 {color: #6495ed;
				font-family: Segoe UI Light;
				display: inline;}
		</style>
	</header>
	<body>
		<?php 
			include_once 'includes/dbconnect.php';
			$dbconn = pg_connect($connection) or die('Could not connect: ' . pg_last_error());
			session_start();
			
			$bidd = $_SESSION['bidarray'];
			$chk =	$_SESSION['finbid'];
			$idtobids = array_combine($chk, $bidd);
			$email = $_SESSION['email'];			

			echo ' <div class="container">
				   <div class="row">

			       <section class="content">
			        <div class="row" align="center">
			       <h1><b>Confirm your bids</b></h1>
			       </div>
			       <br>

			       <div class="container">
					<div class="row">		
			       <div class="col-md-8 col-md-offset-2">
				   <div class="panel panel-default">
					  <div class="panel-body">
						<div class="table-container">
						<table class="table table-filter">
						<tbody>';
			
			echo '<form id="confirmbid-form" action="processbid.php" method="post" role="form" style="display: block;">';
			
			//key is the itemid, value is the bid
			foreach ($idtobids as $key => $value) {
	        
	        //email of person bidding
	        $fetchmember = "SELECT m.name FROM member m WHERE m.email = '$email'";
	        $member = pg_query($fetchmember);
	        $rowmember = pg_fetch_assoc($member);
	        $name = $rowmember['name'];
	        
	        
	        //item person is bidding
	        $fetchitem = "SELECT i.itemname, i.itemid FROM item i WHERE i.itemid = '$key'";
	        $item = pg_query($fetchitem);
	        $rowitem = pg_fetch_assoc($item);
	        $itemname = $rowitem['itemname'];
	        $itemid = $rowitem['itemid'];

	        $updatequery = "INSERT INTO bidding VALUES ('$name', '$email', '$value' , '$itemid', '$itemname', now(), '0', '1')";	     
	        $update = pg_query($updatequery);
	       
	        
	        $query = "SELECT i.feeflag, b.feeamount, i.itemname, i.availabledate, i.description, i.type 
	        FROM item i, member m, bidding b 
	        WHERE i.itemid = '$key' AND i.itemid = b.itemid AND m.email = '$email' AND b.email = m.email"; 
	        $result = pg_query($query); 
			//fetch all selected items
			while ($row = pg_fetch_assoc($result)) {
				$msg = '';
				if ($row["feeflag"] == 0) {
					$msg = 'Free!';
				} else {
					$msg = $row["feeamount"];
				}
				echo '<tr data-status="'.$row["type"].'">
										<td>
											<p><b>Your Bid: '.$msg.'</b></p>
										</td>
										<td>
											<a href="javascript:;" class="star">
												<i class="glyphicon glyphicon-star"></i>
											</a>
										</td>
										<td>
											<div class="media">
												<a href="#" class="pull-left">
													<img src="https://s3.amazonaws.com/uifaces/faces/twitter/fffabs/128.jpg" class="media-photo">
												</a>';	
				echo '<div class="media-body"><span class="media-meta pull-right">'.$row["availabledate"].'</span>';
				echo '<h4 class="title">'.$row["itemname"].'<span class="pull-right '.$row["type"].'">('.$row["type"].')</span></h4>';
				echo '<p class="summary">'.$row["description"].'</p></div></div></td></tr>';	
				}
			} 
			pg_free_result($result);
			echo '</tbody></table>';
		?>
				</form>
				</div>
				</div>
				</div>
				<div class="text-left">
					<p>	
						<a href="retrieveinfo.php" class="btn btn-primary" role="button">Your Shared Items</a>
						<a href="borrowed.php" class="btn btn-primary" role="button">Your Borrowed Items</a>
						<a href="viewbids.php" class="btn btn-success" role="button">View Bids</a>
						<a href="logout.php" class="btn btn-danger" role="button">Logout</a>
					</p>
				</div>
			</div>
		</section>
	</div>
</div>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
	<script type="text/javascript" src="js/biddingpage.js"></script>
	</body>	
</html>

