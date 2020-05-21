
<div class="grid-container">
	<div class="grid-x grid-padding-x">
		<div class="large-12 cell">
			<h1>Catalogue</h1>
		</div>
	</div>

	<div class="grid-x grid-padding-x">
		<div class="large-12 cell text-right">
			
		<div class="grid-x grid-padding-x">

			<div class="large-12 medium-12 cell" data-approved-order>
				<span data-approved-order>approved <span data-approved-order-value><?php echo $approvedOrders['count']->approved;?></span></span> <br/>
		
			<?php

			foreach ($approvedOrders['orderID'] as $key => $approved) {
				?>
				
						<a href="<?php echo ROOT_DIR;?>/order/<?php echo $approved->orderID?>">
							#<span><?php echo $approved->orderID?></span>
								
						</a>
					
		
				<?php
			}
			?>
		    </div>
		</div>
	<div class="grid-x grid-padding-x">
		<div class="large-12 medium-12 cell">
			<button class="button view-cart" data-open="viewCart"><span>View Cart<span></button>
			<span data-cart-total></span>

			<div class="reveal" id="viewCart" data-reveal>

				<table id="cart">
					<thead>
						<tr>
						<th>Image</th>
						<th>Name</th>
						<th>Quantity</th>
						<th>Price</th>


					    </tr>
					</thead>
					<tbody id="cartBody">
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>

							<th>Total</th>

						</tr>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							
							<th>RM <span data-total></span></th>

						</tr>
						<tr>
							<th data-clear-cart>Clear cart</th>
							<th colspan="3" class="text-right"><button id="submitOrder">Submit order</button></th>

						</tr>

					</tfoot>
				</table>


			<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
			</button>
			</div>
		</div>

	</div>

		</div>

	</div>

	<?php
	if (isset($param['result']) && sizeof($param['result']) > 0) {

	?>
	<div class="grid-x grid-padding-x">
		<div class="large-6 cell text-left">
			Showing <?php echo sizeof($param['result']);?> of <?php echo $param['total'];?> products
		</div>
		<div class="large-6 cell text-right">
			<?php
			for($i = 1; $i <= $param['pages']; $i++) {
				?>
				<span><a href="<?php echo ROOT_DIR;?>/catalogue/<?php echo $i?>"><?php echo $i;?></a></span>
				<?php
			}
			?>
		</div>
	</div>

	<div class="grid-x grid-padding-x">
		<?php

		foreach ($param['result'] as $key => $product) {
			?>
			<div class="large-3 medium-3 small-6 cell product">
				<div class="grid-x grid-padding-x">
				<div class="large-10 cell">

					<div class="grid-x grid-padding-x">
						<div class="large-12 cell">
							<img src="<?php echo $product['images'][0]['src']?>">
						</div>
					</div>

					<div class="grid-x grid-padding-x">
						<div class="large-12 cell">

							<span><?php echo $product['name']?></span>
							<i>SKU: <?php echo $product['sku']?></i>
						</div>
					</div>



					<div class="grid-x grid-padding-x">
						<div class="large-12 cell">
							<?php
							foreach ($product['categories'] as $key => $cat) {
								?>
								<span class="categories"><?php echo $cat['name'];?></span>
								<?php
							}
							?>
						</div>
					</div>

					<div class="grid-x grid-padding-x">
						<div class="large-12 cell">
							<?php
							$variations = $product['variations'];
							if (sizeof($variations) > 0) {
								?>
								<button class="button click-more" data-open="moreDetails_<?php echo $product['id']?>">Click me for more detail</button>
								<?php
							}
							?>
							

							<div class="reveal" id="moreDetails_<?php echo $product['id']?>" data-reveal>

							  
							<?php
							
							foreach ($variations as $key => $value) {
								?>
									
								
								<table class="variations">
								<tr>
									<td colspan="2">
										<div class="grid-x grid-padding-x">
											<div class="large-12 cell">
												<span>
													<?php
													if ($value['in_stock']) {
												
														?>
														<button class="addtocart" 
														data-image="<?php echo $product['images'][0]['src'];?>"
														data-name="<?php echo $product['name'];?>"
														data-price="<?php echo ($value['sale_price'] ? $value['sale_price'] :$value['regular_price'])?>" 
														value="<?php echo $value['id']?>">add to cart</button>
														<?php
													}
													?>

												</span>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>ID</td>
									<td><?php echo $value['id']?></td>
								</tr>

								<tr>
									<td>SKU</td>
									<td><?php echo $value['sku']?></td>
								</tr>

								<tr>
									<td>Desc</td>
									<td><?php echo $value['desc']?></td>
								</tr>

								<tr>
									<td>Regular Price</td>
									<td>
										<?php echo $param['currency'];?>
										<?php echo $value['regular_price']?></td>
								</tr>
								<?php
								if (isset($value['sale_price']) && $value['sale_price'] != '') {
									?>
									<tr>
										<td>Sale Price</td>
										<td>
											<?php echo $param['currency'];?>
											<?php echo $value['sale_price']?>	
										</td>
									</tr>
									<tr>
										<td>Sale From</td>
										<td><?php echo $value['date_on_sale_from']?></td>
									</tr>
									<tr>
										<td>Sale To</td>
										<td><?php echo $value['date_on_sale_to']?></td>
									</tr>
									<?php
								}

								?>
								
								<!-- <tr>
									<td>Stock</td>
									<td><?php echo ($value['stock_quantity'] > 0 ? $value['stock_quantity'] : 'No Stock')?></td>
								</tr> -->
								<tr>
									<?php
									if ($value['in_stock']) {
										$stockClass = 'in-stock';
									} else {
										$stockClass = 'no-stock';
									}
									?>
									<td>In stock</td>
									<td class="<?php echo $stockClass;?>"><?php echo ($value['in_stock'] ? 'yes': 'no')?></td>
									
								</tr>
								<tr>
									<td>Weight</td>
									<td><?php echo $value['weight']?></td>
								</tr>
								<tr>
									<td>Dimension</td>
									<td>
										l: <?php echo $value['dimensions']['length']?>
										w: <?php echo $value['dimensions']['width']?>
										h: <?php echo $value['dimensions']['height']?>
										</td>
								</tr>
								<!-- inventory only for admin use-->
<!-- 								<tr>
									<td>Inventory</td>
									<td>
										<table>
										<?php 
										foreach ($value['inventory'] as $key => $value) {
										
												?>
												<td>
													<table>
														<tr>
															<td>Branch ID</td>
															<td><?php echo $value['branch_id']?></td>
														</tr>

														<tr>
															<td>Stock Quantity</td>
															<td><?php echo $value['stock_quantity']?></td>
														</tr>

														<tr>
															<td>Physical Stock Quantity</td>
															<td><?php echo $value['physical_stock_quantity']?></td>
														</tr>
													</table>
												</td>
												<?php
										
											?>
											<?php
										}
										?>
											</table>
									</td>
								</tr> -->
								</table>
							
								<?php
							}
							?>
							
			

							<button class="close-button" data-close aria-label="Close modal" type="button">
							    <span aria-hidden="true">&times;</span>
							  </button>

							</div>


						</div>
					</div>



					
					
				</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
<?php
} else  {
	?>
	<a href="http://localhost/boostorder/catalogue">Catalogue</a>
	<p>No product found</p>
	<?php
}
?>

</div>



<!--cart modify modal-->
<button class="button modify-cart" data-open="modifyCart"></button>

<div class="reveal" id="modifyCart" data-reveal>

	<p class="message"></p>
	<button id="addMore">Yes</button>
	<button id="removeFromCart">Remove from cart</button>


<button class="close-button" data-close aria-label="Close modal" type="button">
<span aria-hidden="true">&times;</span>
</button>
</div>