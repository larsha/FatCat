<?php
	namespace Core\Db;

	abstract class Core
	{
		public static function Identifier( $string )
		{
			switch( ninja_db_type )
			{
				case "mysqli": 	return "`$string`";
				case "sqlite": 	return "\"$string\"";
				default: 		throw new \ErrorException( "Database type is not defined in settings.php." );
			}
		}

		protected $table;
		private $queries;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct( $table, $alias = "" )
		{
			$this->table = array( $table, $alias );
			$this->queries = array();
		}

		/**
		 * @return void
		 */
		public function Debug()
		{
			echo $this->ToSQL();
		}

		/**
		 * @return resource
		 */
		public function Query()
		{
			Connect::Instance()->AddQuery( $this->ToSQL() );

			switch( ninja_db_type )
			{
				case "mysqli":	return mysql_query( $this->ToSQL(), Connect::Instance()->GetResource() );
				case "sqlite":	return sqlite_query( Connect::Instance()->GetResource(), $this->ToSQL() );
				default: 		throw new \ErrorException( "Database type is not defined in settings.php." );
			}
		}

		/**
		 * @return string
		 */
		abstract protected function ToSQL();
	}