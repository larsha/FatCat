<?php
	namespace Core\Model;

	use Core\Db\Select;
	use Core\Db\Type;

	class Model extends Select
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
							$hierarchy[$folder][] = $dir;
					}

					return $hierarchy;
				}
			}

			return listModels( ninja_root_dir."model" );
		}

		protected $id;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct()
		{
			parent::__construct( self::GetTableName() );

			$this->Field( Type::Int, "id" );
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
		final public static function GetClassHierarchy()
		{
			return explode( "\\", get_called_class() );
		}

		/**
		 * @return string
		 */
		final public static function GetTableName()
		{
			list( $catalog, $namespace, $class ) = self::GetClassHierarchy();

			return strtolower( $namespace )."_".strtolower( $class );
		}
	}