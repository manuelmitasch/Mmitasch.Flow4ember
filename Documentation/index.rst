*************************
TYPO3 Flow meets Ember.js
*************************

This package provides an easy way to implement a web application that uses TYPO3 Flow (php framework) as your server backend and Ember.js as your frontend. It mainly provides you with two major features:

* Super fast way to create a REST API  

	* by adding annotations to the respective Flow domain models or 
	* by adding the model to the Ember.yaml configuration file

* Scaffolding / Kickstarting mechanism to

	* kickstart EPF models from Flow model semantics
	* kickstart all other components needed to build a basic CRUD app (Flow controllers, Flow route configuration, Ember routes, Ember controllers, Ember Views, Handlebars templates)
	  

Quickstart
==========
* Do all the boring setup stuff and create a new Flow package with domain models
* Add the Ember.Resource Annotation to the models for which a REST API endpoint should be provided
* Kickstart the full CRUD app through
	``./flow ember:all YourCompany.PackageName``
* Be happy!


Which Ember persistence layers are supported?
=============================================
The current REST implementation (EpfRestController + EpfSerializer) supports:
* EPF (Ember Persistence Foundation)
* Ember Data (up to version 0.13)

The scaffolding mechanism supports:
* EPF only
  
In the future we plan to support current Ember Data and JSON API formats for the REST API. The possibilities for Ember Data scaffolding will be investigated.



