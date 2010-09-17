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
 * @package    WST\Schreibmaschine\Controllers
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\Controllers;

use Zend;

/**
 * Abstract controller class for all non admin controllers.
 * Includes certain convenience features like access to the logger
 * or the configurations.
 *
 * @uses       Zend\Controller\Action
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Controllers
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
abstract class DefaultController extends Zend\Controller\Action {

    protected $log;
    protected $blogConfig;

    /**
     * Includes certain convenience features like access to the logger or the configurations.
     */
    public function init() {

        //Get logger object and store it as a class member
        $bootstrap = $this->getInvokeArg('bootstrap');
        $this->log = $bootstrap->getResource('log');

        //Store the blog configuration document as a class member
        $this->blogConfig = \Zend\Registry::get('blogConfig');

        //Make the name of the current theme accessible in the view
        $this->view->currentTheme = isset($this->blogConfig->revisions[0]['currentTheme']) ? $this->blogConfig->revisions[0]['currentTheme'] : $this->blogConfig->revisions[0]['defaultTheme'];

        //Get the application config object and store the URL of the host as a class member in the view
        $config = \Zend\Registry::get('config');
        $this->view->hostUrl = $config->hostUrl;
    }

    /**
     * This method is executed before the current action is dispatched.
     * It evaluates if the request was made via Ajax and sets the layout to blank.
     */
    public function preDispatch() {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->setLayout('blank');
        }
    }

    /**
     * Getter method for the log object
     * @return \Zend\Log\Logger
     */
    protected function log() {
        return $this->log;
    }

    /**
     * Getter method for the blogConfig object
     * @return \phpillowDocument
     */
    protected function blogConfig() {
        return $this->blogConfig;
    }

}
