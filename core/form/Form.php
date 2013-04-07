<?php
	namespace Core\Form;

	use Core\Db\Type;
	use Core\Model\Model;

	class Form
	{
		private $model;

		public function __construct( Model $model )
		{
			$this->model = $model;
		}

		public function Render()
		{
			foreach( $this->model->Fields() AS $field )
			{
				list( $type, $name, $length, $args ) = $field;

				switch( $type )
				{
					case Type::String:	$this->RenderInput( $name, $length, $args ); break;
				}
			}
		}

		private function RenderInput( $name, $length, $args )
		{
?>
<input type="text" name="<?=$name;?>" value="" maxlength="<?=$length;?>" />
<?
		}
	}