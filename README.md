PHP Ninja framework
===================
Light weight, easy to use and fast progress with your projects.

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
