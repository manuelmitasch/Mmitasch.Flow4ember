<?php
namespace Mmitasch\Flow4ember\Service;

/**
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

/**
 * Interface for the ModelReflectionsService
 */
interface ModelReflectionServiceInterface {
	
	
	/**
	 * Get all Metamodels for the given package
	 * 
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return array<\Mmitasch\Flow4ember\Domain\Model\Metamodel>
	 */
	public function getMetaModels($packageKey);
	
	
	/**
	 * Get Metamodel by Flow model name
	 * 
	 * @param string $flowModelName
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByFlowModelName($flowModelName, $packageKey);
	
	/**
	 * Get Metamodel by resource name
	 * 
	 * @param string $resourceName
 	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return \Mmitasch\Flow4ember\Domain\Model\Metamodel
	 */
	public function findByResourceName($resourceName, $packageKey);
	
	/**
	 * Is the resource with given name registered?
	 * 
	 * @param string $resourceName
	 * @param string $packageKey The package in which the models are used (eg. 'Mmitasch.Taskplaner')
	 * @return boolean
	 */
	public function hasResourceName($resourceName, $packageKey);

}

?>
