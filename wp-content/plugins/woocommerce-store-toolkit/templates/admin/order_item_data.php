<?php if( $order_items ) { ?>
<table class="widefat striped" style="font-family:monospace; text-align:left; width:100%;">
	<tbody>

	<?php foreach( $order_items as $order_item ) { ?>
		<tr>
			<th colspan="3">
				order_item_name: <?php echo $order_item->name; ?><br />
				order_item_type: <?php echo $order_item->type; ?>
			</th>
		</tr>

		<?php if( $order_item->meta ) { ?>
			<?php foreach( $order_item->meta as $meta_value ) { ?>
		<tr>
			<th style="width:20%;">&raquo; <?php echo $meta_value->meta_key; ?></th>
			<td><?php echo $meta_value->meta_value; ?></td>
			<td class="actions"><?php do_action( 'woo_st_order_item_data_actions', $post->ID, $meta_value->meta_key ); ?></td>
		</tr>
			<?php } ?>
		<?php } ?>

	<?php } ?>
	</tbody>
</table>
<?php } ?>