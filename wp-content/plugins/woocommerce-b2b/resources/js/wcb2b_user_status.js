( function( $ ) {
    var wcb2b_statuses = JSON.parse( wcb2b_statuses_parameters.wcb2b_statuses );
    $( '#bulk-action-selector-top, #bulk-action-selector-bottom' ).on( 'change', function( e ) {
        var $this = $( this );

        if ( $this.val() == 'wcb2b-change_status-action' ) {
            var select = $( '<select>', {
                name: 'wcb2b_status'
            } ).addClass( 'wcb2b_status-elements' );

            $.each( wcb2b_statuses, function( index, status ) {
                select.append( $( '<option></option>' ).attr( 'value', index ).text( status ) );
            } );

            $this.after( select );
        } else {
            $( '.wcb2b_status-elements' ).remove();
        }
    } );
} )( jQuery );