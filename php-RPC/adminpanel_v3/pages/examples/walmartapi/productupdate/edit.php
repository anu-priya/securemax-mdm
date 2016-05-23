<?php
include('config.php');
$cList = mysqli_query($con,"select c.* from category c join category_hierarchy ch on c.cid = ch.cid where c.cid in (8,9,45,379)");

$query1= mysqli_query($con,"select c.*,ic.subclass_no,i.item1_desc,i.item2_desc,i.unit_retail_amt,i.max_retail_amt,i.tax_pct,i.tax_calculation,i.price_with_tax,inv.on_hand_qty from category c join category_hierarchy ch on c.cid = ch.cid join item_category ic on ic.category_no = c.cid join item i on i.item_no = ic.item_no join inventory inv  on i.item_no = inv.item_no where c.cid in (8,9,45,379) and ic.item_no = ".$_GET['id']);
$query2=mysqli_fetch_array($query1);
$query3=mysqli_query($con,"select c.* from category c where cid = ".$query2['subclass_no']);
$result=mysqli_fetch_array($query3);
?>
<html>
	 <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    .col-sm-10{
      width: 300px;
    }
    </style>
	<body style="margin:10px; padding:10px;">
	<h2>Edit Product</h2>
	<a href="list.php"><button type="submit" class="btn btn-default">Product List</button></a>
	<br/>
	<br/>
	<fieldset style="width:600px;">
	    <form method="post" name='frm_addproduct' id='frm_addproduct' action='' class="form-horizontal" onSubmit="return validateForm(this);">
		<input type='hidden' name='formaction' id='formaction' value='' />
		<input type='hidden' name='editcategory' id='editcategory' value='<?php echo $query2['cid']; ?>' />
		
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Category</label>
	      <div class="col-sm-8">
	        <select name="category" id="category" class="form-control" disabled>
				<option value=''><?php echo $query2['name']; ?></option>
				<?php
				if(!empty($cList)){
					while($res = mysqli_fetch_array($cList))
					{
						if($res['name'] != $query2['name'] ){
				?>
						<option value='<?php echo $res['cid'];?>' <?php if(isset($_POST['category']) && $_POST['category'] == $res['cid']) {echo "selected";} ?> ><?php echo $res['name'];?></option>
				<?php
						}
					}
				}
				?>
			</select>
	      </div> 
		</div>
		<div class="form-group">
		  <label for="inputEmail3" class="col-sm-4 control-label">Sub Category</label>
	      <div class="col-sm-8">
	        <select name="subcategory" id="subcategory" class="form-control" disabled>
				<option value=''><?php echo $result['name'] ?></option>
				<?php
				if(isset($_POST['formaction']) && $_POST['formaction'] == 'changecategory' && !empty($subCList))	{
					while($res = mysqli_fetch_array($subCList))
					{
						if($res['name'] != $result['name'] ){
					?>
							<option value='<?php echo $res['cid'];?>'><?php echo $res['name'];?></option>
					<?php
						}
					}
				}
				?>
			</select>
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Item Description 1</label>
	      <div class="col-sm-8">
	        <input type="text" name="itemdesc1" class="form-control" id="inputEmail3" maxlength="45" value='<?php echo $query2['item1_desc']; ?>'>
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Item Description 2</label>
	      <div class="col-sm-8">
			<input type="text" name="itemdesc2" class="form-control" id="inputEmail3" maxlength="45" value='<?php echo $query2['item2_desc']; ?>'>
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Sell Price</label>
	      <div class="col-sm-8">
	        <input type="text" name="sellprice" class="form-control" id="sellprice" onblur="updateSellPrice();" maxlength="10" value='<?php echo $query2['unit_retail_amt']; ?>' >
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
				print '<option value="'.$calcper.'" '. (($query2['tax_pct'] == $calcper)?"selected":"") .'>'.$i.'%</option>';
			}
			?>
	    	</select>
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">MRP</label>
	      <div class="col-sm-8">
	        <input type="text" name="mrp" class="form-control" id="inputEmail3" maxlength="10"  value='<?php echo $query2['max_retail_amt']; ?>'>
	      </div> 
	    </div>
	    <div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Price With Tax</label>
	      <div class="col-sm-8">
			<input type="text" name="hidprice" class="form-control" id="hidprice" maxlength="10" value='<?php echo $query2['price_with_tax']; ?>' disabled >
			<input type="hidden" name="price" class="form-control" id="price" >
	      </div> 
	    </div>
		<div class="form-group">
	      <label for="inputEmail3" class="col-sm-4 control-label">Stock</label>
	      <div class="col-sm-8">
	        <input type="text" name="quantity" class="form-control" id="inputEmail3" maxlength="5" value='<?php echo $query2['on_hand_qty']; ?>'>
	      </div> 
	    </div>
		<input type="submit" class="btn btn-default" name="updateProduct" id="updateProduct" onClick="document.getElementById('formaction').value='formsubmit';">
		</form>
	</fieldset>
	<?php
	if(isset($_POST['updateProduct']) && isset($_POST['formaction']) && $_POST['formaction'] == 'formsubmit')	{
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
		
		$update = mysqli_query($con, "update item set item1_desc='".$itemdesc1."', item2_desc='".$itemdesc2."', max_retail_amt='".$maxretail."',unit_retail_amt='".$sellprice."',tax_pct='".$tax."',tax_calculation='".$taxcalc."' ,price_with_tax='".$price."', updated='".time()."' where item_no = ".$_GET['id']);
		$item_id = $_GET['id'];
		
		
		$update_inventory = mysqli_query($con, "update inventory set on_hand_qty = ".$quantity." where item_no = ".$_GET['id']);
		
		$update_itemcategory = mysqli_query($con, "update item_category set updated = ".time()." where item_no = ".$_GET['id']);
		
		$update_usercategory = mysqli_query($con, "update user_category set updated = ".time()." where uid = 7 and cid='".$_POST['editcategory']."'");
		
		if($update)
		{echo"<script> location.href='list.php'</script>";
			exit;
		}
	}
	?>
	<script>
		function validateForm(frm) {
			if(frm.itemdesc1.value == '') {
				alert("Please enter a item description 1");
				frm.itemdesc1.focus();
				return false;
			}  else if(frm.sellprice.value == '') {
				alert("Please enter a sell price");
				frm.sellprice.focus();	
				return false;
			} else if(frm.mrp.value == '') {
				alert("Please enter a MRP");
				frm.mrp.focus();	
				return false;
			}
			/* else if(frm.price.value == '') {
				frm.price.focus();	
				alert("Please enter a price");
				return false;
			} else if(frm.price.value != parseInt(frm.price.value)) {
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
