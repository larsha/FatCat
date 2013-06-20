<?php
	namespace Core\Db;

	class Connect
	{
		static $instance;

		/**
		 * @return self
		 */
		public static function Instance()
		{
			if( isset( self::$instance ) )
				return self::$instance;

			$class = __CLASS__;
			self::$instance = new $class;

			return self::$instance;
		}

		private $queries;
		private $resource;

		public function Initialize()
		{
			switch( fatcat_db_type )
			{
				case "mysqli":	$this->resource = mysqli_connect( fatcat_db_server, fatcat_db_user, fatcat_db_password );
								mysqli_select_db( $this->resource, fatcat_db_name ); break;

				case "sqlite": 	$this->resource = sqlite_open( fatcat_db_name, 0666, $error ); break;

				default: 		throw new \ErrorException( "No database type chosen. Error in settings file." );
			}
		}

		/**
		 * @param string $query
		 */
		public function AddQuery( $query )
		{
			$this->queries[] = $query;
		}

		/**
		 * @return array
		 */
		public function GetQueries()
		{
			return $this->queries;
		}

		public function GetResource()
		{
			return $this->resource;
		}

		private function __construct()
		{
			$this->queries = array();
		}
	}