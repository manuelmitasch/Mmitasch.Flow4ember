***************
Getting Started
***************

Install TYPO3 Flow + Composer
=============================
Follow the instructions of the TYPO3 Flow Quickstart Documentation from:
http://docs.typo3.org/flow/TYPO3FlowDocumentation/Quickstart/Index.html

Create a new package
====================
Use the package:create command of the Flow command line tool in your flow root directory:
./flow package:create YourCompany.PackageName


Create some domain models
=========================
You can kickstart Flow domain models with the kickstart:model command:
./flow kickstart:model YourCompany.PackageName Person "name:string"


Install this package via composer
=================================
* Add this package to your composer.json dependencies::
  
  	{
	   	"require": {
	       	"mmitasch/flow4ember": "*",
	   	}
	}

* Install dependencies via ``php composer.phar install``


Configure Models
================
There are two ways to declare a Flow domain model as a Ember model:
1. Annotations

	* Adding a Ember.Resource annotation before the class defintion will provide a REST API endpoint for this model. When scaffolding the ember model it will be included. 
	* Adding a Ember.Model annotation will provide NOT provide a REST API endpoint for this model. When scaffolding the ember model it will be included. 

2. Configuration in Ember.yaml

	* You can kickstart a basic Ember.yaml file via the command line tool::
			``./flow ember:config YourCompany.PackageName``
	* In the models section you can add your Flow models (fully qualified class names)::
			 YourCompany:
			   PackageName:
			     models:
			       'YourCompany\PackageName\Domain\Model\Person'

The configuration in Ember.yaml overrides possible values from annotations.
See the Configuration section for detailed confiuration options.


Scaffold the CRUD app
=====================

You can kickstart a full Ember based CRUD app through the command line tool::
	``./flow ember:all YourCompany.PackageName``

See the Command section for detailed description of available commands. Or use the help command through ``./flow help command:foo``

