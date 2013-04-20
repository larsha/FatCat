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
			switch( ninja_db_type )
			{
				case "mysqli":	$this->resource = mysql_connect( ninja_db_server, "", "" );
								mysql_select_db( ninja_db_name, $this->resource ); break;

				case "sqlite": 	$this->resource = sqlite_open( ninja_db_name, 0666, $error ); break;

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