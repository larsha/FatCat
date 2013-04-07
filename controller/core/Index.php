<?php
	namespace Controller\Core;

	use Core\Controller\Controller, Core\Db\Db, Core\Db\Type;

	class Index extends Controller
	{
		public function GetData()
		{
			return array(
				"title" => "PHP Ninja framework",
				"body" => "Light weight, easy to use and fast progress."
			);
		}
	}