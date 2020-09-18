( function( $ ) {
    var wcb2b_groups = JSON.parse( wcb2b_groups_parameters.wcb2b_groups );
    if ( wcb2b_groups.length == 0 ) {
        $( 'select option[value="wcb2b-assign_group-action"]' ).attr( 'disabled', 'disabled' );
    }

    $( '#bulk-action-selector-top, #bulk-action-selector-bottom' ).on( 'change', function( e ) {
        var $this = $( this );

        if ( $this.val() == 'wcb2b-assign_group-action' ) {
            var select = $( '<select>', {
                name: 'wcb2b_group'
            } ).addClass( 'wcb2b_group-elements' );

            $.each( wcb2b_groups, function( index, group ) {
                select.append( $( '<option></option>' ).attr( 'value', index ).text( group ) );
            } );

            $this.after( select );
        } else {
            $( '.wcb2b_group-elements' ).remove();
        }
    } );
} )( jQuery );