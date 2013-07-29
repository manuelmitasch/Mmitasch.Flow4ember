<?php
namespace Mmitasch\Flow4ember\Annotations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Marks an association to be sideloaded.
 * Thus the rest api should include the payload of the associated models.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Sideload {

}

?>