<?php
	namespace Controller\Core;

	use Core\Controller\Controller;
	use Core\Db\Type;
	use Core\Form\Form;

	class Edit extends Controller
	{
		public function __construct( $args = array(), $model )
		{
			parent::__construct( $args, $model );

			$this->Post();

			$this->view = "View\\Admin\\Edit";
		}

		public function GetData()
		{
			$this->model->select->WhereEquals( Type::Int, "id", $this->args["id"] );

			return array(
				"form" => new Form( $this->model )
			);
		}

		public function Post()
		{
			if( !isset( $_POST ) || count( $_POST ) <= 0 )
				return;

			foreach( $_POST AS $name => $value )
			{
				foreach( $this->model->Fields() AS $field )
				{
					if( $field[1] == $name && $name != "id" )
					{
						$this->model->update->Field( $field[0], $field[1], $value );
					}
				}
			}

			$this->model->update->WhereEquals( Type::Int, "id", $this->args["id"] );
			$this->model->update->Save();

			header( "Location: ".$this->args["id"] );
			exit();
		}
	}