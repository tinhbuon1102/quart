jQuery( document ).ready( function ( $ ) {

    // Shared function by categories and tags
    var results = function ( data ) {
        var terms = [];
        if ( data ) {
            $.each( data, function ( id, text ) {
                terms.push( { id: id, text: text } );
            } );
        }
        return {
            results: terms
        };
    };

    var initSelection   = function ( element, callback ) {
        var data     = $.parseJSON( element.attr( 'data-selected' ) );
        var selected = [];

        $( element.val().split( ',' ) ).each( function ( i, val ) {
            selected.push( {
                id  : val,
                text: data[ val ]
            } );
        } );
        return callback( selected );
    };
    var formatSelection = function ( data ) {
        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
    };
    /* Metabox functions */


    var $add_new_condition          = $( '#yith-wcpmr-new-condition' ),
        $condition_list             = $( '#yith-wcpmr-conditions-list' ),
        $gateway                    = $( '#yith-wcpmr-rule-gateway' ),
        $bacs_restriction_container = $( '#yith-wcpmr-bacs-payment-restriction-container' ),
        $bacs_select                = $( '#yith-wcpmr-bacs' ),
        $bacs_radio                 = $( 'input[type=radio][name=yith_wcpmr_checked_account]' ),
        $bacs_account_select        = $( '#yith-wcpmr-bacs-account-select' );

    $add_new_condition.on( 'click', function ( event ) {

        event.preventDefault();
        var post_data = {
            index : $( '.yith-wcpmr-conditions-row' ).size(),
            action: 'yith_wcpmr_add_condition_row'
        };
        $.ajax( {
            type   : "POST",
            data   : post_data,
            url    : yith_wcpmr_admin.ajaxurl,
            success: function ( response ) {
                $condition_list.append( response );

            }
        } );
    } );

    $gateway.on( 'change', function () {
        var payment_gateway = $( this ).val();

        if ( payment_gateway == 'bacs' ) {
            $bacs_restriction_container.fadeIn(1000);
            $bacs_radio.trigger('change');
            $bacs_select.select2();

        } else {
            $bacs_restriction_container.fadeOut(1000);
        }
    } );


    $( $bacs_radio ).on( 'change', function () {

        if($(this).is(':checked')) {
            if ( $(this).val() == 'change_bacs_account' ) {
                $bacs_account_select.fadeIn(1000);
            } else {
                $bacs_account_select.fadeOut(1000);

            }
        }
    } );

    var ywcpmr_rule_metabox = {
        init                  : function () {
            $( document ).on( 'click', '.yith-wcpmr-delete-condition', this.delete_condition );
            $( document ).on( 'change', '.yith-wcpmr-get-type-restriction', this.yith_change );
            this.check_bacs_account();
            this.show_fields();
        },
        check_bacs_account    : function () {
            if ( $( '#yith-wcpmr-rule-gateway' ).val() == 'bacs' ) {
                $( '.yith-wcpmr-bacs-payment-restriction' ).show();
                $( '.yith-wcpmr-bacs' ).select2();
                $bacs_radio.trigger('change');

            } else {
                $( '.yith-wcpmr-bacs-payment-restriction' ).hide();
            }
        },
        delete_condition      : function () {
            $( this ).closest( '.yith-wcpmr-conditions-row' ).remove();
        },
        show_fields           : function () {
            $( '.yith-wcpmr-li' ).each( function () {
                var row = $( this ).closest( '.yith-wcpmr-row' );
                if ( $( this ).hasClass( 'yith-wcpmr-hide-rule-set' ) ) {
                    row.hide();
                } else {
                    if ( $( this ).hasClass( 'yith-wcpmr-rule-set' ) ) {
                        $( this ).show();
                        if ( $( this ).hasClass( 'yith-wcpmr-select' ) ){
                            if ($( this ).hasClass( 'yith-wcpmr-selector2' ) ) {
                                ywcpmr_rule_metabox.yith_change( event, $( this ), $( this ).data( 'type' ) );
                            } else {
                                $( this ).select2();
                            }
                        }
                    }
                }
            } );
        },
        yith_change           : function ( event, selector, type_restriction ) {
            if ( typeof(selector) === 'undefined' ) selector = $( this );
            if ( typeof(type_restriction) === 'undefined' ) type_restriction = $( this ).val();
            var row = selector.closest( '.yith-wcpmr-conditions-row' );
            row.find( '.yith-wcpmr-select2' ).hide();
            row.find( '.yith-wcpmr' ).css( 'display', 'none' );
            switch ( type_restriction ) {
                case 'price':
                    row.find( '.yith-wcpmr-restriction-type option[value=""]' ).attr( 'selected', 'selected' );
                    row.find( '.yith-wcpmr-restriction-by-price' ).show();
                    row.find( '.yith-wcpmr-rule-price' ).select2();
                    row.find( '.yith-wcpmr-input-price' ).css( 'display', 'inline' );
                    break;
                case 'category':
                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();

                    row.find( '.yith-wcpmr-select2-categories' ).show();
                    row.find( '.yith-wcpmr-categories' ).select2();
                    row.find( ':input.yith-wcpmr-category-search' ).filter( ':not(.enhanced)' ).each( function () {
                        var ajax = {
                            url        : yith_wcpmr_admin.ajaxurl,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                return {
                                    term    : term,
                                    action  : 'yith_wcpmr_category_search',
                                    security: yith_wcpmr_admin.search_categories_nonce
                                };
                            },
                            cache      : true
                        };

                        if ( yith_wcpmr_admin.before_3_0 ) {
                            ajax.results = results;
                        } else {
                            ajax.processResults = results;
                        }
                        var select2_args = {
                            initSelection     : yith_wcpmr_admin.before_3_0 ? initSelection : null,
                            formatSelection   : yith_wcpmr_admin.before_3_0 ? formatSelection : null,
                            multiple          : $( this ).data( 'multiple' ),
                            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                            placeholder       : $( this ).data( 'placeholder' ),
                            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                            escapeMarkup      : function ( m ) {
                                return m;
                            },
                            ajax              : ajax
                        };
                        $( this ).select2( select2_args ).addClass( 'enhanced' ).on( 'change', function () {

                        } );
                        $( document.body ).trigger( 'wc-enhanced-select-init' );

                    } );

                    break;

                case 'tag':

                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();

                    row.find( '.yith-wcpmr-select2-tags' ).show();
                    row.find( '.yith-wcpmr-tags' ).select2();

                    row.find( ':input.yith-wcpmr-tags-search' ).filter( ':not(.enhanced)' ).each( function () {
                        var ajax = {
                            url        : yith_wcpmr_admin.ajaxurl,
                            dataType   : 'json',
                            quietMillis: 250,
                            data       : function ( term ) {
                                return {
                                    term    : term,
                                    action  : 'yith_wcpmr_tag_search',
                                    security: yith_wcpmr_admin.search_tags_nonce
                                };
                            },
                            cache      : true
                        };

                        if ( yith_wcpmr_admin.before_3_0 ) {
                            ajax.results = results;
                        } else {
                            ajax.processResults = results;
                        }
                        var select2_args = {
                            initSelection     : yith_wcpmr_admin.before_3_0 ? initSelection : null,
                            formatSelection   : yith_wcpmr_admin.before_3_0 ? formatSelection : null,
                            multiple          : $( this ).data( 'multiple' ),
                            allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
                            placeholder       : $( this ).data( 'placeholder' ),
                            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                            escapeMarkup      : function ( m ) {
                                return m;
                            },
                            ajax              : ajax
                        };
                        $( this ).select2( select2_args ).addClass( 'enhanced' ).on( 'change', function () {

                        } );

                        $( document.body ).trigger( 'wc-enhanced-select-init' );
                    } );


                    break;

                case 'product':
                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();

                    row.find( '.yith-wcpmr-select2-product' ).show();
                    row.find( '.yith-wcpmr-product-search' ).css( 'display', 'inline' );
                    $( document.body ).trigger( 'wc-enhanced-select-init' );
                    break;

                case 'geolocalization':
                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();
                    row.find( '.yith-wcpmr-select2-geolocalization' ).show();
                    row.find( '.yith-wcpmr-geolocalization-search' ).select2();
                    break;

                case 'role':
                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();
                    row.find( '.yith-wcpmr-select2-role' ).show();
                    row.find( '.yith-wcpmr-role-search' ).select2();
                    break;

                case 'membership':
                    row.find( '.yith-wcpmr-restriction-by' ).show();
                    row.find( '.yith-wcpmr-restriction-type' ).select2();
                    row.find( '.yith-wcpmr-select2-membership' ).show();
                    row.find( '.yith-wcpmr-membership-search' ).select2();
                    break;
            }
        },
    };

    var ywcpmr_bacs_table = {
        init             : function () {
            $( 'a.add' ).on( 'click', this.add_new_row );
            $( document ).on( 'click', '#yith-wcpmr-custom-fields-tab-actions-save', this.save_bacs_account );
        },
        add_new_row      : function ( e ) {
            e.preventDefault();
            $( '.yith_wcpmr_bank_accounts' ).block( { message: null, overlayCSS: { background: "#fff", opacity: .6 } } );

            var post_data = {
                index : $( 'tr.yith-wcpmr-bacs-account' ).size(),
                action: 'yith_wcpmr_add_bacs_account_row'
            }
            $.ajax( {
                type    : "POST",
                data    : post_data,
                url     : yith_wcpmr_admin.ajaxurl,
                success : function ( response ) {

                    $( '.yith-wcpmr-accounts' ).append( response );
                    $( '.yith_wcpmr_bank_accounts' ).unblock();
                },
                complete: function () {
                }
            } );
        },
        save_bacs_account: function ( e ) {
            e.preventDefault(),
                $( '.yith_wcpmr_bank_accounts' ).block( { message: null, overlayCSS: { background: "#fff", opacity: .6 } } );

            var post_data = {
                'account_name'  : $( "input[name='account_name[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                'account_number': $( "input[name='account_number[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                'bank_name'     : $( "input[name='bank_name[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                'sort_code'     : $( "input[name='sort_code[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                'iban'          : $( "input[name='iban[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                'bic'           : $( "input[name='bic[]']" ).map( function () {
                    return $( this ).val();
                } ).get(),
                action          : 'yith_wcpmr_save_bacs_account'
            };

            $.ajax( {
                type    : "POST",
                data    : post_data,
                url     : yith_wcpmr_admin.ajaxurl,
                success : function ( response ) {
                    $( '.yith_wcpmr_bank_accounts' ).unblock();
                },
                complete: function () {
                }
            } );
        }
    }

    ywcpmr_rule_metabox.init();
    ywcpmr_bacs_table.init();


} );
