<?php
	namespace Core\Db;

	class Get extends Core
	{
		protected $fields;
		protected $limit;
		protected $order;
		protected $where;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct( $table, $alias = "" )
		{
			parent::__construct( $table, $alias );

			$this->fields =
			$this->order =
			$this->where = array();
		}

		/**
		 * @param int $dbType Type::*
		 * @param string $field
		 * @param string $alias
		 * @return $this
		 */
		public function Field( $dbType, $field, $alias = "", $args = array() )
		{
			$this->fields[] = array( $dbType, $field, $alias, $args );
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

		/**
		 * @param int $type
		 * @param string $field
		 * @param mixed $value
		 * @return $this
		 */
		public function WhereEquals( $type, $field, $value )
		{
			$this->where[] = array( "=", $type, $field, $value );
			return $this;
		}

		/**
		 * @param string $field
		 * @param bool $desc
		 * @return $this
		 */
		public function OrderBy( $field, $desc = false )
		{
			$this->order = array( $field, $desc );
			return $this;
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
						$data[$key][$name] = Type::ProcessOutput( $type, $row[$name] );
					}
				}
			}

			return $data;
		}

		/**
		 * @return mixed
		 */
		public function QueryGetValue()
		{
			$data = $this->QueryGetData();

			if( count( $data ) > 0 )
				foreach( $data[0] AS $column )
					return $column;

			return NULL;
		}

		private function QueryArray()
		{
			$data = array();
			$resource = $this->Query();

			if( fatcat_db_type == "mysqli" )
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

		private function QueryAssoc()
		{
			$data = array();
			$resource = $this->Query();

			if( fatcat_db_type == "mysqli" )
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
		 * @return string|void
		 */
		protected function ToSQL(){}
	}