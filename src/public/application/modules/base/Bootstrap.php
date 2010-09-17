<?php

/**
 * Schreibmaschine
 *
 * LICENSE
 *
 * This source file is subject to the new CC-GNU LGPL
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/LGPL/2.1/
 *
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

/**
 * @todo Namespace should be WST\Schreibmaschine\Modules\Base but is somehow not recognized
 */
namespace Base;

/**
 * Bootstrap class for module base
 *
 * @uses       \Zend\Application\Module\Bootstrap
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class Bootstrap extends \Zend\Application\Module\Bootstrap {

    /**
     * Sets the controller directory in the frontcontroller
     */
    protected function _initControllerDirectory() {

        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $front->setControllerDirectory(APPLICATION_PATH . '/modules/base/controllers', 'Base');
    }

}

