*************************
TYPO3 Flow meets Ember.js
*************************

This package provides an easy way to implement a web application that uses TYPO3 Flow (php framework) as your server backend and Ember.js as your frontend. It mainly provides you with two major features:

* Super fast way to create a REST API (that complies to Ember conventions)

	* by adding annotations to the respective Flow domain models or 
	* by adding the model to the Ember.yaml configuration file

* Scaffolding / Kickstarting mechanism to

	* kickstart EPF models from Flow model semantics
	* kickstart all other components needed to build a basic CRUD app (Flow controllers, Flow route configuration, Ember routes, Ember controllers, Ember Views, Handlebars templates)
	  

Quickstart
==========

* Do all the `Flow setup stuff <http://docs.typo3.org/flow/TYPO3FlowDocumentation/Quickstart/Index.html>`_ and create a new Flow package with domain models
* Add this package to your package's composer dependencies (package name: mmitasch/flow4ember) and install it. 
* Add the Ember.Resource Annotation to the models for which a REST API endpoint should be provided
* Kickstart the full CRUD app through: ``./flow ember:all YourCompany.PackageName``
* Be happy!

**Checkout the Getting Started guide for a more detailed description.**


Which Ember persistence layers are supported?
=============================================

The current REST implementation (EpfRestController + EpfSerializer) supports:

* **EPF** (Ember Persistence Foundation)
* **Ember Data** (up to version 0.13)

The scaffolding mechanism supports:

* EPF only
  
In the future we plan to support current Ember Data and JSON API formats for the REST API. The possibilities for Ember Data scaffolding will be investigated.


More documentation
==================

Check out the other documentations on:

* **Getting Started:** Step by step instructions how to start a project based on this package.
* **Commands:** An overview of the command line functions.
* **Configuration:** An overview of the configuration options through annotations and Ember.yaml
* **Architecture:** A description of the structure of this package


Current Limitations
===================

Currently only the following model associations are supported:

    * Uni-directional OneToOne 
    * Bi-directional OneToMany (persisting through owner side - where inversedBy is defined)

Many-To-Many associations are not yet supported by any Ember persistence layer, but can in the meantime be modeled through a transitional model (that has a One-To-Many association to the other two models)

The other limitations have to do with the way doctrine enables persisting of more complex graphs of models. The normal solution would be to explicitly call methods of the repository. As this package tries to solve this in a more generic way the current limitation exists. This will be solved in the future.


Roadmap
=======

* Better error handling (instead of throwing exceptions return json format with errors that could be used by the Ember app)
* Wildcard model config
* Ember Data/JSONAPI support
* Merge strategies for kickstart commands
* Proper Test Coverage
* Refactor of property mapping and persisting strategy

	* support more association types
	* possibility to save OneToMany Association from the One (parent) side (currently due to doctrine internals only possible through Many [child] side generically; currently this is no problem as EPF expects the same behavior; but it would be great to be able to do it from both association sides)
