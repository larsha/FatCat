<?php
	namespace Core\Db;

	use Core\Model\Model;

	class Table extends Put
	{
		/**
		 * @param string $table
		 * @return bool
		 */
		public static function Exists( $table )
		{
			if( ninja_db_type == "mysqli" )
			{
				$data = Db::Select( "information_schema.tables" )
						->Field( Type::Raw, "COUNT(*)" )
						->WhereEquals( Type::String, "table_schema", ninja_db_name )
						->WhereEquals( Type::String, "table_name", $table )
						->QueryGetValue();

				return ( $data > 0 ) ? True : False;
			}
			elseif( ninja_db_type == "mysqli" )
			{
				$data = Db::Select( "sqlite_master" )
						->Field( Type::String, "name" )
						->WhereEquals( Type::String, "type", "table" )
						->WhereEquals( Type::String, "name", $table )
						->QueryGetData();
			}
			else
				throw new \ErrorException( "Database type doesn't exists." );

			return ( count( $data ) > 0 ) ? True : False;
		}

		private $fields;

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
		 * @param int $length
		 * @param string $foreignKey
		 * @return $this
		 */
		public function Field( $dbType, $field, $length = 0, $foreignKey = "" )
		{
			$this->fields[] = array( $dbType, $field, $length, $foreignKey );
			return $this;
		}

		/**
		 * @return string
		 */
		public function ToSql()
		{
			return "CREATE TABLE ".$this->table[0]." (".$this->GenerateFields().")";
		}

		/**
		 * @return string
		 */
		private function GenerateFields()
		{
			$fields = array();
			foreach( $this->fields AS $item )
			{
				list( $dbType, $field, $length, $foreignKey ) = $item;

				$sql = Core::Identifier( $field );

				if( $length <= 0 )
				{
					switch( $dbType )
					{
						case Type::Int:			$length = 11; break;
						case Type::String:		$length = 255; break;
					}
				}

				switch( $dbType )
				{
					case Type::Int:			$sql .= " INTEGER"; break;
					case Type::Bool:		$sql .= " BOOLEAN"; break;
					case Type::String:		$sql .= " VARCHAR($length)"; break;
					case Type::Text:		$sql .= " TEXT"; break;
					case Type::Date:		$sql .= " DATE"; break;
					case Type::DateTime:	$sql .= " DATETIME"; break;
					default: 			throw new \ErrorException( "Type not found in Core\\Db\\Table." );
				}

				// ID is always primary key
				if( $field == "id" )
					$sql .= " PRIMARY KEY";

				// Foreign key
				if( $foreignKey )
				{
					try
					{
						/** @var $class Model */
						$class = new $foreignKey();

						$sql .= ", FOREIGN KEY(".Core::Identifier( $field ).") REFERENCES ".Core::Identifier( $class->GetTableName() )."(id)";

						$class->GetTableName();
					}
					catch( \Exception $e ){}
				}

				$fields[] = $sql;
			}

			return implode( ", ", $fields );
		}
	}