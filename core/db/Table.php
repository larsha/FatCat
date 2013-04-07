<?php
	namespace Core\Db;

	class Table extends Put
	{
		private $fields;

		public function __construct( $table, $alias = "" )
		{
			parent::__construct( $table, $alias );

			$this->fields = array();
		}

		/**
		 * @param int $dbType Type::*
		 * @param string $field
		 * @return $this
		 */
		public function Field( $dbType, $field, $length )
		{
			$this->fields[] = array( $dbType, $field, $length );
			return $this;
		}

		public function ToSql()
		{
			return "CREATE TABLE ".$this->table[0]." (".$this->GenerateFields().")";
		}

		/**
		 * @return string
		 */
		private function GenerateFields()
		{
			$fields = array( "id INTEGER PRIMARY KEY" );

			foreach( $this->fields AS $item )
			{
				list( $dbType, $field, $length ) = $item;

				$sql = $field;

				switch( $dbType )
				{
					case Type::Int:		$sql .= " INTEGER"; break;
					case Type::String:	$sql .= " VARCHAR($length)"; break;
					case Type::Bool:	$sql .= " BOOLEAN"; break;
				}

				$fields[] = $sql;
			}

			return implode( ", ", $fields );
		}
	}