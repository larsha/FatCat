<?php
	namespace Core\Db;

	class Type
	{
		const Int 		= 1;
		const String 	= 2;
		const Bool 		= 3;
		const Text 		= 4;
		const Date 		= 5;
		const DateTime 	= 6;

		/**
		 * @param int $type Type::*
		 * @param mixed $value
		 * @return mixed
		 * @throws \ErrorException
		 */
		public static function ProcessInput( $type, $value )
		{
			switch( $type )
			{
				case Type::Bool:		return (bool)$value;
				case Type::Date:		return $value; // TODO: Should this be handled?
				case Type::DateTime:	return $value; // TODO: Should this be handled?
				case Type::Int:			return intval( $value );
				case Type::String:
				case Type::Text:		return "'".trim( mysql_real_escape_string( $value ) )."'";
				default: 				throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}

		/**
		 * @param int $type Type::*
		 * @param mixed $value
		 * @return mixed
		 * @throws \ErrorException
		 */
		public static function ProcessOutput( $type, $value )
		{
			switch( $type )
			{
				case Type::Bool: 		return (bool)$value;
				case Type::Date:		return $value; // TODO: Should this be handled?
				case Type::DateTime:	return $value; // TODO: Should this be handled?
				case Type::Int: 		return intval( $value );
				case Type::String:
				case Type::Text: 		return htmlentities( $value );
				default: 				throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}
	}