<?php
	namespace Controller\Core;

	use Core\Controller\Controller;

	class Admin extends Controller
	{
		public function __construct( $args = array() )
		{
			parent::__construct( $args );

			$this->view = "View\\Admin\\Index";
		}

		public function GetData()
		{
			return array();
		}
	}