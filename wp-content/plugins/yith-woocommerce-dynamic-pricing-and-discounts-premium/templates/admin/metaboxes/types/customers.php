<?php
if(!defined('ABSPATH')){
	exit;
}
global $post;
extract( $args );
$value =  get_post_meta( $post->ID, $id, true );
$value = ! is_array( $value ) ? explode( ',', $value ) : $value;

// Customers, products
$user_string = array();
if ( ! empty( $value ) ) {
	foreach ( $value as $key => $customer_id ) {
		$user = get_user_by( 'id', $customer_id );
		if ( $user ) {
			$user_string[ $customer_id ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
		} else {
			unset( $value[ $key ] );
		}
	}
}

?>

<?php if ( function_exists( 'yith_field_deps_data' ) ) : ?>
<div id="<?php esc_attr_e( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); ?> class="yith-plugin-fw-metabox-field-row">
	<?php else: ?>
    <div id="<?php esc_attr_e( $id ); ?>-container" <?php if ( isset( $deps ) ): ?> data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
		<?php endif; ?>
	<label for="<?php esc_attr_e( $id  );?>"><?php echo( $label);?></label>

	<?php
	if ( function_exists( 'yit_add_select2_fields' ) ) {
		$args = array(
			'type'             => 'hidden',
			'class'            => 'wc-customer-search',
			'id'               =>  $id,
			'name'             =>  $name,
			'data-placeholder' => esc_attr( $placeholder ),
			'data-allow_clear' => true,
			'data-selected'    => $user_string,
			'data-multiple'    => true,
			'value'            => implode( ',', $value ),
			'style'            => 'width:500px',
		);

		yit_add_select2_fields( $args );
	} ?>

</div>