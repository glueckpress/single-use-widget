/**
 * Disable widget when already in use.
 * @author kraftner
 * @link https://wordpress.stackexchange.com/questions/287183/can-a-widget-in-the-customizer-be-single-use-i-e-disabled-after-1-instance-h/287518#287518
 */
(function() {
	wp.customize.bind( 'ready', function() {

		var api = wp.customize,
			widgetId = singleUseWidget.idBase,
			widget = wp.customize.Widgets.availableWidgets.findWhere( { id_base: widgetId } );

		/**
		 * Counts how often a widget is used based on an array of Widget IDs.
		 *
		 * @param widgetIds
		 * @returns {number}
		 */
		var countWidgetUses = function( widgetIds ){

			var widgetUsedCount = 0;

			widgetIds.forEach(function(id){

				if( id.indexOf( widgetId ) == 0 ){
					widgetUsedCount++;
				}

			});

			return widgetUsedCount;

		};

		var isSidebar = function( setting ) {
			return (
				0 === setting.id.indexOf( 'sidebars_widgets[' )
				&&
				setting.id !== 'sidebars_widgets[wp_inactive_widgets]'
			);
		};

		var updateState = function(){

			// Enable by default ...
			widget.set('is_disabled', false );

			api.each( function( setting ) {
				if ( isSidebar( setting ) ) {
					// ... and disable as soon as we encounter any usage of the widget.
					if( countWidgetUses( setting.get() ) > 0 ) widget.set('is_disabled', true );
				}
			} );

		};

		/**
		 * Listen to changes to any sidebar.
		 */
		api.each( function( setting ) {
			if ( isSidebar( setting ) ) {
				setting.bind( updateState );
			}
		} );

		updateState();

	});
})( jQuery );
