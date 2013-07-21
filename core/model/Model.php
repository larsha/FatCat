<?php
	namespace Core\Model;

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

			$this->Field( Type::Int, "id" );
		}

		/**
		 * @param int $type
		 * @param mixed $field
		 * @param mixed $args
		 */
		public final function Field( $type, $field, $args = NULL )
		{
			$this->fields[] = array( $type, $field, $args );
			$this->insert->Field( $type, $field, $args );
			$this->select->Field( $type, $field, "", $args );

			if( $field != "id" )
				$this->update->Field( $type, $field, $args );
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
	}