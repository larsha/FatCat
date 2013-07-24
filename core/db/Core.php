<?php
	namespace Core\Db;

	abstract class Core
	{
		public static function Identifier( $string )
		{
			switch( fatcat_db_type )
			{
				case "mysqli": 	return "`$string`";
				case "sqlite": 	return "\"$string\"";
				default: 		throw new \ErrorException( "Database type is not defined in settings.php." );
			}
		}

		/**
		 * @return int
		 */
		public static function LastInsertId()
		{
			switch( fatcat_db_type )
			{
				case "mysqli": 	return mysqli_insert_id( Connect::Instance()->GetResource() );
				case "sqlite": 	return sqlite_last_insert_rowid( Connect::Instance()->GetResource() );
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

			switch( fatcat_db_type )
			{
				case "mysqli":	$resource = mysqli_query( Connect::Instance()->GetResource(), $this->ToSQL() ) or die( mysqli_error( Connect::Instance()->GetResource() ) ); break;
				case "sqlite":	$resource = sqlite_query( Connect::Instance()->GetResource(), $this->ToSQL() ); break;
				default: 		throw new \ErrorException( "Database type is not defined in settings.php." );
			}

			if( $resource == false )
				throw new \ErrorException( "Something went wrong with the database query. Check logs for info." );

			return $resource;
		}

		/**
		 * @return string
		 */
		abstract protected function ToSQL();
	}