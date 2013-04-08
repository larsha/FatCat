PHP Ninja framework
===================
Lightweight, easy to use and fast progress with your projects.

Quick start
-----------

1.   Publish Ninja-folder to your favourite webserver with PHP installed.
2. 	Edit /core/settings.php
3. 	Point your webserver root to /public
4. 	Now you're ready to use the Ninja framework!

Crash course
------------

<dl>
	<dt>Model</dt>
	<dd>Models har placed in the models directory in a subfolder named by the module you are creating. The model has to be in the Model namespace (Model\Modulename) and extend the Model class from the Core\Model namespace.</dd>
	<dt>View</dt>
	<dd>Views, or template files, are placed in the view directory in a subfolder named by the module you are creating. File name depends on how you name the controller class but the file ending is always ".tpl".</dd>
	<dt>Controller</dt>
	<dd>Controller classes are placed in the controller directory in a subfolder named by the module you are creating. File name is always the same as the contained class. A controller should be in the Controller\Modulename namespace. It should also extend the Controller class from the Core\Controller namespace.</dd>
</dl>

Into the deep
=============

Models
------

Basic model example. This is a file named "article.php" and placed in /model/article/ directory. It also uses a database column called "title":

	namespace Model\Article;

	use Core\Model\Model, Core\Db\Type;

	class Article extends Model
	{
		public function __construct()
		{
			$this->Add( Type::String, "title", 255 );
		}
	}

When a model is created and fields are added or changed the /core/sync.php script has to run one time to build the database with its tables. In the example above a database table named "article_article" will be created with a column called "title" in varchar(255).

Database tables are named after the model (modelname_classname). A field named "id" will always be created.

Views
-----

<dl>
	<dt>Variables</dt>
	<dd>{{name}}</dd>
	<dt>Loop</dt>
	<dd>{{FOR items AS item}} {{ENDFOR}}</dd>
	<dt>Replace variable</dt>
	<dd>{{REPLACE name}} {{ENDREPLACE}}</dd>
	<dt>Import other template</dt>
	<dd>use View\Module\Template</dd>
</dl>

Controllers
-----------

Basic controller example. This controller loads all articles created with the model from the example above and returns an array with the loaded content.

	namespace Controller\Article;

	use Core\Controller\Controller, Core\Db\Db, Core\Db\Type;

	class Index extends Controller
	{
		public function GetData()
		{
			$data = Db::Select( "article_article" )
				->Field( Type::Int, "id" )
				->Field( Type::String, "title" )
				->QueryGetData();

			return array(
				"title" => "My list of articles",
				"articles" => $data
			);
		}
	}
