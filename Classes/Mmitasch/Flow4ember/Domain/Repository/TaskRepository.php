<?php
namespace Mmitasch\Flow4ember\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mmitasch.Flow4ember".   *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class TaskRepository extends Repository {

	/**
	 * Finds all tasks ordered by name
	 *
	 * @return \TYPO3\Flow\Persistence\QueryResultProxy The posts
	 */
	public function findTasksOrdered() {
		
			$query = $this->createQuery();
			return $query->setOrderings(array('name' => \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING))
					->execute();
	}
}
?>