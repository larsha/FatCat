<?php
	namespace Core\Db;

	class Db
	{
		/**
		 * @param string $table
		 * @return Table
		 */
		public static function Create( $table )
		{
			return new Table( $table );
		}

		/**
		 * @param string $table
		 * @return Insert
		 */
		public static function Insert( $table )
		{
			return new Insert( $table );
		}

		/**
		 * @param string $table
		 * @param string $alias
		 * @return Select
		 */
		public static function Select( $table, $alias = "" )
		{
			return new Select( $table, $alias );
		}
	}