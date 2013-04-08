<?php
	namespace Core\Db;

	class Get extends Core
	{
		protected $fields;
		protected $limit;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct( $table, $alias = "" )
		{
			parent::__construct( $table, $alias );

			$this->fields = array();
		}

		/**
		 * @param int $dbType Type::*
		 * @param string $field
		 * @param string $alias
		 * @return $this
		 */
		public function Field( $dbType, $field, $alias = "" )
		{
			$this->fields[] = array( $dbType, $field, $alias );
			return $this;
		}

		/**
		 * @param $count
		 * @return $this
		 */
		public function Limit( $count )
		{
			$this->limit = intval( $count );

			return $this;
		}

		public function QueryArray()
		{
			$data = array();
			$resource = $this->Query();

			if( ninja_db_type == "mysqli" )
			{
				while( $row = mysqli_fetch_array( $resource ) )
					$data[] = $row;
			}
			else
			{
				while( $row = sqlite_fetch_array( $resource ) )
					$data[] = $row;
			}

			return $data;
		}

		public function QueryAssoc()
		{
			$data = array();
			$resource = $this->Query();

			if( ninja_db_type == "mysqli" )
			{
				while( $row = mysqli_fetch_assoc( $resource ) )
					$data[] = $row;
			}
			else
			{
				while( $row = sqlite_fetch_array( $resource, SQLITE_ASSOC ) )
					$data[] = $row;
			}

			return $data;
		}

		/**
		 * @return array
		 */
		public function QueryGetData()
		{
			$data = array();
			foreach( $this->QueryAssoc() AS $key => $row )
			{
				foreach( $this->fields AS $field )
				{
					list( $type, $name ) = $field;

					if( array_key_exists( $name, $row ) )
					{
						$data[$key][$name] = $this->ProcessData( $type, $row[$name] );
					}
				}
			}

			return $data;
		}

		/**
		 * @param int $type
		 * @param mixed $value
		 * @return mixed
		 */
		private function ProcessData( $type, $value )
		{
			switch( $type )
			{
				case Type::Bool: 	return (bool)$value;
				case Type::Int: 	return intval( $value );
				case Type::String: 	return htmlentities( $value );
				default: 			throw new \ErrorException( "Type not found. Use Core\\Db\\Type::*" );
			}
		}

		/**
		 * @return string|void
		 */
		protected function ToSQL(){}
	}