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
	 * Kickstart configuration Ember.yaml 
	 * 
	 * Creates a Ember.yaml configuration file.
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing Ember.yaml file.
	 * @return string
	 */
	public function configCommand($packageKey, $force = FALSE) {
		$this->validatePackageKey($packageKey);
		if (!$this->packageManager->isPackageAvailable($packageKey)) {
			$this->outputLine('Package "%s" is not available.', array($packageKey));
			$this->quit(2);
		}
		$generatedFiles = $this->generatorService->generateConfig($packageKey, $force);
		$this->outputLine(implode(PHP_EOL, $generatedFiles));
	}
	
	/**
	 * Create RestController and ModelReflectionService 
	 * 
	 * Creates the RestController and ModelReflectionService for the given package
	 * 
	 * @param string $packageKey The package key, for example "MyCompany.MyPackageName"
	 * @param boolean $force Overwrite an existing Ember.yaml file.
	 * @return string
	 */
	public function infrastructureCommand($packageKey, $force = FALSE) {
		$this->validatePackageKey($packageKey);
		if (!$this->packageManager->isPackageAvailable($packageKey)) {
			$this->outputLine('Package "%s" is not available.', array($packageKey));
			$this->quit(2);
		}
		
		$generatedFiles = array();
		$generatedFiles += $this->generatorService->generateRestController($packageKey, $force);
		$generatedFiles += $this->generatorService->generateModelReflectionservice($packageKey, $force);

		$this->outputLine(implode(PHP_EOL, $generatedFiles));
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

}
?>
