jQuery( function ( $ ) {
    $( document ).on( 'click', '.yith-wcpsc-product-size-chart-button, .yith-wcpsc-product-size-chart-list, a[href^="#yith-size-chart?"]', function ( event ) {
        var c_id      = $( this ).data( 'chart-id' ),
            all_popup = $( '.yith-wcpsc-product-size-charts-popup' ),
            style     = $( this ).data( 'chart-style' ),
            effect    = $( this ).data( 'chart-effect' ),
            href      = $( this ).attr( 'href' );

        if ( href && href.length > 0 && href.search( '#yith-size-chart?' ) > -1 ) {
            event.preventDefault();
            href     = href.replace( '#yith-size-chart?', '' );
            var data = href.split( '&' );
            if ( data.length > 0 ) {
                for ( var i = 0; i < data.length; i++ ) {
                    var current_data = data[ i ].split( '=' );
                    if ( current_data.length == 2 ) {
                        switch ( current_data[ 0 ] ) {
                            case 'id':
                                if ( !c_id ) {
                                    c_id = current_data[ 1 ];
                                }
                                break;
                            case 'style':
                                if ( !style ) {
                                    style = current_data[ 1 ];
                                }
                                break;
                            case 'effect':
                                if ( !effect ) {
                                    effect = current_data[ 1 ];
                                }
                                break;
                        }
                    }
                }
            }
        }
        
        var my_popup = $( '#yith-wcpsc-product-size-charts-popup-' + c_id );

        if ( style && style.length > 0 ) {
            my_popup.removeClass( 'yith-wcpsc-product-size-charts-popup-default' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-elegant' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-casual' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-informal' )
                .addClass( 'yith-wcpsc-product-size-charts-popup-' + style );
        }

        if ( !(effect && effect.length > 0 ) ) {
            effect = ajax_object.popup_effect
        }

        // set max height of table wrapper to allow scrolling
        my_popup.find( '.yith-wcpsc-product-table-wrapper' ).css( 'max-height', ($( window ).height() - 120) + 'px' );

        all_popup.each( function () {
            $( this ).yith_wcpsc_popup( 'close' );
        } );

        my_popup.find( '.yith-wcpsc-product-table-wrapper-tabbed-popup' ).tabs();

        var created_popup = my_popup.yith_wcpsc_popup( {
                                                           position: ajax_object.popup_position,
                                                           effect  : effect
                                                       } );
        created_popup.find( '.yith-wcpsc-product-table-wrapper-tabbed-popup' ).tabs();
    } );

    // set max height of table wrapper to allow scrolling
    $( '.yith-wcpsc-product-size-charts-popup-container .yith-wcpsc-product-table-wrapper' ).css( 'max-height', ($( window ).height() - 120) + 'px' );
} );
