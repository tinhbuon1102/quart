<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPC_Datetime_Condition' ) ) {

	class WPC_Datetime_Condition extends WPC_Condition {

		public function __construct() {
			$this->name        = __( 'Datetime', 'wpc-conditions' );
			$this->slug        = __( 'datetime', 'wpc-conditions' );
			$this->group       = __( 'General', 'wpc-conditions' );
			$this->description = sprintf( __( 'Compares current server time to user given time. Current time: %s', 'woocommerce-advanced-messages' ), date_i18n( get_option( 'time_format' ) ) );

			parent::__construct();
		}

		public function get_value( $value ) {
			return date_i18n( 'Y-d-d H:i', strtotime( $value ) ); // Returns set time in Hour:Minutes
		}

		public function get_compare_value() {
			return date( 'Y-d-d H:i' ); // Compares against current time in Hour:Minutes
		}

		public function get_value_field_args() {

			$field_args = array(
				'type' => 'text',
				'class' => array( 'wpc-value' ),
				'placeholder' => 'yyyy-mm-dd H:i',
			);

			return $field_args;

		}

	}

}