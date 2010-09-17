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
 * @package    WST\Schreibmaschine
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine;

use WST\Schreibmaschine\Model as Model;
use Zend\Application as ZA;

/**
 * @todo move to autoloader
 */
require_once 'Phpillow/bootstrap.php';
require_once APPLICATION_PATH . '/models/Config.php';

/**
 *
 *
 * @uses       \Zend\Application\Bootstrap
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class Bootstrap extends ZA\Bootstrap {

    /**
     *
     * @var string
     */
    private $theme = null;

    /**
     * Initializer method for the autoloader
     * @todo not working yet
     */
    protected function _initAutoload() {
        //Make sure Modules are bootstrapped
        $this->bootstrap('Modules');
        $frontController = $this->getPluginResource('FrontController');
        $resourceLoader = $frontController->getBootstrap()->getResourceLoader();
        $resourceLoader->addResourceType('model', 'models', 'Model');
    }

    /**
     * Stores the whole config object into the registry for easier access.
     *
     * @return void
     */
    protected function _initConfig() {
        $config = new \Zend\Config\Config($this->getOptions());
        \Zend\Registry::set('config', $config);

        $this->bootstrap('Log');
        $log = $this->getPluginResource('log');
        \Zend\Registry::set('log', $log->getLog());
    }

    /**
     * Initializes CouchDb connection.
     *
     * @return \phpillowConnection
     */
    protected function _initCouchDb() {

        $config = $this->getOptions();
        $dbConfig = $config['couch']['params'];

        //Initialize Database Object
        \phpillowConnection::createInstance($dbConfig['host'],
                        $dbConfig['port'],
                        $dbConfig['username'],
                        $dbConfig['password']);
        $db = \phpillowConnection::getInstance();
        \phpillowConnection::setDatabase($dbConfig['database']);

        return $db;
    }

    /**
     * This method tries to fetch the configuration object which is stored in the
     * database and stores it into the Registry.
     * If it does not exist, the object is created.
     *
     * @return WST\Schreibmaschine\Model\Config
     */
    protected function _initBlogConfig() {
        //Make sure CouchDb is bootstrapped
        $this->bootstrap('CouchDb');

        $configDoc = new Model\Config();
        $configId = $configDoc->getType() . '-' . $configDoc->getId();

        try {
            $configDoc->fetchById($configId);
        } catch (\phpillowResponseNotFoundErrorException $e) {

            $configDoc->_id = $configDoc->getId();
            $configDoc->save();
        }

        \Zend\Registry::set('blogConfig', $configDoc);

        return $configDoc;
    }

    /**
     * Initializes the view
     *
     * @return \Zend\View\View 
     */
    protected function _initView() {
        $this->bootstrap('BlogConfig');

        /**
         * @todo cache
         */
        // Initialize view
        $view = new \Zend\View\View();

        //Get current theme
        $theme = $this->getTheme();

        //If theme dir does not exist - throw exception
        if (!is_dir(VIEW_PATH . '/' . $theme)) {
            /**
             * @todo create typed exception class
             */
            throw new \Exception($theme . ' appears to be not a valid theme for WST Schreibmaschine.');
        }

        //Set path to views according to the current theme
        $view->setBasePath(VIEW_PATH . '/' . $theme . '/views');

        //Fetch blog configuration document from the registry
        $blogConfig = \Zend\Registry::get('blogConfig');


        //Set Head Title
        $blogTitle = isset($blogConfig->revisions[0]['blogTitle']) ? $blogConfig->revisions[0]['blogTitle'] : 'Welcome to WST Schreibmaschine';
        $view->headTitle($blogTitle);

        //Set encoding of the view
        $view->setEncoding('UTF-8');

        //Set environment of the view (according to the applicaiton's environment
        $view->env = APPLICATION_ENV;

        //Register default helper classes
        $view->addHelperPath(\APPLICATION_PATH . '/views/helpers', 'WST\Schreibmaschine\View\Helpers');

        //Register helper classes for theme (if present)
        if (is_dir(VIEW_PATH . '/' . $theme . '/helpers')) {
            $view->addHelperPath(VIEW_PATH . '/' . $theme . '/helpers', 'WST\Schreibmaschine\Themes\\' . \ucfirst($theme) . '\Helpers');
        }

        //Register helper classes as plugins
        foreach ((array) $blogConfig->revisions[0]['plugins'] as $plugin) {
            if (\is_dir(PLUGIN_PATH . '/' . $plugin)) {
                $view->addHelperPath(PLUGIN_PATH . '/' . $plugin, 'WST\Schreibmaschine\Plugins\\' . \ucfirst($plugin));
            }
        }


        // Add the view object to the ViewRenderer
        $viewRenderer = \Zend\Controller\Action\HelperBroker::getStaticHelper('ViewRenderer');

        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

    /**
     * Initializing method of the layout
     *
     *
     */
    protected function _initLayout() {
        
        //Get current theme
        $theme = $this->getTheme();
        
        // Change layout path
        \Zend\Layout\Layout::startMvc(
                        array(
                            'layoutPath' => VIEW_PATH . '/' . $theme . '/layouts/scripts',
                            'layout' => 'layout'
                        )
        );
    }

    /**
     *  Getter method for the current theme
     *
     * @return string
     * @throws \Exception
     */
    private function getTheme() {

        //If $theme is not set - try to fetch it from the blog configuration
        if (isset($this->theme) === false) {

            $blogConfig = \Zend\Registry::get('blogConfig');

            if (isset($blogConfig->revisions[0]['currentTheme'])) {

                $this->theme = $blogConfig->revisions[0]['currentTheme'];
            
                
             //	If no currentTheme is defined, set to default
            } else if (isset($blogConfig->revisions[0]['defaultTheme'])) {

                $this->theme = $blogConfig->revisions[0]['defaultTheme'];
            
                
             //If no default theme is defined throw an exception
            } else {
                /**
                 * @todo Implement a typed exception
                 */
                throw new \Exception('No default theme defined in configuration!');
            }
        }
        
        return $this->theme;
    }

}

