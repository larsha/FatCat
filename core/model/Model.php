<?php
	namespace Core\Model;

	class Model
	{
		protected $id;
		protected $fields;

		/**
		 * @return array
		 */
		public final function Fields()
		{
			return $this->fields;
		}

		/**
		 * @param int $type Core\Db\Type
		 * @param string $name
		 * @param array $args
		 * @return $this
		 */
		protected final function Add( $type, $name, $length, $args = array() )
		{
			$this->fields[] = func_get_args();
			return $this;
		}
	}