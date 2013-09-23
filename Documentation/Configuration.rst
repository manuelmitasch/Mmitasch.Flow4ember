*************
Configuration
*************

Overview
========

There are two ways to configure your package:

1. Annotations
2. Configuration in Ember.yaml


Annotations
===========

You can use Annotation classes to configure your Flow domain models. Simply add the namespace before your class definition. ::

	 use Mmitasch\Flow4ember\Annotations as Ember; 


The following annotations can be added before the class definition:

* **Resource:** 

	* Marks a domain model as a ember resource. Thus, a REST API endpoint will be provided for this model. When scaffolding with ember:model command it will be added to the file. The ModelReflectionService will contain a Metamodel for this Flow domain model.
	* Usage ::

		/**
		 * Description of model
		 * 
		 * @Flow\Entity
		 * @Ember\Resource
		 */
		class Person {	}

* **Model:**

	* Marks a domain model as a ember model. NO REST API endpoint will be provided for this model. Thus, it can only be used in embedded associations. When scaffolding with ember:model command it will be added to the file. The ModelReflectionService will contain a Metamodel for this Flow domain model.
	* Usage ::

		/**
		 * Description of model
		 * 
		 * @Flow\Entity
		 * @Ember\Model
		 */
		class Person {	}


The following annotations can be added before a class properties:

* **Sideload:**

	* Marks an association to sideload the contained model in the same REST HTTP payload.
	* Usage ::
	  
		/**
		 * @var \Doctrine\Common\Collections\Collection<\Mmitasch\Taskplaner\Domain\Model\Task>
		 * @ORM\OneToMany(mappedBy="list")
		 * @Ember\Sideload
		 */
		protected $tasks;

* **Embedded:**
 
 	* Marks an association to embed the contained models inside the association payload.
 	* Options:

 		* type: Accepted values are "always" or "load". Thus, association content is only embedded on loading a model (read only - GET) or always (read and save - GET,POST,PUT)
 
 	* Usage ::
 	  
 	  	/**
		 * @var \Doctrine\Common\Collections\Collection<\Mmitasch\Taskplaner\Domain\Model\Task>
		 * @ORM\OneToMany(mappedBy="list")
		 * @Ember\Embedded(type="load")
		 */
		protected $tasks;




Ember.yaml options
==================

You can kickstart a basic Ember.yaml file via the command line tool.

	``./flow ember:config YourCompany.PackageName``


The kickstarted Ember.yaml file will contain something like: ::

	# Ember.yaml 
	# Configuration for YourCompany.PackageName Package

	YourCompany:
	  PackageName:
	    emberNamespace: App
	    persistenceLibrary: EPF  
	    routes:
	      rest: 'YourCompany.PackageName/rest'
	      app: 'YourCompany.PackageName'
	    restController: 'YourCompany\PackageName\Controller\EpfRestController'


	    # add custom typeConverters 
	    typeConverters: 

	    # add model configuration 
	    models:


emberNamespace
--------------
The emberNamespace configures the Ember app namespace that will be used by the scaffolding mechanism. Standard value: App


persistenceLibrary
------------------
Currently only EPF is supported as ember persistence library. This option is currently contained for forward compatibility only.


routes
------
* The ``rest`` option defines the route that will be used for the REST API. The scaffolding mechanisme uses this value when kickstarting the Routes.yaml file.
* The ``app`` option defines the route that the AppController will be available (entrypoint for the Ember app). The scaffolding mechanism uses this value when kickstarting the Routes.yaml file.


restController
-------------- 
Contains the fully-qualified class name of the RestController. The scaffolding mechanism uses this value when kickstarting the Routes.yaml file.


models
------
In the models section you can add additional configuration for your Flow domain models (fully qualified class names). Example: ::

	# Ember.yaml 
	# Configuration for Mmitasch.Taskplaner Package

	Mmitasch:
	  Taskplaner:
	    # add model configuration 
	    models:
	      'Mmitasch\Taskplaner\Domain\Model\Tasklist':
	        associations:
	          tasks:
	          	sideload: true

