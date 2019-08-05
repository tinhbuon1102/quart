<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCPMR_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Payment Method Restrictions List
 *
 * @class   YITH_WCPMR_List
 * @package YITH Payment Method Restrictions
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class YITH_WCPMR_List extends WP_List_Table {


    /**
     * YITH_WCPMR_List constructor.
     *
     * @param array $args
     */
    public function __construct( $args = array() ) {
        parent::__construct( array(
            'singular' => __( 'Payment method restriction', 'yith-payment-method-restrictions-for-woocommerce' ),
            'plural'   => __( 'Payment method restrictions', 'yith-payment-method-restrictions-for-woocommerce' ),
            'ajax'     => false
        ) );
    }

    /**
     * @return array
     */
    function get_columns() {

        $columns = array(
            'cb'         => '<input type="checkbox" />',
            'rule_name'  =>__('Rule name','yith-payment-method-restrictions-for-woocommerce'),
            'payment_method_restriction' => __( 'Payment Method', 'yith-payment-method-restrictions-for-woocommerce' ),
            'status'  =>__('Status','yith-payment-method-restrictions-for-woocommerce'),
        );


        return apply_filters( 'yith_wcpmr_column_list', $columns );
    }

    /**
     * get views for the table
     * @author YITHEMES
     * @since 1.0.0
     * @return array
     */
    protected function get_views()
    {
        $views = array( 'all' => __( 'All', 'yith-payment-method-restrictions-for-woocommerce' ),
            'publish' => __( 'Published', 'yith-payment-method-restrictions-for-woocommerce' ),
            'trash' => __( 'Trash', 'yith-payment-method-restrictions-for-woocommerce' ) );

        $current_view = $this->get_current_view();

        foreach ( $views as $view_id => $view ) {

            $query_args = array(
                'posts_per_page' => -1,
                'post_type' => 'yith_wcpmr_rule',
                'post_status' => 'publish',
                'suppress_filter' => false
            );
            $status = 'status';
            $id = $view_id;

            if ( 'all' !== $view_id ) {
                $query_args[ 'post_status' ] = $view_id;
            }

            $href = esc_url( add_query_arg( $status, $id ) );
            $total_items = count( get_posts( $query_args ) );
            $class = $view_id == $current_view ? 'current' : '';
            $views[ $view_id ] = sprintf( "<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>", $href, $class, $view, $total_items );
        }


        return $views;
    }

    /**
     * return current view
     * @author YITHEMES
     * @since 1.0.0
     * @return string
     */
    public function get_current_view()
    {

        return empty( $_GET[ 'status' ] ) ? 'all' : $_GET[ 'status' ];
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @since 1.0.0
     */
    function prepare_items() {

        $current_view = $this->get_current_view();
        if($current_view == 'all'){
            $current_view = 'any';
        }

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $perpage = apply_filters( 'yith_wcpmr_per_page', 15 );

        $args = array(
            'post_type'           => 'yith_wcpmr_rule',
            'post_status'         => $current_view,
            'posts_per_page'      => $perpage,
            'paged'               => absint( $this->get_pagenum() ),
            'orderby'             => 'ID',
            'order'               => 'DESC',
        );
        $query = new WP_Query( $args );
        $this->items = $query->posts;

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $query->found_posts,
            "per_page" => $perpage
        ) );
    }

    /**
     * @param object $item
     * @param string $column_name
     *
     * @return string|void
     */
    function column_default( $item, $column_name ) {
        $payment_restriction_rule = get_post_meta($item->ID,'yith_wcpmr_payment_restriction',true);

        switch( $column_name ) {
            case 'payment_method_restriction':
                $payment_method = $payment_restriction_rule['gateway'];
                $payments = yith_wcpmr_get_payment_gateway();
                if (!empty($payments[$payment_method])) {
                    $payment = $payments[$payment_method];
                    return '<span class="status ' . $payment . '">' . $payment . '</span>';
                }else {
                    return '';
                }
                break;

            case 'rule_name':
                return ($item->post_title) ? '<span>'.$item->post_title.'</span>':'';
                break;

            case 'status':
                 $status = get_post_meta($item->ID,'yith_wcpmr_enable_disable_payment_restriction',true);
                 if( isset( $status ) && !empty( $status ) ) {
                     return '<span>'.__('Disabled','yith-payment-method-restrictions-for-woocommerce').'</span>';
                 } else {
                     return '<span>'.__('Enabled','yith-payment-method-restrictions-for-woocommerce').'</span>';
                 }
            default:
                return apply_filters('yith_wcdppm_column_default','',$item, $column_name); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * @author YITHEMES
     * @since 1.0.0
     * @param object $item
     * @return string
     */
    public function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="wcpmr_ids[]" value="%s" />', $item->ID
        );
    }

    /**
     * return bulk actions
     * @author YITHEMES
     * @since 1.0.0
     * @return array|false|string
     */
    public function get_bulk_actions()
    {

        $actions = $this->current_action();
        $current_view = $this->get_current_view();

        if ( isset( $_REQUEST[ 'wcpmr_ids' ] ) ) {

            $rules = $_REQUEST[ 'wcpmr_ids' ];

            if ( $actions == 'delete' && $current_view == 'trash' ) {
                foreach ( $rules as $rule_id ) {
                    wp_delete_post( $rule_id, true );
                }
            }else {
                if ($actions == 'delete') {
                    foreach ( $rules as $rule_id ) {
                        wp_trash_post( $rule_id );
                    }
                }
            }

            $this->prepare_items();
        }

        if ($current_view == 'trash') {
            $actions = array(
                'delete' => __( 'Delete permanently', 'yith-payment-method-restrictions-for-woocommerce' )
            );
        } else {
            $actions = array(
                'delete' => __( 'Delete', 'yith-payment-method-restrictions-for-woocommerce' )
            );
        }
        return $actions;
    }
    /**
     * @return array
     */
    function get_sortable_columns() {

        $sortable_columns = array(
            'post_title'         => array( 'Rule name', false ),
            'rule_type'          => array( 'Rule type', false ),
        );
        return $sortable_columns;
    }

    /**
     * Function to edit or delete rules
     * @return array
     */
    protected function handle_row_actions( $post, $column_name, $primary ) {
        if ( $primary !== $column_name ) {
            return '';
        }

        $post_type_object = get_post_type_object( $post->post_type );
        $can_edit_post = current_user_can( 'edit_post', $post->ID );
        $title = _draft_or_post_title();
        $actions = array();

        if ( $can_edit_post && 'trash' != $post->post_status ) {
            $actions['edit'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                get_edit_post_link( $post->ID ),
                /* translators: %s: post title */
                esc_attr( sprintf( __( 'Edit %s' ), $title ) ),
                __( 'Edit' )
            );
        }

        if ( current_user_can( 'delete_post', $post->ID ) ) {
            if ( 'trash' === $post->post_status ) {
                $actions['untrash'] = sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( 'Restore %s from the Trash' ), $title ) ),
                    __( 'Restore' )
                );
            } elseif ( EMPTY_TRASH_DAYS ) {
                $actions['trash'] = sprintf(
                    '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                    get_delete_post_link( $post->ID ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( 'Move %s to the Trash' ), $title ) ),
                    _x( 'Trash', 'verb' )
                );
            }
            if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {
                $actions['delete'] = sprintf(
                    '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                    get_delete_post_link( $post->ID, '', true ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( 'Delete %s permanently' ), $title ) ),
                    __( 'Delete Permanently' )
                );
            }
        }

        return $this->row_actions( $actions );
    }
}