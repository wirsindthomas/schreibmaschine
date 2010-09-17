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
 * @todo Namespaces are not yet working for controller classes as they're obviously not recognised by the frontcontroller
 */
//namespace WST\Schreibmaschine\Modules\Base\Controller;
use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/controllers/DefaultController.php';

/**
 * Index controller - forwards to the list of latest posts
 *
 * @uses       \WST\Schreibmaschine\Controllers\DefaultController
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base\Controller
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class IndexController extends \WST\Schreibmaschine\Controllers\DefaultController {

    /**
     * Index action forwards to the list of latest posts
     * @see \WST\Schreibmaschine\Modules\Base\Controller\Post#listAction()
     */
    public function indexAction() {
        $this->_forward('list', 'post');
    }

}
