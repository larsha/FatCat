<?php
	namespace Core\Db;

	class Select extends Get
	{
		protected function ToSQL()
		{
			list( $table, $alias ) = $this->table;

			$sql = "SELECT ".$this->GenerateFields()." FROM $table";

			if( $alias )
				$sql .= " AS $alias";

			$sql .= " WHERE 1 = 1";

			if( $this->limit > 0 )
				$sql .= " LIMIT ".$this->limit;

			return $sql;
		}

		/**
		 * @return string
		 */
		private function GenerateFields()
		{
			$fields = array();

			foreach( $this->fields AS $item )
			{
				list( $dbType, $field, $alias ) = $item;

				$sql = $field;

				if( $alias )
					$sql .= " AS $alias";

				$fields[] = $sql;
			}

			if( count( $fields ) <= 0 )
				return "*";

			return implode( ", ", $fields );
		}
	}