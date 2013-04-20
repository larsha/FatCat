<?php
	namespace Core\Model;

	use Core\Db\Select;
	use Core\Db\Type;

	class Model extends Select
	{
		protected $id;

		/**
		 * @param string $table
		 * @param string $alias
		 */
		public function __construct()
		{
			parent::__construct( $this->GetTableName() );

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
		final public function GetClassHierarchy()
		{
			return explode( "\\", get_called_class() );
		}

		/**
		 * @return string
		 */
		final public function GetTableName()
		{
			list( $catalog, $namespace, $class ) = $this->GetClassHierarchy();

			return strtolower( $namespace )."_".strtolower( $class );
		}
	}