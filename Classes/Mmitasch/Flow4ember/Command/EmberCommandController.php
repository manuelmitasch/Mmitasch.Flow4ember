<?php
namespace Mmitasch\Flow4ember\Command;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Command controller for the Ember Kickstart generator
 *
 */
class EmberCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;

	/**
	 * @var \Mmitasch\Flow4ember\Service\GeneratorService
	 * @Flow\Inject
	 */
	protected $generatorService;
	
	/**
	 * Kickstart everything needed for Emberification
	 * 
	 * Calls every other ember:* command to kickstart everthing needed.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite existing files.
	 * @return string
	 */
	public function allCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
				
		$this->outputParagraph("<b>## PERFORMING ALL THE MAGIC ##</b>");
		
		$this->configCommand($packageKey, $force);
		$this->flowCommand($packageKey, $force);
		$this->staticCommand($packageKey, $force);
		$this->appCommand($packageKey, $force);
		$this->modelCommand($packageKey, $force);
		$this->controllerCommand($packageKey, $force);
		$this->routeCommand($packageKey, $force);
		$this->viewCommand($packageKey, $force);
		$this->templateCommand($packageKey, $force);
		$this->npminstallCommand($packageKey);
		$this->buildtemplatesCommand($packageKey);
	}
	
	
	/**
	 * Kickstart configuration Ember.yaml and Routes.yaml
	 * 
	 * Creates the Ember.yaml and Routes.yaml configuration file.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing Ember.yaml file.
	 * @return string
	 */
	public function configCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
				
		$this->outputLine("<b>## GENERATING CONFIGURATION FILES (EMBER.YAML, ROUTES.YAML) ##</b>");
		$generatedFiles = $this->generatorService->generateEmberConfig($packageKey, $force);
		$this->outputLine(implode(PHP_EOL, $generatedFiles));

		$generatedFiles += $this->generatorService->generateRoutesConfig($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Kickstarts the app.js, store.js, router.js
	 * 
	 * Creates the models into Resources/Private/Script/Model.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing model file.
	 * @return string
	 */
	public function appCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
				
		$this->outputLine("<b>## GENERATING APP FILES (APP.JS, STORE.JS, ROUTER.JS) ##</b>");
		$generatedFiles = array();
		$generatedFiles += $this->generatorService->generateApp($packageKey, $force);
		$generatedFiles += $this->generatorService->generateStore($packageKey, $force);
		$generatedFiles += $this->generatorService->generateRouter($packageKey, $force);
		
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Kickstarts the models
	 * 
	 * Creates the models into Resources/Private/Script/Model.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing model file.
	 * @return string
	 */
	public function modelCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
		
		$this->outputLine("<b>## GENERATING TEMPLATES FILES ##</b>");
		$generatedFiles = $this->generatorService->generateModels($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Kickstarts the controllers
	 * 
	 * Creates the controllers into Resources/Private/Script/Controller.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing controller file.
	 * @return string
	 */
	public function controllerCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
		
		$this->outputLine("<b>## GENERATING CONTROLLERS FILES ##</b>");
		$generatedFiles = $this->generatorService->generateControllers($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Kickstarts the views
	 * 
	 * Creates the views into Resources/Private/Script/View.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing view file.
	 * @return string
	 */
	public function viewCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
		
		$this->outputLine("<b>## GENERATING VIEW FILES ##</b>");
		$generatedFiles = $this->generatorService->generateViews($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Kickstarts the templates
	 * 
	 * Creates the routes into Resources/Private/Script/Route.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing router.js file.
	 * @return string
	 */
	public function templateCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);

		$this->outputLine("<b>## GENERATING TEMPLATE FILES ##</b>");
		$generatedFiles = $this->generatorService->generateTemplates($packageKey, $force);
		$this->outputLine(implode(PHP_EOL, $generatedFiles));
		$this->outputParagraph("<b>## DONT'T FORGET TO BUILD YOUR TEMPLATES! ##</b>");
	}
	
	/**
	 * Kickstarts the routes
	 * 
	 * Creates the views into Resources/Private/Script/View.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing route file.
	 * @return string
	 */
	public function routeCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
		
		$this->outputLine("<b>## GENERATING ROUTES FILES ##</b>");
		$generatedFiles = $this->generatorService->generateRoutes($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
		
	/**
	 * Kickstart router.js
	 * 
	 * Creates the router.js file.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing router.js file.
	 * @return string
	 */
	public function routerCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
		
		$this->outputLine("<b>## GENERATING ROUTER.JS ##</b>");
		$generatedFiles = $this->generatorService->generateRouter($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Copies the static assets (css, js libraries)
	 * 
	 * Copies the static css and javascript assets (jquery, ember, handlebars, bootstrap, ...)
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing model file.
	 * @return string
	 */
	public function staticCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
				
		$this->outputLine("<b>## COPYING STATIC ASSETS ##</b>");
		$generatedFiles = $this->generatorService->generateStatic($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	
	/**
	 * Generates the Flow components: RestController, StandardController, RoutePart for the given package key
	 * 
	 * Generates the EpfRestController.php file into Classes/Controller/.
	 * Generates StandardController.php into Classes/Controller/
	 * and needed Fluid Templates and Layouts into Resources/Private
	 * 
	 * @param string $packageKey
	 * @param boolean $force
	 * @return string
	 */
	public function flowCommand($packageKey, $force = FALSE) {
		$this->checkPackage($packageKey);
				
		$this->outputLine("<b>## GENERATING REST CONTROLLER FILE ##</b>");
		$generatedFiles = $this->generatorService->generateRestController($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles) . '');

		$this->outputLine("<b>## GENERATING STANDARD CONTROLLER FILES ##</b>");
		$generatedFiles = $this->generatorService->generateStandardController($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
		
		$this->outputLine("<b>## GENERATING ROUTE PART FILE ##</b>");
		$generatedFiles = $this->generatorService->generateRoutePart($packageKey, $force);
		$this->outputParagraph(implode(PHP_EOL, $generatedFiles));
	}
	
	
	/**
	 * Install npm dependencies
	 * 
	 * Runs "npm install" to install npm dependencies for the grunt task (template compilation)
	 * This is just an alias to the shell command.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @return string
	 */
	public function npminstallCommand($packageKey) {
		$this->checkPackage($packageKey);
		$package = $this->packageManager->getPackage($packageKey);

		$this->outputLine("<b>## RUNNING NPM INSTALL ##</b>");
		$this->outputParagraph(exec('(cd ' . $package->getPackagePath() . ' && sudo npm install)'));
	}
	
	/**
	 * Runs template grunt task
	 * 
	 * Runs "grunt emberTemplates" to build/compile the handlebars templates into Resources/Public/Script/Build/template.js
	 * This is just an alias to the shell command.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @return string
	 */
	public function buildtemplatesCommand($packageKey) {
		$this->checkPackage($packageKey);
		$package = $this->packageManager->getPackage($packageKey);

		$this->outputLine("<b>## RUNNING GRUNT TEMPLATE BUILD TASK ##</b>");
		$this->outputParagraph(exec('(cd ' . $package->getPackagePath() . ' && grunt emberTemplates)'));
	}
	
	

	
	/**
	 * Check if packageKey is valid and package is available
	 */
	protected function checkPackage($packageKey) {
		$this->validatePackageKey($packageKey);
		if (!$this->packageManager->isPackageAvailable($packageKey)) {
			$this->outputLine('Package "%s" is not available.', array($packageKey));
			$this->quit(2);
		}
	}

	/**
	 * Checks the syntax of the given $packageKey and quits with an error message if it's not valid
	 *
	 * @param string $packageKey
	 * @return void
	 */
	protected function validatePackageKey($packageKey) {
		if (!$this->packageManager->isPackageKeyValid($packageKey)) {
			$this->outputLine('Package key "%s" is not valid. Only UpperCamelCase with alphanumeric characters in the format <VendorName>.<PackageKey>, please!', array($packageKey));
			$this->quit(1);
		}
	}
	
	/**
	 * Outputs specified text to the console window and appends two line break
	 *
	 * @param string $text Text to output
	 * @param array $arguments Optional arguments to use for sprintf
	 * @return void
	 * @see output()
	 * @see outputLines()
	 */
	protected function outputParagraph($text = '', array $arguments = array()) {
		$this->output($text . PHP_EOL . PHP_EOL, $arguments);
	}
}
?>
