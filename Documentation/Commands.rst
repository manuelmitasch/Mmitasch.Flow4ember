********
Commands
********

This package contains several command line functions to invoke certain scaffolding functionalities. You can invoke a command with: ::

  $ ./flow ember:command YourCompany.PackageName

The ``ember:all``, ``ember:npminstall``, and ``ember:buildtemplates`` have a **dependency on grunt**. Thus make sure to have node (with npm) installed.


Help System
===========

You can use Flow's help system to obtain informations about the available commands: ::

  $ ./flow help flow4ember
  13 commands match the command identifier "flow4ember":

  PACKAGE "MMITASCH.FLOW4EMBER":
  -------------------------------------------------------------------------------
    ember:all                                Kickstart everything needed for
                                             Emberification
    ember:config                             Kickstart configuration Ember.yaml
                                              and Routes.yaml
    ember:app                                Kickstarts the app.js, store.js,
                                              router.js
    ember:model                              Kickstarts the models
    ember:controller                         Kickstarts the controllers
    ember:view                               Kickstarts the views
    ember:template                           Kickstarts the templates
    ember:route                              Kickstarts the routes
    ember:router                             Kickstart router.js
    ember:static                             Copies the static assets (css, js
                                              libraries)
    ember:flow                               Generates the Flow components:
                                              RestController, StandardController,
                                              RoutePart for the given package key
    ember:npminstall                         Install npm dependencies
    ember:buildtemplates                     Runs template grunt task


To obtain informations about the parameters of a specific command use: ::

  $ ./flow help ember:config

  Kickstart configuration Ember.yaml and Routes.yaml

  COMMAND:
    mmitasch.flow4ember:ember:config

  USAGE:
    ./flow ember:config [<options>] <package key>

  ARGUMENTS:
    --package-key        The package key, for example "MyCompany.MyPackageName

  OPTIONS:
    --force              Overwrite an existing Ember.yaml file.

  DESCRIPTION:
    Creates the Ember.yaml and Routes.yaml configuration file.



