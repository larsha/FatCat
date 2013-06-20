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

		const Raw 		= 99;

		/**
		 * @param int $type Type::*
		 * @param mixed $value
		 * @return mixed
		 * @throws \ErrorException
		 */
		public static function ProcessInput( $type, $value )
		{
			// Strings are handled different based on database type
			if( $type == self::String || $type == self::Text )
			{
				switch( fatcat_db_type )
				{
					case "mysqli": 	return "'".trim( mysqli_real_escape_string( Connect::Instance()->GetResource(), $value ) )."'";
					case "sqlite": 	return "'".trim( sqlite_escape_string( $value ) )."'";
				}
			}
			// Validate date
			elseif( $type == self::Date )
			{
				list( $year, $month, $day ) = explode( "-", $value );

				if( checkdate( $month, $day, $year ) )
					return "'".$value."'";
			}

			switch( $type )
			{
				case self::Bool:		return (bool)$value;
				case self::DateTime:	return "'".$value."'"; // TODO: Should this be handled?
				case self::Int:			return intval( $value );
				case self::Raw:			return $value;
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
				case self::Bool: 		return (bool)$value;
				case self::Date:		return $value;
				case self::DateTime:	return $value; // TODO: Should this be handled?
				case self::Int: 		return intval( $value );
				case self::String:
				case self::Text: 		return htmlentities( $value );
				case self::Raw: 		return $value;
				default: 				throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}
	}