<?php
	include('config.php');
?>
<html>
	 <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    .col-sm-10{
      width: 300px;
    }
    </style>
	<body style="margin:10px; padding:10px;">
	<h2>Product Listing</h2>
	<a href="add.php"><button type="submit" class="btn btn-default">Add New Product</button></a>
	<br/>
	<br/>
	<?php
	$list = mysqli_query($con,"SELECT i.item_no, i.item1_desc, i.item2_desc, i.unit_retail_amt,i.max_retail_amt,i.tax_pct,i.tax_calculation,i.price_with_tax, inv.on_hand_qty, DATE_FORMAT(FROM_UNIXTIME(i.updated), '%m/%d/%y %H:%i:%s') last_updated_on FROM item as i join inventory as inv on i.item_no = inv.item_no join item_category ic on ic.item_no = inv.item_no join category c on c.cid = ic.category_no where ic.status = 'A' and c.cid in (8,9,45,379) group by i.item_no order by i.updated desc");
	if(!empty($list)){
		echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr><th>Item Description 1</th><th>Item Description 2</th><th>Sell Price</th><th>Tax</th><th>MRP</th><th>Price With Tax</th><th>Stock</th><th>Last Updated</th><th>Edit Product</th></tr></thead>";
		while($res = mysqli_fetch_array($list))
		{
			echo "<tr><td>".$res['item1_desc']."</td>";
			echo "<td>".$res['item2_desc']."</td>";
			echo "<td>".$res['unit_retail_amt']."</td>";
			echo "<td>".$res['tax_pct']."</td>";
			echo "<td>".$res['max_retail_amt']."</td>";
			echo "<td>".$res['price_with_tax']."</td>";
			echo "<td>".$res['on_hand_qty']."</td>";
			echo "<td>".$res['last_updated_on']."</td>";
			echo "<td><a href='edit.php?id=".$res['item_no']."'>Edit</a></td>";
			echo "</tr>";
		}
	}
	?>
	</table>
	</div>
	</body>
</html>
  