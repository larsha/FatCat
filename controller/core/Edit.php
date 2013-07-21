<?php
	namespace Controller\Core;

	use Core\Controller\Controller;
	use Core\Db\Type;
	use Core\Form\Form;

	class Edit extends Controller
	{
		public function __construct( $args = array() )
		{
			parent::__construct( $args );

			$this->view = "View\\Admin\\Edit";
		}

		public function GetData()
		{
			$this->model->select->WhereEquals( Type::Int, "id", $this->args["id"] );

			return array(
				"form" => new Form( $this->model )
			);
		}
	}