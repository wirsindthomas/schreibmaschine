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
 * @package    WST\Schreibmaschine\Modules\Base\Controller
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\View\Helpers;

use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/models/PluginDatastore.php';

/**
 * Abstract class to be implemented by plugins and view helpers
 * Implements a datastore API for each Helper/Plugin
 *
 * @uses       \Zend\View\Helper\AbstractHelper
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\View\Helpers
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper {

    /**
     *
     * @var array
     */
    private $datastore;


    /**
     * The constructor tries to load the document of the current plugin
     * or creates one.
     */
    public function __construct() {

        $this->datastore = new Model\PluginDatastore();
        try {
            $id = $this->datastore->getType() . '-' . $this->getName();
            $this->datastore->fetchById($id);
        } catch (\phpillowResponseNotFoundErrorException $e) {

            $this->datastore->name = $this->getName();
            $this->datastore->created = date('r');

            $this->datastore->save();
        }
    }

    /**
     * Getter method for accessing the plugin datastore
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key) {

        if (\is_string($key) === false) {
            /**
             * @todo Implement proper error handling
             */
            throw new \Exception('Key must be of type string');
        }

        if (isset($this->datastore->revisions[0]['data'][$key])) {

            return $this->datastore->revisions[0]['data'][$key];
        }
    }

    /**
     * Setter method for storing values into the datastore
     *
     * @param string $key
     * @param mixed $value
     */
    public function setData($key, $value) {

        if (\is_string($key) === false) {
            /**
             * @todo Implement proper error handling
             */
            throw new \Exception('Key must be of type string');
        }

        $this->datastore->$key = $value;
        $this->datastore->save();
    }

    /**
     * Abstract method for geeting the unique name of the plugin
     *
     * @return string
     */
    abstract public function getName();

}
