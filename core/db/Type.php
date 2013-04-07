<?php
	namespace Core\Db;

	class Type
	{
		const Int 		= 1;
		const String 	= 2;
		const Bool 		= 3;

		/**
		 * @param int $type
		 * @param mixed $value
		 * @return mixed
		 * @throws \ErrorException
		 */
		public static function Decode( $type, $value )
		{
			switch( $type )
			{
				case self::Int:		return intval( $value );
				case self::String:	return htmlentities( $value ); // TODO: Is this correct?
				case self::Bool:	return (bool)$value;
				default:			throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}

		/**
		 * @param int $type
		 * @param mixed $value
		 * @return mixed
		 * @throws \ErrorException
		 */
		public static function Encode( $type, $value )
		{
			switch( $type )
			{
				case self::Int:		return intval( $value );
				case self::String:	return "'".trim( mysql_real_escape_string( $value ) )."'";
				case self::Bool:	return (bool)$value;
				default:			throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}
	}