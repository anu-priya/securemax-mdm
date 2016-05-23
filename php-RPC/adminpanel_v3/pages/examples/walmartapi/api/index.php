<?php
include 'db.php';
require 'Slim/Slim.php';
require 'PHPMailerAutoload.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//$app->add(new \Slim\Extras\Middleware\HttpBasicAuth('walmart', 'wal@123'));

$app->get('/users','getUsers');
$app->post('/user/create','createUser');
$app->post('/user/authenticate','authenticateUser');
$app->post('/user/add/category','userAddCategory');

$app->get('/updates','getUserUpdates');
$app->post('/updates', 'insertUpdate');
$app->delete('/updates/delete/:update_id','deleteUpdate');
$app->get('/users/search/:query','getUserSearch');


$app->get('/category','getCategory');
$app->get('/category/parent','getParentCategory');
$app->get('/category/child/:parent_id','getChildCategory');
$app->get('/category/parent/search/:query','parentCategorySearch');
$app->get('/category/child/search/:query','childCategorySearch');

$app->post('/category/data','getCategoryData');
$app->post('/product/data','getProductData');
$app->post('/sync/data','checkDataForSync');
$app->get('/product/list','getProductList');

//category 
$app->get('/category/:parent_id','getCategoryByid');
$app->get('/subcategory/:subcat_id','getSubcategoryByid');

/*Order Creation*/
$app->post('/order/create','createOrder');
$app->post('/store/list','getStoreList');
$app->post('/order/list','getOrderList');
$app->get('/order/order_list','getAllOrderList');
$app->get('/order/countorder_list','getCountOrderList');
$app->post('/order/update','updateOrder');
//$app->post('/order/delete','deleteOrder');

$app->get('/product/lists','getProductLists');
$app->get('/product/monthlysales','getMonthlysales');

$app->run();

