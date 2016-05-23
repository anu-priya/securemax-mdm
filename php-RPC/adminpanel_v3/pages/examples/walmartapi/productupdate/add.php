<?php
include('config.php');
$cList = mysqli_query($con,"select c.* from category c join category_hierarchy ch on c.cid = ch.cid where c.cid in (8,9,45,379)");
if(isset($_POST['formaction']) && $_POST['formaction'] == 'changecategory')	{
	$subCList = mysqli_query($con, "select c.* from category c join category_hierarchy ch on c.cid = ch.cid where ch.parent = ".$_POST['category']);
}
?>
<html>
	 <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    .col-sm-10{
      width: 300px;
    }
    </style>
	<body style="margin:10px; padding:10px;">
	<h2>Add New Product</h2>
	<a href="list.php"><button type="submit" class="btn btn-default">Product List</button></a>
	<br/>
	<br/>
	<fieldset style="width:600px;">
	    <form method="post" name='frm_addproduct' id='frm_addproduct' action='add.php' class="form-horizontal" onSubmit="return validateForm(this);">
		<input type='hidden' name='formaction' id='formaction' value='' />
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Category</label>
	      <div class="col-sm-8">
	        <select name="category" id="category" class="form-control" onChange="document.getElementById('formaction').value='changecategory'; this.form.submit();">
				<option value=''>--Select a Category--</option>
				<?php
				if(!empty($cList)){
					while($res = mysqli_fetch_array($cList))
					{
				?>
						<option value='<?php echo $res['cid'];?>' <?php if(isset($_POST['category']) && $_POST['category'] == $res['cid']) {echo "selected";} ?> ><?php echo $res['name'];?></option>
				<?php
					}
				}
				?>
			</select>
	      </div> 
		</div>
		<div class="form-group">
		  <label for="inputEmail3" class="col-sm-4 control-label">Sub Category</label>
	      <div class="col-sm-8">
	        <select name="subcategory" id="subcategory" class="form-control" >
				<option value=''>--Select a Sub Category--</option>
				<?php
				if(isset($_POST['formaction']) && $_POST['formaction'] == 'changecategory' && !empty($subCList))	{
					while($res = mysqli_fetch_array($subCList))
					{
				?>
						<option value='<?php echo $res['cid'];?>'><?php echo $res['name'];?></option>
				<?php
					}
				}
				?>
			</select>
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Item Description 1</label>
	      <div class="col-sm-8">
	        <input type="text" name="itemdesc1" class="form-control" id="inputEmail3" maxlength="45">
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Item Description 2</label>
	      <div class="col-sm-8">
			<input type="text" name="itemdesc2" class="form-control" id="inputEmail3" maxlength="45">
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Sell Price</label>
	      <div class="col-sm-8">
	        <input type="text" name="sellprice" class="form-control" id="sellprice" maxlength="10" onblur="updateSellPrice();">
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Tax</label>
	      <div class="col-sm-8">
	        <select name="tax" class="form-control" id="tax" onChange="updatePrice(this.value);" >
			<option value="">--Select Tax--</option>
			<?php
			for( $i=1; $i<=100; $i++ ) {
				$calcper = $i/100;
				print '<option value="'.$calcper.'">'.$i.'%</option>';
			}
			?>
			</select>
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">MRP</label>
	      <div class="col-sm-8">
	        <input type="text" name="mrp" class="form-control" id="inputEmail3" maxlength="10" >
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Price With Tax</label>
	      <div class="col-sm-8">
	        <input type="text" name="hidprice" class="form-control" id="hidprice" maxlength="10" disabled>
			<input type="hidden" name="price" class="form-control" id="price" >
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Stock</label>
	      <div class="col-sm-8">
	        <input type="text" name="quantity" class="form-control" id="inputEmail3" maxlength="5">
	      </div> 
	    </div>
		<input type="submit" class="btn btn-default" name="addProduct" id="addProduct" onClick="document.getElementById('formaction').value='formsubmit';">
		</form>
	</fieldset>
	<?php
	if(isset($_POST['addProduct']) && isset($_POST['formaction']) && $_POST['formaction'] == 'formsubmit')	{
		$itemdesc1 = mysqli_real_escape_string($con,$_POST['itemdesc1']);
		$itemdesc2 = mysqli_real_escape_string($con,$_POST['itemdesc2']);
		$price = mysqli_real_escape_string($con,$_POST['price']);
		$quantity = mysqli_real_escape_string($con,$_POST['quantity']);
    
    $checkZero = mysqli_query($con, "select count(*) as num from item where item_no = 0");
    if($checkZero) {
      $checkres = mysqli_fetch_array($checkZero);
      if($checkres['num'] > 0){
        $deleteItemZero = mysqli_query($con, "delete from item where item_no = 0");
        $deleteItemCategoryZero = mysqli_query($con, "delete from item_category where item_no = 0");
        $deleteInventoryZero = mysqli_query($con, "delete from inventory where item_no = 0");
      }
    }
	
	/* pricecalc */
	$sellprice = mysqli_real_escape_string($con,$_POST['sellprice']);
	$tax = mysqli_real_escape_string($con,$_POST['tax']);
	$taxcalc = 1 + $tax;
	$maxretail = mysqli_real_escape_string($con,$_POST['mrp']); 

			
		$add = mysqli_query($con, "insert into item(`item1_desc`, `item2_desc`, `max_retail_amt`, `unit_retail_amt`, `tax_pct`, `tax_calculation`, `price_with_tax`,`updated`) values('$itemdesc1','$itemdesc2', '$maxretail', '$sellprice','$tax','$taxcalc','$price', ".time().")");
		$item_id = mysqli_insert_id($con);
		
		$add_inventory = mysqli_query($con, "insert into inventory(`club_no`, `item_no`, `item_status_code`, `on_hand_qty`) values(4712, '".$item_id."', 'A', '".$quantity."')");
		
		$add_itemcategory = mysqli_query($con, "insert into item_category(`item_no`, `category_no`, `subclass_no`, `status`, `updated`) values('".$item_id."', '".$_POST['category']."', '".$_POST['subcategory']."', 'A', ".time().")");
		
		$update_usercategory = mysqli_query($con, "update user_category set updated = ".time()." where uid = 7 and cid='".$_POST['category']."'");
		
		if($add) {
			echo"<script> location.href='list.php'</script>";
			exit;
		}
	}
	?>
	<script>
		function validateForm(frm) {
			if(frm.category.value== '') {
				alert("Please select a Category");
				frm.category.focus();
				return false;
			} else if(frm.subcategory.value == '') {
				alert("Please select a Sub Category");
				frm.subcategory.focus();
				return false;
			} else if(frm.itemdesc1.value == '') {
				alert("Please enter a item description 1");
				frm.itemdesc1.focus();
				return false;
			}  else if(frm.sellprice.value == '') {
				alert("Please enter a sell price");
				frm.sellprice.focus();	
				return false;
			}  else if(frm.mrp.value == '') {
				alert("Please enter a MRP");
				frm.mrp.focus();	
				return false;
			} 
			/* else if(frm.mrp.value != parseInt(frm.mrp.value)) {
				alert("Please enter only integer");
				frm.mrp.focus();
				return false;
			} else if(frm.sellprice.value != parseFloat(frm.sellprice.value)) {
				alert("Please enter only integer");
				frm.sellprice.focus();
				return false;
			} else if(frm.price.value == '') {
				frm.price.focus();	
				alert("Please enter a price");
				return false;
			}
			else if(frm.price.value != parseInt(frm.price.value)) {
				alert("Please enter only integer");
				frm.price.focus();
				return false;
			} else if(frm.quantity.value != parseInt(frm.quantity.value)) {
				alert("Please enter only integer");
				frm.quantity.focus();
				return false;
			} 
			*/
		}
		function updatePrice(res){
			var calPercentage = document.getElementById("sellprice").value * res;
			var sprice = parseFloat(document.getElementById("sellprice").value) + parseFloat(calPercentage);
			document.getElementById("price").value = sprice;
			document.getElementById("hidprice").value = sprice;
		}
		function updateSellPrice() {
			if(document.getElementById("tax") != null) {
				var price = document.getElementById("sellprice").value * document.getElementById("tax").value;
				var sprice = parseFloat(document.getElementById("sellprice").value) + parseFloat(price);
				document.getElementById("price").value = sprice;
				document.getElementById("hidprice").value = sprice;
			} else {
				var price = document.getElementById("sellprice").value;
				document.getElementById("price").value = price;
				document.getElementById("hidprice").value = price;
			}
		}
	</script>
	</body>
</html>
