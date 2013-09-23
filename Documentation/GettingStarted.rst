***************
Getting Started
***************

Install TYPO3 Flow + Composer
=============================
Follow the instructions of the TYPO3 Flow Quickstart Documentation from:
http://docs.typo3.org/flow/TYPO3FlowDocumentation/Quickstart/Index.html

Create a new package
====================
Use the package:create command of the Flow command line tool (inside your flow root directory):

	* ``./flow package:create YourCompany.PackageName``


Create some domain models
=========================
You can kickstart Flow domain models with the kickstart:model command:

	* ``./flow kickstart:model YourCompany.PackageName Person "name:string"``

Don't forget to update the databse schema with the respective doctrine:* commands (doctrine:update does the trick in development context).

If you want to provide a REST API endpoint for the model you also need to create a repository for this model: 

	* ``./flow kickstart:repository YourCompany.PackageName Person


Install this package
====================
* Clone this repositor into your <flow-root>/Packages/Application folder ::
	git clone https://github.com/manuelmitasch/Mmitasch.Flow4ember.git

* You should also add the package to your ``composer.json`` dependencies::
  
  	{
	   	"require": {
	       	"mmitasch\/flow4ember": "dev-master",
	   	}
	}

Note: As the package is not yet released as a stable version to ``packagist <https://packagist.org/>``_ you need to specify "dev-master" as the version.


Configure Models
================
There are two ways to declare a Flow domain model as a Ember model:

1. Annotations

	* Adding a Ember.Resource annotation before the class defintion will provide a REST API endpoint for this model. When scaffolding the ember model it will be included.  ::
		 /**
		  * @Ember\Resource
		  */
	* Adding a Ember.Model annotation will NOT provide a REST API endpoint for this model. When scaffolding the ember model it will be included. 
	* Note: In order to be able to use the Ember.* annotations you need to import the Annotations namespace before your class definition with: ::
		use Mmitasch\Flow4ember\Annotations as Ember;

2. Configuration in Ember.yaml

	* You can kickstart a basic Ember.yaml file via the command line tool.

			``./flow ember:config YourCompany.PackageName``

	* In the models section you can add your Flow models (fully qualified class names) ::

			 YourCompany:
			   PackageName:
			     models:
			       'YourCompany\PackageName\Domain\Model\Person'

The configuration in Ember.yaml overrides possible values from annotations.
See the Configuration section for detailed confiuration options.


Add your package routes to your Flow Routes.yaml
================================================

Add the following configuration snippet to your Flow Routes configuration at <flow-root>/Configuration/Routes.yaml to activate your packages routes: ::

  -
    name: 'PackageName'
    uriPattern: '<EmberSubroutes>'
    defaults:
      '@package': 'YourCompany.PackageName'
      '@format': 'html'
    subRoutes:
      EmberSubroutes:
        package: YourCompany.PackageName

*Substitute YourCompany.PackageName with your package key.*


Scaffold the CRUD app
=====================

You can kickstart a full Ember based CRUD app through the command line tool

	* ``./flow ember:all YourCompany.PackageName``

See the Command section for detailed description of available commands. Or use the help command through ``./flow help command:foo``. 

Note: The handlebars templates currently uses the `grunt-ember-templates task <https://github.com/dgeb/grunt-ember-templates>`_ to compile the template files into one file. Thus, make sure to have node with npm installed before kickstarting.

**See the documentation of the scaffolding mechanism in the Commands section**


Try your app
============

Visit your kickstarted app at:
``http://<flow-root-url>/YourPackage.PackageName``

You can see an overview of the configured REST resources and Flow Routes at:
``http://<flow-root-url>/YourPackage.PackageName/rest``

The configured REST resource follow this convention:
``http://<flow-root-url>/YourPackage.PackageName/rest/<resourcen-name>/<resource-identifier>``

Note: Of course all routes can simply be changed in your Routes.yaml


Finetune the kickstarted app
============================

You can find and modify the kickstarted Ember app in ``Resources/Public/Script``. 


Templates
---------

The handlebars templates are organized as seperate files in the ``Resources/Public/Script/Template/``. Currently the grunt task `grunt-ember-templates <https://github.com/dgeb/grunt-ember-templates>`_ is used to compile the templates into the file ``Resources/Public/Build/templates.js``. Thus, you need to recompile the templates after changing them. You can either start the grunt watch task with ``grunt watch`` (inside your package root) to auto-recompile them on changes. Or you can recompile them explicitly with ``grunt`` or use the Flow command alias with ``./flow ember:buildtemplates YourPackage.PackageName`` (inside your flow root).

Remember to name partial templates with a leading underscore. This underscore will be preserved in the compiled template name. For instance, post/_edit.hbs will be registered as Ember.TEMPLATES["post/_edit"].


Read more docs
==============

For a more detailed documentation please refer to the sections about Commands and Configuration. 