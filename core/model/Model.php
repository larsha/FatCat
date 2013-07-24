<?php
	namespace Core\Model;

	use Core\Db\Core;
	use Core\Db\Insert;
	use Core\Db\Select;
	use Core\Db\Type;
	use Core\Db\Update;

	class Model
	{
		public static function GetModelsHierarchy()
		{
			if( !function_exists( __NAMESPACE__."\\listModels" ) )
			{
				function listModels( $path, &$hierarchy = array(), $folder = "" )
				{
					foreach( scandir( $path ) AS $dir )
					{
						if( $dir == "." || $dir == ".." || $dir == ".gitignore" )
							continue;

						if( is_dir( $path."/".$dir ) )
						{
							$hierarchy[$dir] = array();
							listModels( $path."/".$dir, $hierarchy, $dir );
						}
						else
							$hierarchy[$folder][] = str_replace( ".php", "", $dir );
					}

					return $hierarchy;
				}
			}

			return listModels( fatcat_root_dir."model" );
		}

		private $fields;
		protected $id;
		public $insert;
		public $select;
		public $update;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct()
		{
			$this->fields = array();

			$this->select = new Select( self::GetTableName() );
			$this->insert = new Insert( self::GetTableName() );
			$this->update = new Update( self::GetTableName() );

			$this->Field( Type::Int, "id", NULL, array( "auto_increment" => true ) );
		}

		/**
		 * @param int $type
		 * @param string $field
		 * @param mixed $value
		 * @param mixed $args
		 */
		public final function Field( $type, $field, $value = NULL, $args = NULL )
		{
			$this->fields[$field] = array( $type, $field, $value, $args );
			$this->select->Field( $type, $field, "", $args );

			if( $field != "id" )
			{
				$this->insert->Field( $type, $field, $value );
				$this->update->Field( $type, $field, $value );
			}
		}

		/**
		 * @return array
		 */
		public final function Fields()
		{
			return $this->fields;
		}

		/**
		 * @return string
		 */
		public final static function GetClassHierarchy()
		{
			return explode( "\\", get_called_class() );
		}

		/**
		 * @return string
		 */
		public final static function GetTableName()
		{
			list( $catalog, $namespace, $class ) = self::GetClassHierarchy();

			return strtolower( $namespace )."_".strtolower( $class );
		}

		/**
		 * @return bool
		 */
		public function IsForeign()
		{
			foreach( $this->fields AS $field )
			{
				list( $type, $name, $value, $args ) = $field;

				if( isset( $args["foreign_key"] ) )
					return true;
			}

			return false;
		}

		/**
		 * @return int
		 */
		public function Save()
		{
			if( $this->fields["id"][2] > 0 )
			{
				$query = $this->update;
				$query->WhereEquals( Type::Int, "id", $this->fields["id"][2] );
			}
			else
			{
				$query = $this->insert;
			}

			$query->Save();

			return ( $this->fields["id"][2] > 0 ) ? $this->fields["id"][2] : Core::LastInsertId();
		}
	}