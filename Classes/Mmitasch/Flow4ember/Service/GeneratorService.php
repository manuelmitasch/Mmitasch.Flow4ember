<?php
namespace Mmitasch\Flow4ember\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 * Highly inspired by "TYPO3.Kickstart" package.                          *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow,
	TYPO3\Flow\Package\Package;

/**
 * Service for the Kickstart generator
 */
class GeneratorService {

	/**
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;

	/**
	 * @var \TYPO3\Fluid\Core\Parser\TemplateParser
	 * @Flow\Inject
	 */
	protected $templateParser;
	
	/**
	 * @var \Mmitasch\Flow4ember\Parser\JavascriptTemplateParser
	 * @Flow\Inject
	 */
	protected $javascriptTemplateParser;

	/**
	 * @var \Mmitasch\Flow4ember\Service\ModelReflectionServiceInterface
 	 * @Flow\Inject
	 */
	protected $modelReflectionService;

	/**
	 * @var array
	 */
	protected $generatedFiles = array();
	
	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;
	
	
	/**
	 * Generates the Ember.yaml configuration file for given package key
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateEmberConfig($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Configuration/Ember.yaml.tmpl';
		$package = $this->packageManager->getPackage($packageKey);
		
		$tokens = explode('.', $packageKey);
		$packageNamespace = trim($tokens[0]);
		$packageName = trim($tokens[1]);
		
		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['packageNamespace'] = $packageNamespace;
		$contextVariables['packageName'] = $packageName;
		$contextVariables['appRoute'] = $packageKey;
		$contextVariables['restRoute'] = $packageKey . '/rest'; 
		$contextVariables['restController'] = $package->getNamespace() . '\Controller\EpfRestController';
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables);

		$targetPathAndFilename = $package->getConfigurationPath() . 'Ember.yaml';
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}
	
	/**
	 * Generates the Routes.yaml configuration file for given package key
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRoutesConfig($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Configuration/Routes.yaml.tmpl';
		$package = $this->packageManager->getPackage($packageKey);
		
		$config = $this->configurationManager->getConfiguration('Ember');
		
		$tokens = explode('.', $packageKey);
		$packageNamespace = trim($tokens[0]);
		$packageName = trim($tokens[1]);
		$restController = '';
		$restRoute = '';
		$appRoute = '';
		
		if (isset($config[$packageNamespace][$packageName]['routes']['rest']) && !empty($config[$packageNamespace][$packageName]['routes']['rest'])) {
			$restRoute = $config[$packageNamespace][$packageName]['routes']['rest'];
		} else {
			$restRoute = '{@package}/rest';
		}
		if (isset($config[$packageNamespace][$packageName]['routes']['app']) && !empty($config[$packageNamespace][$packageName]['routes']['app'])) {
			$appRoute = $config[$packageNamespace][$packageName]['routes']['app'];
		} else {
			$appRoute = '{@package}';
		}
		if (isset($config[$packageNamespace][$packageName]['restController']) && !empty($config[$packageNamespace][$packageName]['restController'])) {
			$restController = $config[$packageNamespace][$packageName]['restController'];
		} else {
			$restController = $package->getNamespace() . '\Controller\RestController';
		}
		
		$tokens = explode('\\', $restController);
		$className = trim(end($tokens));
		$restControllerShortName = str_replace("Controller", "", $className);

		$contextVariables = array();
		$contextVariables['packageKey'] = $packageKey;
		$contextVariables['restRoute'] = $restRoute; 
		$contextVariables['appRoute'] = $appRoute; 
		$contextVariables['restControllerShortName'] = $restControllerShortName;
		$contextVariables['routePart'] = $package->getNamespace() . '\Routing\ResourceNameRoutePart';
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetPathAndFilename = $package->getConfigurationPath() . 'Routes.yaml';
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}
	
	
		/**
	 * Generates the app.js file for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateApp($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/app.js.tmpl';
		$package = $this->packageManager->getPackage($packageKey);
		$configurationManager = $this->objectManager->get('TYPO3\Flow\Configuration\ConfigurationManager');
		$config = $configurationManager->getConfiguration('Ember');
		
		$tokens = explode('.', $packageKey);
		$packageNamespace = trim($tokens[0]);
		$packageName = trim($tokens[1]);
		$emberNamespace = '';
		
		if (isset($config[$packageNamespace][$packageName]['emberNamespace']) && !empty($config[$packageNamespace][$packageName]['emberNamespace'])) {
			$emberNamespace = $config[$packageNamespace][$packageName]['emberNamespace'];
		} else {
			$emberNamespace = 'App';
		}			
		
		$contextVariables = array('emberNamespace' => $emberNamespace);
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'app.js';
		$targetPath = $package->getResourcesPath() . 'Public/Script/';
		$targetPathAndFilename = $targetPath . $targetFilename;

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}
	
		
	/**
	 * Generates the store.js file for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateStore($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/store.js.tmpl';
		$package = $this->packageManager->getPackage($packageKey);
		$config = $this->configurationManager->getConfiguration('Ember');

		$tokens = explode('.', $packageKey);
		$packageNamespace = trim($tokens[0]);
		$packageName = trim($tokens[1]);
		$restRoute = '';
		$emberNamespace = '';
		
		if (isset($config[$packageNamespace][$packageName]['restNamespace']) && !empty($config[$packageNamespace][$packageName]['restNamespace'])) {
			$restRoute = $config[$packageNamespace][$packageName]['restNamespace'];
		} else {
			$restRoute = $packageKey . '/rest';
		}		
		if (isset($config[$packageNamespace][$packageName]['emberNamespace']) && !empty($config[$packageNamespace][$packageName]['emberNamespace'])) {
			$emberNamespace = $config[$packageNamespace][$packageName]['emberNamespace'];
		} else {
			$emberNamespace = 'App';
		}			
		
		$contextVariables = array(
			'restRoute' => $restRoute, 
			'emberNamespace' => $emberNamespace
		);
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'store.js';
		$targetPath = $package->getResourcesPath() . 'Public/Script/';
		$targetPathAndFilename = $targetPath . $targetFilename;

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}	
	
	
	/**
	 * Generates the router.js file for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRouter($packageKey, $overwrite = FALSE) {
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/router.js.tmpl';
		$metaModels = $this->modelReflectionService->getMetaModels($packageKey);
		$package = $this->packageManager->getPackage($packageKey);
		
		$contextVariables = array('models' => $metaModels);
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'router.js';
		$targetPath = $package->getResourcesPath() . 'Public/Script/';
		$targetPathAndFilename = $targetPath . $targetFilename;

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}

	
	
	
	/**
	 * Generates the router.js file for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateModels($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$metaModels = $this->modelReflectionService->getMetaModels($packageKey);
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Model/models.js.tmpl';
		
		$contextVariables = array('models' => $metaModels);
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'models.js';
		$targetPath = $package->getResourcesPath() . 'Public/Script/Model/';
		$targetPathAndFilename = $targetPath . $targetFilename;

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		
		foreach ($metaModels as $metaModel) {
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Model/model.js.tmpl';
					
			$contextVariables = array();
			$contextVariables['model'] = $metaModel;
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getModelNameLowercased() . '.js';
			$targetPathAndFilename = $targetPath . $targetFilename;

			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		}
		
		return $this->returnAndEmptyGeneratedFiles();
	}
	
	/**
	 * Generates the controller javascript files for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateControllers($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$metaModels = $this->modelReflectionService->getMetaModels($packageKey);
		$targetPath = $package->getResourcesPath() . 'Public/Script/Controller/';
		
		foreach ($metaModels as $metaModel) {
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Controller/models_index.js.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getResourceName() . '_index.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models index controller
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Controller/models_new.js.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getResourceName() . '_new.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models new controller
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Controller/model_index.js.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getModelNameLowercased() . '_index.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate model route
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
		}
		
		return $this->returnAndEmptyGeneratedFiles();
	}
	
			
	/**
	 * Generates the view javascript files for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateViews($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$metaModels = $this->modelReflectionService->getMetaModels($packageKey);
		$targetPath = $package->getResourcesPath() . 'Public/Script/View/';
		
		foreach ($metaModels as $metaModel) {
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/View/models_new.js.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getResourceName() . '_new.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models new view
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		}
		
		return $this->returnAndEmptyGeneratedFiles();
	}
	
	
	/**
	 * Generates the handlebars template files for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateTemplates($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$metaModels = $this->modelReflectionService->getMetaModels($packageKey);
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/application.hbs.tmpl';
		
		$contextVariables = array('models' => $metaModels); // use first model as index route
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'application.hbs';
		$targetPath = $package->getResourcesPath() . 'Public/Script/Template/';
		$targetPathAndFilename = $targetPath . $targetFilename;
			// generate application handlebars template
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		
		foreach ($metaModels as $metaModel) {
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/models.hbs.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getResourceName() . '.hbs';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models template
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/model.hbs.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getModelNameLowercased() . '.hbs';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate model template
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
						
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/_model_edit_fields.hbs.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = '_' . $metaModel->getModelNameLowercased() . '_edit_fields.hbs';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate model edit fields partial
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/models/index.hbs.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $metaModel->getResourceName() . '/index.hbs';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models index template
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Template/models/new.hbs.tmpl';
			$contextVariables = array('model' => $metaModel);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = '_' . $metaModel->getResourceName() . '/new.hbs';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models index template
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		}
		
		return $this->returnAndEmptyGeneratedFiles();
	}	
	
	/**
	 * Generates the routes javascript files for the given package
	 *
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRoutes($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$resource = $this->modelReflectionService->getResources($packageKey);
		$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Route/index.js.tmpl';
		
		$contextVariables = array('model' => reset($resource)); // use first model as index route
		$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);

		$targetFilename = 'index.js';
		$targetPath = $package->getResourcesPath() . 'Public/Script/Route/';
		$targetPathAndFilename = $targetPath . $targetFilename;
			// generate index route
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		
		foreach ($resource as $resource) {
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Route/models_index.js.tmpl';
			$contextVariables = array('model' => $resource);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $resource->getResourceName() . '_index.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models index route
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Route/models_new.js.tmpl';
			$contextVariables = array('model' => $resource);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $resource->getResourceName() . '_new.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate models new route
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
			
			$templatePathAndFilename = 'resource://Mmitasch.Flow4ember/Private/Generator/Epf/Script/Route/model.js.tmpl';
			$contextVariables = array('model' => $resource);
			$fileContent = $this->renderTemplate($templatePathAndFilename, $contextVariables, TRUE);
			
			$targetFilename = $resource->getModelNameLowercased() . '.js';
			$targetPathAndFilename = $targetPath . $targetFilename;
				// generate model route
			$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
			
		}
		
		return $this->returnAndEmptyGeneratedFiles();
	}	
	
	
	/**
	 * Copies the static assets
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateStatic($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$thisPackageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
		$thisPackage = $this->packageManager->getPackage($thisPackageKey);
		
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/Epf/Script/Vendor';
		$targetPath = $package->getResourcesPath() . 'Public/Script/Vendor';
		$this->copyDirectory($sourcePath, $targetPath, $overwrite);
		
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/Css';
		$targetPath = $package->getResourcesPath() . 'Public/Css';
		$this->copyDirectory($sourcePath, $targetPath, $overwrite);
				
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/Fonts';
		$targetPath = $package->getResourcesPath() . 'Public/Fonts';
		$this->copyDirectory($sourcePath, $targetPath, $overwrite);
				
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/package.json';
		$targetPath = $package->getPackagePath() . 'package.json';
		$this->copyFile($sourcePath, $targetPath, $overwrite);
		
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/Gruntfile.js';
		$targetPath = $package->getPackagePath() . 'Gruntfile.js';
		$this->copyFile($sourcePath, $targetPath, $overwrite);		

		return $this->returnAndEmptyGeneratedFiles();
	}
	
	/**
	 * Generates the RestController for the given package key
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRestController($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$thisPackageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
		$thisPackage = $this->packageManager->getPackage($thisPackageKey);
		
		$fileName = 'EpfRestController.php';
		$sourcePathAndFilename = $thisPackage->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		$targetPathAndFilename = $package->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		
		$fileContent = file_get_contents($sourcePathAndFilename);
		$fileContent = str_replace('Mmitasch\\Flow4ember\\Controller', $package->getNamespace() . '\Controller', $fileContent);

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}
	
	/**
	 * Generates the AppController and ApiController for the given package key
	 * 
	 * Generates StandardController.php into Classes/Controller/
	 * and needed Fluid Templates and Layouts into Resources/Private
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateAppAndApiController($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$thisPackageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
		$thisPackage = $this->packageManager->getPackage($thisPackageKey);
		
		$fileName = 'AppController.php';
		$sourcePathAndFilename = $thisPackage->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		$targetPathAndFilename = $package->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		
		$fileContent = file_get_contents($sourcePathAndFilename);
		$fileContent = str_replace('Mmitasch\\Flow4ember\\Controller', $package->getNamespace() . '\Controller', $fileContent);
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
		
		$fileName = 'ApiController.php';
		$sourcePathAndFilename = $thisPackage->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		$targetPathAndFilename = $package->getClassesNamespaceEntryPath() . 'Controller/' . $fileName;
		
		$fileContent = file_get_contents($sourcePathAndFilename);
		$fileContent = str_replace('Mmitasch\\Flow4ember\\Controller', $package->getNamespace() . '\Controller', $fileContent);
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/Layouts';
		$targetPath = $package->getResourcesPath() . 'Private/Layouts';
		$this->copyDirectory($sourcePath, $targetPath, $overwrite);
		
		$sourcePath = $thisPackage->getResourcesPath() . 'Private/Generator/General/Templates';
		$targetPath = $package->getResourcesPath() . 'Private/Templates';
		$this->copyDirectory($sourcePath, $targetPath, $overwrite);
		
		return $this->returnAndEmptyGeneratedFiles();
	}
	
	
	/**
	 * Generates the StandardController for the given package key
	 * 
	 * Generates StandardController.php into Classes/Controller/
	 * and needed Fluid Templates and Layouts into Resources/Private
	 * 
	 * @param string $packageKey The package key
	 * @param boolean $overwrite Overwrite any existing files?
	 * @return array An array of generated filenames
	 */
	public function generateRoutePart($packageKey, $overwrite = FALSE) {
		$package = $this->packageManager->getPackage($packageKey);
		$thisPackageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
		$thisPackage = $this->packageManager->getPackage($thisPackageKey);
		
		$fileName = 'ResourceNameRoutePart.php';
		$sourcePathAndFilename = $thisPackage->getClassesNamespaceEntryPath() . 'Routing/' . $fileName;
		$targetPathAndFilename = $package->getClassesNamespaceEntryPath() . 'Routing/' . $fileName;
		
		$fileContent = file_get_contents($sourcePathAndFilename);
		$fileContent = str_replace('Mmitasch\\Flow4ember\\Routing', $package->getNamespace() . '\Routing', $fileContent);

		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);

