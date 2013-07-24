<?php
	namespace Core\Db;

	use Core\Model\Model;

	class Table extends Put
	{
		/**
		 * @return string
		 * @throws \ErrorException
		 */
		private static function AutoIncrement()
		{
			switch( fatcat_db_type )
			{
				case "mysqli": return "AUTO_INCREMENT";
				case "sqlite": return "";
				default: throw new \ErrorException( "Database type doesn't exists." );
			}
		}

		/**
		 * @param string $table
		 * @return bool
		 */
		public static function Exists( $table )
		{
			if( fatcat_db_type == "mysqli" )
			{
				$data = Db::Select( "information_schema.tables" )
						->Field( Type::Raw, "COUNT(*)" )
						->WhereEquals( Type::String, "table_schema", fatcat_db_name )
						->WhereEquals( Type::String, "table_name", $table )
						->QueryGetValue();

				return ( $data > 0 ) ? True : False;
			}
			elseif( fatcat_db_type == "sqlite" )
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
		 * @param array $args
		 * @return $this
		 */
		public function Field( $dbType, $field, $args = array() )
		{
			$this->fields[] = array( $dbType, $field, $args );
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
				list( $dbType, $field, $args ) = $item;

				$length = isset( $args["length"] ) ? $args["length"] : 0;
				$foreignKey = isset( $args["foreign_key"] ) ? $args["foreign_key"] : "";
				$autoIncrement = isset( $args["auto_increment"] ) ? true : false;

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

				// Add auto increment
				if( $autoIncrement )
					$sql .= " ".self::AutoIncrement();

				// Foreign key
				if( $foreignKey )
				{
					try
					{
						/** @var $class Model */
						$class = new $foreignKey();

						$sql .= ", FOREIGN KEY(".Core::Identifier( $field ).") REFERENCES ".Core::Identifier( $class->GetTableName() )."(id)";
					}
					catch( \Exception $e ){}
				}

				$fields[] = $sql;
			}

			return implode( ", ", $fields );
		}
	}