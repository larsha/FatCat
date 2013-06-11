<?php
	namespace Controller\Core;

	use Core\Controller\Controller;

	class Data extends Controller
	{
		public function __construct( $args = array() )
		{
			parent::__construct( $args );

			$this->view = "View\\Admin\\Data";
		}

		public function GetData()
		{
			return array();
		}
	}