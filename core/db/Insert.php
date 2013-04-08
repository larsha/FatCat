<?php
	namespace Core\Db;


	class Insert extends Put
	{
		private $fields;

		/**
		 * @param string $table
		 */
		public function __construct( $table )
		{
			parent::__construct( $table );

			$this->fields = array();
		}

		/**
		 * @param int $dbType
		 * @param string $field
		 * @param mixed $value
		 * @return $this
		 */
		public function Field( $dbType, $field, $value )
		{
			$this->fields[] = array( $dbType, $field, $value );
			return $this;
		}

		/**
		 * @return string
		 */
		protected function ToSQL()
		{
			list( $table, $alias ) = $this->table;

			return "INSERT INTO $table (".$this->GenerateFields().") VALUES (".$this->GenerateValues().")";
		}

		/**
		 * @return string
		 */
		private function GenerateFields()
		{
			$fields = array();

			foreach( $this->fields AS $item )
			{
				list( $dbType, $field, $value ) = $item;

				$fields[] = $field;
			}

			return implode( ", ", $fields );
		}

		private function GenerateValues()
		{
			$fields = array();

			foreach( $this->fields AS $item )
			{
				list( $dbType, $field, $value ) = $item;

				$fields[] = $this->ProcessData( $dbType, $value );
			}

			if( count( $fields ) <= 0 )
				return "*";

			return implode( ", ", $fields );
		}
	}