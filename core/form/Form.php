<?php
	namespace Core\Form;

	use Core\Db\Type;
	use Core\Model\Model;

	class Form
	{
		private $data;
		private $exclude;
		private $form;
		private $model;

		/**
		 * @param Model $model
		 */
		public function __construct( Model $model )
		{
			$this->exclude =
			$this->form = array();
			$this->model = $model;

			$this->data = $this->model->select->QueryGetSingleRow();

			if( $this->data["id"] <= 0 )
				$this->data = array();
		}

		/**
		 * @param string $name
		 * @return $this
		 */
		public function ExcludeField( $name )
		{
			$this->exclude[] = $name;
			return $this;
		}

		/**
		 * @return string
		 * @throws \ErrorException
		 */
		public function Generate()
		{
			$this->form[] = '<form method="post">';

			foreach( $this->model->Fields() AS $field )
			{
				list( $type, $name, $value, $args ) = $field;

				if( in_array( $name, $this->exclude ) )
					continue;

				$foreignKey = isset( $args["foreign_key"] ) ? $args["foreign_key"] : "";
				$length = isset( $args["length"] ) ? $args["length"] : 0;
				$value = isset( $this->data[$name] ) ? $this->data[$name] : "";

				$foreign = ( $foreignKey ) ? new $foreignKey : NULL;

				$this->form[] = '<fieldset>';
				$this->form[] = '<label>'.$name.'</label>';

				if( $foreign instanceof Model )
				{
					$this->GenerateSelect( $name, $foreign->select->QueryGetSingleColumn( "id" ) );
				}
				else
				{
					switch( $type )
					{
						case Type::Bool:		$this->GenerateCheckbox( $name, $value ); break;
						case Type::Date:		$this->GenerateDate( $name, $value ); break;
						case Type::DateTime:	$this->GenerateDateTime( $name, $value ); break;
						case Type::Int:			$this->GenerateInput( $name, $value, $length ); break;
						case Type::String:		$this->GenerateInput( $name, $value, $length ); break;
						case Type::Text:		$this->GenerateText( $name, $value ); break;
						default: 				throw new \ErrorException( "Type not found in Core\\Form\\Form." );
					}
				}

				$this->form[] = '</fieldset>';
			}

			$this->form[] = '<input type="submit" class="btn btn-primary">';
			$this->form[] = '</form>';

			return implode( " ", $this->form );
		}

		/**
		 * @param string $name
		 * @param bool $value
		 * @return string
		 */
		private function GenerateCheckbox( $name, $value )
		{
			$this->form[] = '<input type="checkbox" name="'.$name.'"'.( $value ) ? ' checked="checked"' : NULL.'>';
		}

		/**
		 * @param string $name
		 * @param string $value
		 * @return string
		 */
		private function GenerateDate( $name, $value )
		{
			$this->form[] = '<input type="date" name="'.$name.'" value="'.$value.'">';
		}

		/**
		 * @param string $name
		 * @param string $value
		 * @return string
		 */
		private function GenerateDateTime( $name, $value )
		{
			$this->form[] = '<input type="datetime" name="'.$name.'" value="'.$value.'">';
		}

		/**
		 * @param string $name
		 * @param string $value
		 * @param int $length
		 * @return string
		 */
		private function GenerateInput( $name, $value, $length )
		{
			$this->form[] = '<input type="text" name="'.$name.'" value="'.$value.'"'.( ( $length > 0 ) ? ' maxlength="'.$length.'"' : NULL ).'>';
		}

		/**
		 * @param string $name
		 * @param array $values
		 */
		private function GenerateSelect( $name, $values = array() )
		{
			$this->form[] = '<select name="'.$name.'">';

			foreach( $values AS $value )
				$this->form[] = '<option>'.$value.'</option>';

			$this->form[] = '</select>';
		}

		/**
		 * @param string $name
		 * @param string $value
		 * @return string
		 */
		private function GenerateText( $name, $value )
		{
			$this->form[] = '<textarea name="'.$name.'">'.$value.'</textarea>';
		}
	}