		return $this->returnAndEmptyGeneratedFiles();
	}
	

	/**
	 * Generate a file with the given content and add it to the
	 * generated files
	 *
	 * @param string $targetPathAndFilename
	 * @param string $fileContent
	 * @param boolean $overwrite
	 * @return void
	 */
	protected function generateFile($targetPathAndFilename, $fileContent, $overwrite = FALSE) {
		if (!is_dir(dirname($targetPathAndFilename))) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively(dirname($targetPathAndFilename));
		}

		if (substr($targetPathAndFilename, 0, 11) === 'resource://') {
			list($packageKey, $resourcePath) = explode('/', substr($targetPathAndFilename, 11), 2);
			$relativeTargetPathAndFilename = $packageKey . '/Resources/' . $resourcePath;
		} elseif (strpos($targetPathAndFilename, 'Tests') !== FALSE) {
			$relativeTargetPathAndFilename = substr($targetPathAndFilename, strrpos(substr($targetPathAndFilename, 0, strpos($targetPathAndFilename, 'Tests/') - 1), '/') + 1);
		} else {
			$relativeTargetPathAndFilename = substr($targetPathAndFilename, strrpos(substr($targetPathAndFilename, 0, strpos($targetPathAndFilename, 'Classes/') - 1), '/') + 1);
		}

		if (!file_exists($targetPathAndFilename) || $overwrite === TRUE) {
			file_put_contents($targetPathAndFilename, $fileContent);
			$this->generatedFiles[] = 'Created ' . $targetPathAndFilename;
		} else {
			$this->generatedFiles[] = 'Omitted ' . $targetPathAndFilename;
		}
	}
	
	/**
	 * Copies a file from sourcePath to targetPath
	 * 
	 * @param string $sourcePathAndFilename
	 * @param string $targetPathAndFilename
	 * @param type $overwrite
	 */
	protected function copyFile($sourcePathAndFilename, $targetPathAndFilename, $overwrite = FALSE) {
		$fileContent = file_get_contents($sourcePathAndFilename);
		$this->generateFile($targetPathAndFilename, $fileContent, $overwrite);
	}
	
	/**
	 * Copy a directory from sourcePath to targetPath
	 * 
	 * @param type $sourcePath
	 * @param type $targetPath
	 * @param type $overwrite
	 */
	protected function copyDirectory($sourcePath, $targetPath, $overwrite = FALSE) {
		$dir = opendir($sourcePath); 

		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($sourcePath . '/' . $file) ) { 
					$this->copyDirectory($sourcePath . '/' . $file, $targetPath . '/' . $file, $overwrite); 
				} else { 
					$this->copyFile($sourcePath . '/' . $file, $targetPath . '/' . $file, $overwrite); 
				} 
			} 
		} 
		closedir($dir); 
	}
	
	protected function returnAndEmptyGeneratedFiles() {
		$generatedFiles = $this->generatedFiles;
		$this->generatedFiles = array();
		return $generatedFiles;
	}

	/**
	 * Render the given template file with the given variables
	 *
	 * @param string $templatePathAndFilename
	 * @param array $contextVariables
	 * @param boolean $parseJavascript 
	 * @return string
	 * @throws \TYPO3\Fluid\Core\Exception
	 */
	protected function renderTemplate($templatePathAndFilename, array $contextVariables, $parseJavascript = FALSE) {
		$templateSource = \TYPO3\Flow\Utility\Files::getFileContents($templatePathAndFilename, FILE_TEXT);
		if ($templateSource === FALSE) {
			throw new \TYPO3\Fluid\Core\Exception('The template file "' . $templatePathAndFilename . '" could not be loaded.', 1225709595);
		}
		$parsedTemplate = '';
		
		if ($parseJavascript) {
			$parsedTemplate = $this->javascriptTemplateParser->parse($templateSource);
		} else {
			$parsedTemplate = $this->templateParser->parse($templateSource);
		}
		
		$renderingContext = $this->buildRenderingContext($contextVariables);

		return $parsedTemplate->render($renderingContext);
	}

	/**
	 * Build the rendering context
	 *
	 * @param array $contextVariables
	 * @return \TYPO3\Fluid\Core\Rendering\RenderingContext
	 */
	protected function buildRenderingContext(array $contextVariables) {
		$renderingContext = new \TYPO3\Fluid\Core\Rendering\RenderingContext();

		$renderingContext->injectTemplateVariableContainer(new \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer($contextVariables));
		$renderingContext->injectViewHelperVariableContainer(new \TYPO3\Fluid\Core\ViewHelper\ViewHelperVariableContainer());

		return $renderingContext;
	}
}
?>
