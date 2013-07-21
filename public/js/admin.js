var navigation = jQuery( "#navigation" );

jQuery( "#site-container" ).css(
	{
		minHeight: parseInt( navigation.outerHeight() ) + parseInt( navigation.css( "top" ) ) + "px"
	}
);