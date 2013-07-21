<?php
	namespace Core\Db;

	class Update extends Get
	{
		public function Save()
		{
			$this->Query();
		}

		/**
		 * @return string
		 */
		protected function ToSQL()
		{
			list( $table, $alias ) = $this->table;

			return "UPDATE $table SET ".$this->GenerateFields().$this->GenerateWhere();
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

				$fields[] = $field." = ".Type::ProcessInput( $dbType, $value );
			}

			return implode( ", ", $fields );
		}

		private function GenerateWhere()
		{
			$wheres = array();

			foreach( $this->where AS $where )
			{
				list( $delimiter, $type, $field, $value ) = $where;

				$wheres[] = $field." $delimiter ".Type::ProcessInput( $type, $value );
			}

			return ( count( $wheres ) > 0 ) ? " WHERE ".implode( " AND ", $wheres ) : "";
		}
	}