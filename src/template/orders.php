<h1>orders</h1>
<table>

	<tr>
		<th>Order ID</th>
		<th>Total</th>
		<th>Created Date</th>
		<th>Products</th>
		<th>Order Status</th>
	</tr>
<?php

foreach ($param as $key => $order) {
	?>
	<tr <?php echo $order['orderID'] ?>>
		<td><?php echo $order['orderID'] ?></td>
		<td>RM <?php echo $order['total'] ?></td>
		<td><?php echo $order['createdDate'] ?></td>
		<td>
			<table>
				<tr>
					<th>ID</th>
					<th>Image</th>
					<th>Name</th>
					<th>Quantity</th>
					<th>Unit Price</th>


				</tr>
				<?php
				foreach ($order['products'] as $key => $product) {
					?>
					<tr>
						<td><?php echo $product->productID?></td>
						<td><img class="cart-image" src="<?php echo $product->image?>"></td>
						<td><?php echo $product->product_name?></td>
						<td><?php echo $product->qty?></td>
						<td>RM <?php echo $product->unit_price?></td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
		<td class="<?php echo ($order['status'] == '0' ? 'pending': 'approved')?>">
			<?php

			if($order['status'] == '1') {
				echo "Approved";
			} else {
				?>
				<select data-order-status id="<?php echo $order['orderID'] ?>">
				<option value="0" selected="selected">Pending</option>
				<option value="1">Approve</option>

			</select>
				<?php
			}
			?>
			
		</td>
	</tr>
	<?php
}
?>
</table>