/* Order API */
function createOrder() {
  $orderID = 0;
  $orderTotal = 0;
  $tableData = '';
  $lineItemData = array();
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  // echo "<pre>"; print_r($input); echo "</pre>"; exit;
  $uid = $input->order->uid;
  $line_item = $input->order->line_item;
  $member_no = $input->order->member_no;
  $payment_mode = $input->order->payment_mode;
  $delivery_mode = $input->order->delivery_mode;

  $orderQuery = "SELECT order_id, status FROM commerce_order WHERE uid = ". $uid." AND status = 'Processing' AND membership_no = ".$member_no;
  try {
    $db = getDB();
    $stmt = $db->query($orderQuery);
    $orderData = $stmt->fetchAll(PDO::FETCH_OBJ);
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
  // echo "<pre>"; print_r($orderData); echo "</pre>"; exit;


  if(empty($orderData)) {
    // Create an order
    $insertSql = "INSERT INTO commerce_order (order_number, uid, membership_no, status, created, changed, data) VALUES (:order_number, :uid, :membership_no, :status, :created, :changed,:data)";
    try {
      $db = getDB();
      $stmt1 = $db->prepare($insertSql);
      $max = $db->query("SELECT MAX( order_number ) as order_no FROM commerce_order")->fetch(PDO::FETCH_OBJ);
      $auto_inc = (int) $max->order_no + 1;
      $status = "Processing";
      $data = "";
      $stmt1->bindParam("order_number", $auto_inc);
      $stmt1->bindParam("uid", $input->order->uid);
      $time=time();
      $stmt1->bindParam("membership_no", $member_no);
      $stmt1->bindParam("status", $status);
      $stmt1->bindParam("created", $time);
      $stmt1->bindParam("changed", $time);
      $stmt1->bindParam("data", $data);
      $stmt1->execute();
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
      die();
    }
    $orderID = $db->lastInsertId();

    // Add Line items to the order
    foreach($line_item as $delta => $itemData) {
      $lineItemData['items'][$itemData->item_no] = createLineItem($orderID, $delta, $itemData->item_no, $itemData->qty);
      $orderTotal += $lineItemData['items'][$itemData->item_no]['price'];
      $tableData .= '<tr>';
      $tableData .= '<td style="text-align: center;">'.($delta + 1).'</td>';
      $tableData .= '<td>'.$lineItemData["items"][$itemData->item_no]["name"].'</td>';
      $tableData .= '<td style="text-align: center;">'.$itemData->qty.'</td>';
      $tableData .= '<td style="text-align: right;">'.$lineItemData["items"][$itemData->item_no]["price"].'</td>';
      $tableData .= '</tr>';
    }
  } else {
    // Create order line item
    $orderID = $orderData[0]->order_id;

    // Add Line items to the order
    foreach($line_item as $delta => $itemData) {
      $lineItemData['items'][$itemData->item_no] = createLineItem($orderID, $delta, $itemData->item_no, $itemData->qty);
      $orderTotal += $lineItemData['items'][$itemData->item_no]['price'];
      $tableData .= '<tr>';
      $tableData .= '<td style="text-align: center;">'.($delta + 1).'</td>';
      $tableData .= '<td>'.$lineItemData["items"][$itemData->item_no]["name"].'</td>';
      $tableData .= '<td style="text-align: center;">'.$itemData->qty.'</td>';
      $tableData .= '<td style="text-align: right;">'.$lineItemData["items"][$itemData->item_no]["price"].'</td>';
      $tableData .= '</tr>';
    }
  }
  $lineItemData['order_total'] = $orderTotal;
  $lineItemData['order_id'] = $orderID;

  $orderData1 = order_load($orderID);
  $lineItemData['order_number'] = (int) $orderData1->order_number;
  //echo "<pre>"; print_r($lineItemData); echo "</pre>"; exit;
  if(!empty($lineItemData)) {
    // Update the order data
    $orderUpdated = time();
    $updateOrderSql = "UPDATE commerce_order SET status='confirmed', changed = ".$orderUpdated.", data= ".$orderTotal." WHERE order_id = ".$orderID;
    $db = getDB();
    $stmt2 = $db->prepare($updateOrderSql);
    $stmt2->execute();

    // Insert order payment details
    $insertPaymentSql = "INSERT INTO commerce_payment (order_id, membership_no, payment_mode, delivery_mode) VALUES (:order_id, :membership_no, :payment_mode, :delivery_mode)";
    $db = getDB();
    $stmt3 = $db->prepare($insertPaymentSql);
    $stmt3->bindParam("order_id", $orderID);
    $stmt3->bindParam("membership_no", $member_no);
    $stmt3->bindParam("payment_mode", $payment_mode);
    $stmt3->bindParam("delivery_mode", $delivery_mode);
    $stmt3->execute();

    // Send Mail
    $userData = user_load($orderData1->uid);
    $memberData = member_load($orderData1->membership_no);
	$updateMailStatus = "UPDATE commerce_order SET mail_status=1 where uid=".$orderData1->uid;
    $db = getDB();
    $stmtm = $db->prepare($updateMailStatus);
    $stmtm->execute();
    /*$message =  '<p>A new order has been placed. Please find the&nbsp;details below:</p>
                      <p>&nbsp;</p>

                      <p>Store Name:&nbsp;&nbsp;<strong>'.$memberData->company_name.'</strong></p>
                      <p>Order No: <strong>'.$orderData1->order_number.'</strong></p>
                      <p>Date: '.date("d/m/Y", $orderData1->created).'</p>

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

    $mailStatus = sendMail("arifn@calsoftlabs.com", $userData->mail, "New Order is been created", $message1);
    sendMail("arifn@calsoftlabs.com", "arifn@calsoftlabs.com", "New Order is been created", $message2);

    if(!$mailStatus) {
      echo '{"success":{"text": "order updated", "mail":"not sent", "data": '.json_encode($lineItemData).' }}';
    } else {
      echo '{"success":{"text": "order updated", "mail":"sent", "data": '.json_encode($lineItemData).' }}';
    }*/
  } else {
    echo '{"error":{"text": "Some error occurred. Add again."}}';
  }
}
function getMonthlysales() {
	$Query = "SELECT bdas.plan_value,bdas.bda_id,usr.uid,co.order_number,cli.quantity,cli.price_with_tax FROM bda_sales bdas join users usr on bdas.bda_id = usr.bda_id join commerce_order co on usr.uid = co.uid join commerce_line_item cli on cli.order_id= co.order_number group by bdas.bda_id";
	$lineItem = array();
	try {
    $db = getDB();
    $stmt = $db->query($Query);
    $sData = $stmt->fetchAll(PDO::FETCH_OBJ);
    if(!empty($sData)) {
	//print_r($sData);
	
	$res = 0;
    foreach($sData as $key => $val) {
	  $percentage = (($val->quantity * $val->price_with_tax) / $sData[$key]->plan_value) *100; 
	  if($res <  $percentage) {
			$data[$key]['planvalue'] = $sData[$key]->plan_value;
			$data[$key]['quantity'] = $val->quantity;
			$data[$key]['price_with_tax'] = $val->price_with_tax;
			$data[$key]['percentage'] = (($val->quantity * $val->price_with_tax) / $sData[$key]->plan_value) *100;
            echo '{"responce": ' . json_encode($data) . '} ';
			die;
			}
    }
	} 
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
}

/* Order Line Item Creation Start */
function createLineItem($order_id, $delta, $item_no, $qty){
  $lineItem = array();
  $stock = 0;
  $price = 0;
  // Check the stock and price before creating the lineitem
  $stockQuery = "SELECT * FROM item i inner join inventory inv ON (i.item_no = inv.item_no) WHERE i.item_no = ".$item_no;
  try {
    $db = getDB();
    $stmt = $db->query($stockQuery);
    $stockData = $stmt->fetchAll(PDO::FETCH_OBJ);
    if(!empty($stockData)) {
      $stock = $stockData[0]->on_hand_qty;
      $price = $stockData[0]->price_with_tax;
      $itemName = $stockData[0]->item1_desc.' '.$stockData[0]->item2_desc;
    } /*else {
    echo '{"error":{"text":"invalid item"}}';
    die();
    }*/
    // echo "<pre>"; print_r($stockData); echo "</pre>"; exit;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }

  /*
   if($stock == 0) {
   echo '{"error":{"text":"no stock found"}}';
   die();
   } else {
   if($stock >= $qty) {
   if($price == 0) {
   echo '{"error":{"text":"price data not found"}}';
   die();
   } else {
   $lineItemPrice = $qty * $price;

   // Create a line item
   $insertLineItem = "INSERT INTO commerce_line_item (order_id, line_item_label, delta, quantity, created, changed, data) VALUES (:order_id, :line_item_label, :delta, :quantity, :created, :changed,:data)";
   try {
   $db = getDB();
   $stmt = $db->prepare($insertLineItem);
   $stmt->bindParam("order_id", $order_id);
   $stmt->bindParam("line_item_label", $item_no);
   $time = time();
   $data = "";
   $stmt->bindParam("delta", $delta);
   $stmt->bindParam("quantity", $qty);
   $stmt->bindParam("created", $time);
   $stmt->bindParam("changed", $time);
   $stmt->bindParam("data", $data);
   $stmt->execute();
   $lineItem['updated'] = $time;
   $lineItem['price'] = $lineItemPrice;
   } catch(PDOException $e) {
   echo '{"error":{"text":'. $e->getMessage() .'}}';
   die();
   }
   }
   }
   }
   */

  $lineItemPrice = $qty * $price;

  // Create a line item
  $insertLineItem = "INSERT INTO commerce_line_item (order_id, line_item_label, delta, quantity, price_with_tax, created, changed, data) VALUES (:order_id, :line_item_label, :delta, :quantity, :price, :created, :changed,:data)";
  try {
    $db = getDB();
    $stmt = $db->prepare($insertLineItem);
    $stmt->bindParam("order_id", $order_id);
    $stmt->bindParam("line_item_label", $item_no);
    $time = time();
    $data = "";
    $stmt->bindParam("price", $lineItemPrice);
    $stmt->bindParam("delta", $delta);
    $stmt->bindParam("quantity", $qty);
    $stmt->bindParam("created", $time);
    $stmt->bindParam("changed", $time);
    $stmt->bindParam("data", $data);
    $stmt->execute();
    $lineItem['updated'] = $time;
    $lineItem['price'] = $lineItemPrice;
    $lineItem['name'] = $itemName;

    // Update the stock count in Inventory
    $updatedStock = $stock - $qty;
    updateStockCount($item_no, $updatedStock);
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
  return $lineItem;
}

function updateOrder() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $order_id = $input->order->order_id;
  $status = $input->order->status;

  $updateOrderSql = "UPDATE commerce_order SET changed = ".time().", status = '".$status."' WHERE order_id = ".$order_id;
  try {
    $db = getDB();
    $stmt2 = $db->prepare($updateOrderSql);
    $updateStatus = $stmt2->execute();
    echo '{"success":{"text": "Order updated."}}';
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
}

function getStoreList() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;

  try {
    $userSql = "SELECT users.uid,
                       users.bda_id,
                       membership.membership_no,
                       membership.company_name,
                       membership.first_name,
                       membership.last_name,
                       CONCAT_WS(',', membership.address_line_1, membership.address_line_2, membership.city_name, membership.state_prov_code, membership.county_name, membership.postal_code) as address,
                       membership.phone_no,
                       membership.coordinates
                  FROM users users
                       INNER JOIN membership membership
                          ON (users.bda_id = membership.bda_id) WHERE membership.coordinates != '' AND uid = ".$uid;
    $db = getDB();
    $stmt = $db->query($userSql);
    $userData = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(empty($userData)) {
      echo '{"error":{"text": "Invalid User or data not found"}}';
    } else {
      echo '{"stores": '.json_encode($userData).'}';
    }
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getAllOrderList() {
  try {
    $orderSql = "SELECT users.uid,users.name,
                     commerce_order.order_id,
                     commerce_order.order_number,
                     commerce_order.created,
                     commerce_order.membership_no,
                     membership.company_name
                   FROM ((commerce_order commerce_order
                      INNER JOIN membership membership
                        ON (commerce_order.membership_no = membership.membership_no))
                      INNER JOIN users users
                        ON (users.uid = commerce_order.uid))
                      INNER JOIN commerce_line_item commerce_line_item
                        ON (commerce_order.order_id = commerce_line_item.order_id)
                        GROUP BY commerce_line_item.order_id";
    $db = getDB();
    $stmt = $db->query($orderSql);
	$orderData = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(empty($orderData)) {
      echo '{"error":{"text": "Invalid User or data not found"}}';
    } else {
       $json = '{"orders": '.json_encode($orderData).'}';
	   echo (cryptoJsAesEncrypt('pass',$json));
    }
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getCountOrderList() {
  try {
    $orderSql = "SELECT users.uid,users.name,
                     commerce_order.order_id,
                     commerce_order.order_number,
                     commerce_order.created,
                     commerce_order.membership_no,
                     membership.company_name
                   FROM ((commerce_order commerce_order
                      INNER JOIN membership membership
                        ON (commerce_order.membership_no = membership.membership_no))
                      INNER JOIN users users
                        ON (users.uid = commerce_order.uid))
                      INNER JOIN commerce_line_item commerce_line_item
                        ON (commerce_order.order_id = commerce_line_item.order_id)
                        GROUP BY commerce_line_item.order_id";
    $db = getDB();
    $stmt = $db->query($orderSql);
	$orderData = $stmt->fetchAll(PDO::FETCH_OBJ);
	$num_rows = count($orderData);
    if(empty($num_rows)) {
      echo '{"error":{"text": "Invalid User or data not found"}}';
    } else {
      echo $json = '{"orders_count": '.json_encode($num_rows).'}';
	//  echo (cryptoJsAesEncrypt('pass',$json));
    }
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getOrderList() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;

  try {
    $orderSql = "SELECT users.uid,
                     commerce_order.order_id,
                     commerce_order.order_number,
                     sum(commerce_line_item.quantity * commerce_line_item.price_with_tax) as total,
                     commerce_order.created,
                     commerce_order.membership_no,
                     membership.company_name
                   FROM ((commerce_order commerce_order
                      INNER JOIN membership membership
                        ON (commerce_order.membership_no = membership.membership_no))
                      INNER JOIN users users
                        ON (users.uid = commerce_order.uid))
                      INNER JOIN commerce_line_item commerce_line_item
                        ON (commerce_order.order_id = commerce_line_item.order_id)
                        WHERE users.uid = ".$uid." GROUP BY commerce_line_item.order_id";
    $db = getDB();
    $stmt = $db->query($orderSql);
    $orderData = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(empty($orderData)) {
      echo '{"error":{"text": "Invalid User or data not found"}}';
    } else {
      echo '{"orders": '.json_encode($orderData).'}';
    }
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
/* Order Line Item Creation End */
/* Order API END */



/*** USER API ***/
function createUser() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $sql = "INSERT INTO users (bda_id, name, pass, mail, created, access, login, status, timezone, language, init) VALUES (:bda_id, :name, :pass, :mail, :created, :access, :login, :status, :timezone, :language, :init)";
  try {
    $status = 1;
    $access = 0;
    $login = 0;
    $timezone = 'Asia/Kolkata';
    $language = 'en';
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("bda_id", $input->user->bda_id);
    $stmt->bindParam("name", strtolower($input->user->name));
    $stmt->bindParam("pass", md5($input->user->pass));
    $stmt->bindParam("mail", $input->user->mail);
    $time=time();
    $stmt->bindParam("created", $time);
    $stmt->bindParam("access", $access);
    $stmt->bindParam("login", $login);
    $stmt->bindParam("status", $status);
    $stmt->bindParam("timezone", $timezone);
    $stmt->bindParam("language", $language);
    $stmt->bindParam("init", $input->user->mail);
    $stmt->execute();
    $message = 'User created.';
    echo '{"success":{"text":'. $message .'}}';
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function userAddCategory() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;
  try {
    $userSql = "SELECT * FROM users WHERE uid = ".$uid;
    $db = getDB();
    $stmt = $db->query($userSql);
    $userData = $stmt->fetchAll(PDO::FETCH_OBJ);
    if(empty($userData)) {
      echo '{"error":{"text": "Invalid User"}}';
    } else {
      $cid = $input->user->cid;
      $insertStr = '';
      $time = time();
      foreach ($cid as $key => $val) {
        $insertStr .= '('.$uid.', '.$val.' , '.$time.'), ';
      }
      $insertStr = trim($insertStr, ", ");

      $deleteSql = "DELETE FROM user_category WHERE uid = ".$uid;
      $stmt = $db->prepare($deleteSql);
      $stmt->execute();

      $insertSql = "INSERT INTO user_category (uid, cid, updated) VALUES ".$insertStr;
      $stmt = $db->prepare($insertSql);
      $stmt->execute();
      $message = 'Categories added to User.';
      echo '{"success":{"text":'. $message .'}}';
    }
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function authenticateUser() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  // echo "<pre>"; print_r($input); echo "</pre>"; exit;

  if (empty($input->user->id) OR empty($input->user->pass)) {
    echo '{"error":{"text":"Invalid input parameters"}}';
    die();
  }

  // Check with DB
  $sql = "SELECT uid, name, mail FROM users WHERE (name = '".$input->user->id."' OR mail = '".$input->user->id."') AND pass = '".md5($input->user->pass)."'";
  try {
    $db = getDB();
    $stmt = $db->query($sql);
    $user = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(!empty($user)) {
      echo '{"response": {"name": "'.$user[0]->name.'","mail": "'.$user[0]->mail.'","status": "success","userid": '.$user[0]->uid.'}}';
    } else {
      echo '{"error":{"text":"Invalid user or wrong password"}}';
    }
    $db = null;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

/*** USER API END ***/

/*** PRODUCT API ***/
function getProductList(){
  $sql = "SELECT item.item_no,
               item.item1_desc,
               item.item2_desc,
               item.max_retail_amt,
               item.price_with_tax,
               item.unit_retail_amt,
               item.tax_pct,
               item.tax_calculation
          FROM wallmart.item item";
  try {
    $db = getDB();
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
    echo count($categories).'<br>';
    $i = 1;
    foreach($categories as $key => $val) {
      // $data[$categories[$key]->item_no] = $val;
      $data[$categories[$key]->item_no]['item_no'] = $val->item_no;
      $data[$categories[$key]->item_no]['item1_desc'] = utf8_encode($val->item1_desc);
      $data[$categories[$key]->item_no]['item2_desc'] = utf8_encode($val->item2_desc);
    }
    echo '{"category": ' . json_encode($data) . '} ';
    // echo "<pre>"; print_r($data); echo "</pre>"; exit;
    $db = null;
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getCategoryByid($id) {
  $sql = "select c.*,ic.subclass_no,i.item1_desc,i.item2_desc,i.unit_retail_amt,i.max_retail_amt,i.tax_pct,i.tax_calculation,i.price_with_tax,inv.on_hand_qty from category c join category_hierarchy ch on c.cid = ch.cid join item_category ic on ic.category_no = c.cid join item i on i.item_no = ic.item_no join inventory inv  on i.item_no = inv.item_no where c.cid in (8,9,45,379) and ic.item_no = ".$id;
 try {
    $db = getDB();
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
     $json= '{"category": ' . json_encode($categories) . '} ';
	 echo (cryptoJsAesEncrypt('pass',$json));
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
}
function getSubcategoryByid($subcat_id) {
	$sql = "select c.* from category c where cid = ".$subcat_id;
	try {
    $db = getDB();
    $stmt = $db->query($sql);
    $subcategories = $stmt->fetchAll(PDO::FETCH_OBJ);
    $json= '{"subcategory": ' . json_encode($subcategories) . '} ';
	echo (cryptoJsAesEncrypt('pass',$json));
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }
}
function getCategoryData(){
  $data = array();
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;
  $timestamp = isset($input->user->timestamp) ? $input->user->timestamp : 0 ;

  try {
    $userSql = "SELECT cid, status FROM user_category WHERE updated > ".$timestamp." AND uid = ".$uid;
    if($timestamp == 0) {
      $userSql .= " AND status = 'A'";
    }
    $db = getDB();
    $stmt = $db->query($userSql);
    $userData = $stmt->fetchAll(PDO::FETCH_OBJ);
    if(empty($userData)) {
      echo '{"category": {}, "timestamp": '.time().'}';
      die();
    } else {
      $categoryStr = '';
      foreach ($userData as $key => $val) {
        $data[$val->cid]['status'] = $val->status;
        $categoryStr .= $val->cid . ', ';
      }
      $categoryStr = trim($categoryStr, ", ");
    }

  } catch (PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }

  $sql = "SELECT category_hierarchy.cid, category.cid, category.name, category_hierarchy.parent FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = 0 AND category.cid IN (".$categoryStr.") ";

  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
    //echo count($categories);
    $mainitems = array();
    foreach($categories as $key => $val) {
      $cid = $categories[$key]->cid;
      $category_name = $categories[$key]->name;
      $parent = $categories[$key]->parent;
      $data[$cid]['name'] = $category_name;

      $sql2 = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = :parent";
      $stmt2 = $db->prepare($sql2);
      $stmt2->bindParam("parent", $cid);
      $stmt2->execute();
      $subcategories = $stmt2->fetchAll(PDO::FETCH_OBJ);
      if(!empty($subcategories)) {
        $data[$cid]['child'] = $subcategories;
      }
    }
    echo '{"category": ' . json_encode($data) . ', "timestamp": '.time().'}';
    $db = null;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function getProductData(){
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;
  $timestamp = isset($input->user->timestamp) ? $input->user->timestamp : 0 ;
  try {
    $userSql = "SELECT cid, status FROM user_category WHERE updated > ".$timestamp." AND uid = ".$uid;
    if($timestamp == 0) {
      $userSql .= " AND status = 'A'";
    }
    $db = getDB();
    $stmt = $db->query($userSql);
    $userData = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(empty($userData)) {
      echo '{"items": {}, "timestamp": '.time().'}';
      die();
    } else {
      $categoryStr = '';
      foreach ($userData as $key => $val) {
        $categoryStr .= $val->cid . ', ';
      }
      $categoryStr = trim($categoryStr, ", ");
    }

  } catch (PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }

  $sql1 = "SELECT category.cid,
  			   item_category.subclass_no,
               category.name,
               item.item_no,
               item.item1_desc,
               item.item2_desc,
               item.max_retail_amt,
               item.price_with_tax,
               item.unit_retail_amt,
               item.tax_pct,
               item.tax_calculation,
               item.status,
               inventory.club_no,
               inventory.item_status_code,
               inventory.on_hand_qty
          FROM ((wallmart.item item
                 INNER JOIN wallmart.item_category item_category
                    ON (item.item_no = item_category.item_no))
                INNER JOIN wallmart.category category
                   ON (category.cid = item_category.category_no))
               INNER JOIN wallmart.inventory inventory
                  ON (inventory.item_no = item.item_no) WHERE item.updated > ".$timestamp." AND category.cid IN (".$categoryStr.")";
  if($timestamp == 0) {
    $sql1 .= " AND item.status = 'A'";
  }

  $sql1 .= "  GROUP BY item.item_no";

  try {
    $db = getDB();
    $stmt1 = $db->prepare($sql1);
    $stmt1->execute();
    $mainitems = $stmt1->fetchAll(PDO::FETCH_OBJ);

    if(empty($mainitems)) {
      $sql2 = str_replace("item.updated > ".$timestamp." AND ", "", $sql1);
      $stmt2 = $db->prepare($sql2);
      $stmt2->execute();
      $mainitems = $stmt2->fetchAll(PDO::FETCH_OBJ);
    }
    $i = 1;
    $items = array();
    foreach($mainitems as $key => $val) {
      if($mainitems[$key]->subclass_no == 0) {
        $subclass_no = $mainitems[$key]->cid.'_0';
      } else {
        $subclass_no = $mainitems[$key]->subclass_no;
      }
      $items[$mainitems[$key]->cid][$subclass_no][$i]['subclass_no'] = $val->subclass_no;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['item_no'] = $val->item_no;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['item1_desc'] = utf8_encode($val->item1_desc);
      $items[$mainitems[$key]->cid][$subclass_no][$i]['item2_desc'] = utf8_encode($val->item2_desc);
      $items[$mainitems[$key]->cid][$subclass_no][$i]['max_retail_amt'] = $val->max_retail_amt;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['price_with_tax'] = $val->price_with_tax;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['unit_retail_amt'] = $val->price_with_tax;
      //$items[$mainitems[$key]->cid][$subclass_no][$i]['unit_retail_amt'] = $val->unit_retail_amt;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['tax_pct'] = $val->tax_pct;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['tax_calculation'] = $val->tax_calculation;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['club_no'] = $val->club_no;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['item_status_code'] = $val->item_status_code;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['on_hand_qty'] = $val->on_hand_qty;
      $items[$mainitems[$key]->cid][$subclass_no][$i]['status'] = $val->status;
      $i++;
    }

    if(!empty($items)) {
      echo '{"items": ' . json_encode($items) . ', "timestamp": '.time().'}';
    } else {
      echo '{"items": {}, "timestamp": '.time().'}';
    }


    $db = null;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
/*** PRODUCT API END ***/

function checkDataForSync() {
  $request = \Slim\Slim::getInstance()->request();
  $input = json_decode($request->getBody());
  $uid = $input->user->uid;
  $timestamp = isset($input->user->timestamp) ? $input->user->timestamp : 0 ;

  try {
    $userSql = "SELECT cid, status FROM user_category WHERE updated > ".$timestamp." AND uid = ".$uid;

    $db = getDB();
    $stmt = $db->query($userSql);
    $userData = $stmt->fetchAll(PDO::FETCH_OBJ);
    if(empty($userData)) {
      echo '{"category": 0, "product": 0, "timestamp": '.time().'}';
      die();
    } else {
      $categoryStr = '';
      foreach ($userData as $key => $val) {
        $categoryStr .= $val->cid . ', ';
      }
      $categoryStr = trim($categoryStr, ", ");
    }

  } catch (PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    die();
  }

  $sql1 = "SELECT category.cid,
  			   item_category.subclass_no,
               category.name,
               item.item_no,
               item.item1_desc,
               item.item2_desc,
               item.max_retail_amt,
               item.price_with_tax,
               item.unit_retail_amt,
               item.tax_pct,
               item.tax_calculation,
               item.status,
               inventory.club_no,
               inventory.item_status_code,
               inventory.on_hand_qty
          FROM ((wallmart.item item
                 INNER JOIN wallmart.item_category item_category
                    ON (item.item_no = item_category.item_no))
                INNER JOIN wallmart.category category
                   ON (category.cid = item_category.category_no))
               INNER JOIN wallmart.inventory inventory
                  ON (inventory.item_no = item.item_no) WHERE item.updated > ".$timestamp." AND category.cid IN (".$categoryStr.")";

  $sql1 .= "  GROUP BY item.item_no";

  try {
    $db = getDB();
    $stmt1 = $db->query($sql1);
    $mainitems = $stmt1->fetchAll(PDO::FETCH_OBJ);

    if(!empty($mainitems)) {
      echo '{"category": '.count($userData).', "product": '.count($mainitems).', "timestamp": '.time().'}';
    } else {
      echo '{"category": '.count($userData).', "product": 0, "timestamp": '.time().'}';
    }
    $db = null;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}


function getCategory() {
  $sql = "SELECT cid,name FROM category ORDER BY cid";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"category": ' . json_encode($categories) . '}';
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function getParentCategory() {
  $sql = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = 0 ORDER BY category.cid";
  try {
    $db = getDB();
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"category": ' . json_encode($categories) . '}';
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function getChildCategory($parent_id) {
  $sql = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = ".$parent_id." ORDER BY category.cid";
  try {
    $db = getDB();
    $stmt = $db->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"category": ' . json_encode($categories) . '}';
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function parentCategorySearch($query) {
  // $sql = "SELECT user_id,username,name,profile_pic FROM users WHERE UPPER(name) LIKE :query ORDER BY user_id";
  $sql = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = 0 AND UPPER(category.name) LIKE :query ORDER BY category.cid";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $query = "%".$query."%";
    $stmt->bindParam("query", $query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"users": ' . json_encode($users) . '}';
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function childCategorySearch($query) {
  //$sql = "SELECT user_id,username,name,profile_pic FROM users WHERE UPPER(name) LIKE :query ORDER BY user_id";
  //$sql = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent = 0 AND UPPER(category.name) LIKE :query ORDER BY category.cid";
  $sql = "SELECT category.cid, category.name FROM wallmart.category_hierarchy category_hierarchy INNER JOIN wallmart.category category ON (category_hierarchy.cid = category.cid) WHERE category_hierarchy.parent > 0 AND UPPER(category.name) LIKE :query ORDER BY category.cid";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $query = "%".$query."%";
    $stmt->bindParam("query", $query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"users": ' . json_encode($users) . '}';
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function cryptoJsAesEncrypt($passphrase, $value){
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$passphrase.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}
function getUsers() {
  $sql = "SELECT users.*,bda.* FROM users join bda where users.bda_id = bda.bda_id ORDER BY users.uid";
  try {
    $db = getDB();
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $json =  '{"users": ' . json_encode($users) . '}';
	echo (cryptoJsAesEncrypt('pass',$json));
	
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getProductLists() {
  $sql = "SELECT i.item_no, i.item1_desc, i.item2_desc, i.unit_retail_amt,i.max_retail_amt,i.tax_pct,i.tax_calculation,i.price_with_tax, inv.on_hand_qty, DATE_FORMAT(FROM_UNIXTIME(i.updated), '%m/%d/%y %H:%i:%s') last_updated_on FROM item as i join inventory as inv on i.item_no = inv.item_no join item_category ic on ic.item_no = inv.item_no join category c on c.cid = ic.category_no where ic.status = 'A' and c.cid in (8,9,45,379) group by i.item_no order by i.updated asc";
	try {
    $db = getDB();
    $stmt = $db->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $json =  '{"products": ' . json_encode($products) . '}';
	echo (cryptoJsAesEncrypt('pass',$json));
	
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}
function getUserUpdates() {
  $sql = "SELECT A.user_id, A.username, A.name, A.profile_pic, B.update_id, B.user_update, B.created FROM users A, updates B WHERE A.user_id=B.user_id_fk  ORDER BY B.update_id DESC";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"updates": ' . json_encode($updates) . '}';

  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function getUserUpdate($update_id) {
  $sql = "SELECT A.user_id, A.username, A.name, A.profile_pic, B.update_id, B.user_update, B.created FROM users A, updates B WHERE A.user_id=B.user_id_fk AND B.update_id=:update_id";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("update_id", $update_id);
    $stmt->execute();
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"updates": ' . json_encode($updates) . '}';

  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function insertUpdate() {
  $request = \Slim\Slim::getInstance()->request();
  $update = json_decode($request->getBody());
  $sql = "INSERT INTO updates (user_update, user_id_fk, created, ip) VALUES (:user_update, :user_id, :created, :ip)";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("user_update", $update->user_update);
    $stmt->bindParam("user_id", $update->user_id);
    $time=time();
    $stmt->bindParam("created", $time);
    $ip=$_SERVER['REMOTE_ADDR'];
    $stmt->bindParam("ip", $ip);
    $stmt->execute();
    $update->id = $db->lastInsertId();
    $db = null;
    $update_id= $update->id;
    getUserUpdate($update_id);
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function deleteUpdate($update_id) {

  $sql = "DELETE FROM updates WHERE update_id=:update_id";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("update_id", $update_id);
    $stmt->execute();
    $db = null;
    echo true;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }

}

function getUserSearch($query) {
  $sql = "SELECT user_id,username,name,profile_pic FROM users WHERE UPPER(name) LIKE :query ORDER BY user_id";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $query = "%".$query."%";
    $stmt->bindParam("query", $query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"users": ' . json_encode($users) . '}';
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
}

function order_load($orderId) {
  $orderData = array();
  $db = getDB();
  $orderData = $db->query("SELECT * FROM commerce_order WHERE order_id = ".$orderId)->fetch(PDO::FETCH_OBJ);
  return $orderData;
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

function updateStockCount($item_no, $updatedStock) {
  $updateInventorySql = "UPDATE inventory SET on_hand_qty = ".$updatedStock." WHERE item_no = ".$item_no;
  $db = getDB();
  $stmt = $db->prepare($updateInventorySql);
  $updateStatus = $stmt->execute();

  updateRelativeTables($item_no, "A");
}

function updateRelativeTables($item_no, $status) {
  $db = getDB();
  $itemCategoryData = $db->query("SELECT * FROM item_category WHERE item_no = ".$item_no)->fetch(PDO::FETCH_OBJ);
  $updateItemSql = "UPDATE item SET updated = UNIX_TIMESTAMP(NOW()), status = '".$status."' WHERE item_no = ".$item_no;
  $updateItemCategorySql = "UPDATE item_category SET updated = UNIX_TIMESTAMP(NOW()), status = '".$status."' WHERE item_no = ".$item_no;
  $updateUserCategorySql = "UPDATE user_category SET updated = UNIX_TIMESTAMP(NOW()), status = '".$status."' WHERE cid = ".$itemCategoryData->category_no." OR cid = ".$itemCategoryData->subclass_no;

  // get the category of the item
  $stmt1 = $db->prepare($updateItemSql);
  $stmt1->execute();

  $stmt2 = $db->prepare($updateItemCategorySql);
  $stmt2->execute();

  $stmt3 = $db->prepare($updateUserCategorySql);
  $stmt3->execute();
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

  $mail->From = 'arifn@calsoftlabs.com';
  $mail->FromName = 'Arif N';
  $mail->addAddress($to);     // Add a recipient

  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = $subject;
  $mail->Body    = $body;
  return $mail->send();
}
?>