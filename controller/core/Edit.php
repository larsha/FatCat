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
			if( $this->args["id"] )
			{
				$this->model->select->WhereEquals( Type::Int, "id", $this->args["id"] );
			}

			$form = new Form( $this->model );
			$form->ExcludeField( "id" );

			return array(
				"form" => $form
			);
		}

		public function Post()
		{
			if( !isset( $_POST ) || count( $_POST ) <= 0 )
				return;

			if( $this->args["id"] )
			{
				$this->model->Field( Type::Int, "id", $this->args["id"] );
			}

			foreach( $_POST AS $name => $value )
			{
				foreach( $this->model->Fields() AS $field )
				{
					if( $field[1] == $name && $name != "id" )
					{
						$this->model->Field( $field[0], $field[1], $value, $field[3] );
					}
				}
			}

			$id = $this->model->Save();

			header( "Location: ".$id );
			exit();
		}
	}