<?php
    
	require 'PHPMailerAutoload.php';
	include 'db.php';
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim();
	$db = getDB();	
	$orderData1 = $db->query("SELECT order_id,mail_status,uid,membership_no,order_number,created FROM commerce_order where mail_status=1")->fetchAll(PDO::FETCH_OBJ);
	//print_r($orderData1);
	
	foreach($orderData1 as $order => $itemData) {
	$tableData = "";
	$orderTotal ="";
	//echo $orderData1[$order]->uid;
	$userData = user_load($orderData1[$order]->uid);
	ECHO "<pre>";
	//print_r($userData);
	echo $userData->mail;
	$memberData = member_load($orderData1[$order]->membership_no);
	//print_r($memberData);
	//echo $memberData->company_name;
	//echo $orderData1[$order]->order_id;
	$order_id = $orderData1[$order]->order_id;
	//echo "SELECT * commerce_line_item where order_id = ".$order_id;
	$line_item = $db->query("SELECT * FROM commerce_line_item cli JOIN item  on cli.line_item_id = item.item_no and  order_id = ".$order_id)->fetchAll(PDO::FETCH_OBJ);
	//print_r($line_item);
	 // Add Line items to the order
    foreach($line_item as $delta => $itemData) {
	$name = $line_item[$delta]->item1_desc.' '.$line_item[$delta]->item2_desc;
	$delt = $delta +1;
	$price = $line_item[$delta]->price_with_tax;
	$qty = $line_item[$delta]->quantity;
	$total_unit_price =$qty * $price;
	
     /* $tableData .= '<tr>';
      $tableData .= '<td style="text-align: center;">'.($delta + 1).'</td>';
      $tableData .= '<td>'.$lineItemData["items"][$itemData->item_no]["name"].'</td>';
      $tableData .= '<td style="text-align: center;">'.$itemData->qty.'</td>';
      $tableData .= '<td style="text-align: right;">'.$lineItemData["items"][$itemData->item_no]["price"].'</td>';
      $tableData .= '</tr>';*/
	  $orderTotal += $total_unit_price;
	  $tableData .= '<tr>';
      $tableData .= '<td style="text-align: center;">'.$delt.'</td>';
      $tableData .= '<td>'.$name.'</td>';
      $tableData .= '<td style="text-align: center;">'.$qty.'</td>';
      $tableData .= '<td style="text-align: right;">'.$total_unit_price.'</td>';
      $tableData .= '</tr>';
    }
  
	$message =  '<p>A new order has been placed. Please find the&nbsp;details below:</p>
                      <p>&nbsp;</p>

                      <p>Store Name:&nbsp;&nbsp;<strong>'.$memberData->company_name.'</strong></p>
                      <p>Order No: <strong>'.$orderData1[$order]->order_number.'</strong></p>
                      <p>Date: '.date("d/m/Y", $orderData1[$order]->created).'</p>


                      <table border="1" cellpadding="1" cellspacing="0" style="border:1px solid #CCCCCC; width:500px">
                      	<tbody>
                      		<tr>
                      			<td style="text-align: center;"><strong>No</strong></td>
                      			<td style="text-align: center;"><strong>Item Name</strong></td>
                      			<td style="text-align: center;"><strong>Quantity</strong></td>
                      			<td style="text-align: center;"><strong>Price (INR)</strong></td>
                      		</tr>';
    $message .= $tableData;
    $message .= ' 		<tr>
                      			<td colspan="3" style="text-align: right;"><strong>Order Total</strong></td>
                      			<td style="text-align: right;"><strong>'.$orderTotal.'</strong></td>
                      		</tr>
                      	</tbody>
                      </table>
                      <p>&nbsp;</p>
                      <p>Regards,</p>
                      <p>Walmart.</p>';

    $message1 = '<p>Dear Bala,</p>'.$message;
    $message2 = '<p>Dear Admin,</p>'.$message;

  // $mailStatus = sendMail("rahila.begam@calsoftlabs.com", "rahilabegum@gmail.com", "New Order is been created", $message1);
  //sendMail("rahila.begam@calsoftlabs.com", "rahila.begam@calsoftlabs.com", "New Order is been created", $message2);

  //  if(!$mailStatus) {
      //echo 'Not sent';
  // } else {
     //echo 'sent';
//	$updateOrderSql = "UPDATE commerce_order SET mail_status='2' WHERE order_id = ".$orderData1[$order]->order_id;
  //  $db = getDB();
 //   $stmt2 = $db->prepare($updateOrderSql);
 //   $stmt2->execute();
  //  }
	}
	
function user_load($userId) {
  $userData = array();
  $db = getDB();
  $userData = $db->query("SELECT * FROM users WHERE uid = ".$userId)->fetch(PDO::FETCH_OBJ);
  return $userData;
}
function member_load($memberId) {
  $memberData = array();
  $db = getDB();
  $memberData = $db->query("SELECT * FROM membership WHERE membership_no = ".$memberId)->fetch(PDO::FETCH_OBJ);
  return $memberData;
}

function sendMail($from, $to, $subject, $body) {
  $mail = new PHPMailer;

  // $mail->SMTPDebug = 3;                               // Enable verbose debug output

  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'drupal.syb@gmail.com';                 // SMTP username
  $mail->Password = 'Reset!23';                           // SMTP password
  $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 465;                                    // TCP port to connect to

  $mail->From = 'rahila.begam@calsoftlabs.com';
  $mail->FromName = 'Rahila J';
  $mail->addAddress($to);     // Add a recipient

  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = $subject;
  $mail->Body    = $body;
  return $mail->send();
}
?>