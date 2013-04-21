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

			$sql .= $this->GenerateWhere();

			if( $this->limit > 0 )
				$sql .= " LIMIT ".$this->limit;

			if( count( $this->order ) == 2 )
				$sql .= " ORDER BY ".$this->order[0].( $this->order[1] ? " DESC" : " ASC" );

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

				$sql = Core::Identifier( $field );

				if( $alias )
					$sql .= " AS $alias";

				$fields[] = $sql;
			}

			if( count( $fields ) <= 0 )
				return "*";

			return implode( ", ", $fields );
		}

		private function GenerateWhere()
		{
			$wheres = array();

			foreach( $this->where AS $where )
			{
				list( $delimiter, $type, $field, $value ) = $where;

				$wheres[] = Core::Identifier( $field )." $delimiter ".Type::ProcessInput( $type, $value );
			}

			return ( count( $wheres ) > 0 ) ? " WHERE ".implode( " AND ", $wheres ) : "";
		}
	}