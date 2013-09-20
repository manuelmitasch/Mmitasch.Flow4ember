<?php
namespace Mmitasch\Flow4ember\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class AppController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;
	
	/**
	 * @var string
	 */
	protected $packageKey;
	
	function __construct() {
		$this->packageKey = \Mmitasch\Flow4ember\Utility\NamingUtility::extractPackageKey(get_class($this));
	}
	
	/**
	 * Display the configured REST resources and Flow routes
	 * 
	 * @return void
	 */
	public function indexAction() {
		$javascriptResources = array();

		$javascriptResources[] = 'Script/Vendor/jquery-1.9.1.js';
		$javascriptResources[] = 'Script/Vendor/bootstrap.min.js';
		$javascriptResources[] = 'Script/Vendor/handlebars-1.0.0.js';
		$javascriptResources[] = 'Script/Vendor/ember-1.0.0.js';
		$javascriptResources[] = 'Script/Vendor/epf.js';


		$javascriptResources[] = 'Script/app.js';
		$javascriptResources[] = 'Script/store.js';
		$javascriptResources[] = 'Script/router.js';
		$javascriptResources[] = 'Script/Model/models.js';
		
		$javascriptResources = array_merge($javascriptResources, $this->getDirectoryContents('Script/Model', array('Script/Model/models.js')));
		$javascriptResources = array_merge($javascriptResources, $this->getDirectoryContents('Script/Controller'));

		$javascriptResources[] = 'Script/Build/templates.js';

		$javascriptResources = array_merge($javascriptResources, $this->getDirectoryContents('Script/View'));
		$javascriptResources = array_merge($javascriptResources, $this->getDirectoryContents('Script/Route'));

		$this->view->assign('javascriptResources', $javascriptResources);
		$this->view->assign('cssResources', array());
	}
	
	
	protected function getDirectoryContents($sourcePath, $excludeFiles = array()) {
		$package = $this->packageManager->getPackage($this->packageKey);
		
		$dir = opendir($package->getResourcesPath() . 'Public/' . $sourcePath); 
		$files = array();

		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($sourcePath . '/' . $file) ) { 
					$files = array_merge($files, $this->getDirectoryContents($sourcePath . '/' . $file, $excludeFiles));
				} else { 
					$pathAndFile = $sourcePath . '/' . $file;
					if (!in_array($pathAndFile, $excludeFiles)) {
						$files[] = $sourcePath . '/' . $file;
					}
				} 
			} 
		} 
		closedir($dir); 
		
		return $files;
	}
	
	

}

?>