<?php
	function __autoload( $class )
	{
		$path = str_replace( "\\", "/", $class );
		$path = "../$path.php";

		if( file_exists( $path ) )
		{
			require_once( $path );
		}
		else
			throw new ErrorException( "No class with name $class found." );
	}