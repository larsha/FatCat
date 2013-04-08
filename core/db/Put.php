<?php
	namespace Core\Db;

	class Put extends Core
	{
		protected function ToSQL(){}

		protected function ProcessData( $type, $value )
		{
			switch( $type )
			{
				case Type::Int:		return intval( $value );
				case Type::String:	return "'".trim( mysql_real_escape_string( $value ) )."'";
				case Type::Bool:	return (bool)$value;
				default: 			throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}
	}