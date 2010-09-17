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
 * @package    WST\Schreibmaschine\Model
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\Model;

/**
 * Model class for the datastore each registered plugin as.
 *
 * @uses       \phpillowDocument
 * @uses       \phpillowStringValidator
 * @uses       \phpillowArrayValidator
 * @uses       \phpillowDateValidator
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Model
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class PluginDatastore extends \phpillowDocument {

    /**
     * Document type
     * @var string
     */
    protected static $type = 'wst.schreibmaschine.plugin-datastore';

    /**
     * Definition of required properties
     * @var array
     */
    protected $requiredProperties = array(
        'name',
        'created'
    );

    /**
     * The constructor contains setup of all document properties
     * and the corresponding validators.
     */
    public function __construct() {
        $this->properties = array(
            'name' => new \phpillowStringValidator(),
            'created' => new \phpillowDateValidator(),
            //This is the datastore which can be used by plugins
            'data' => new \phpillowArrayValidator(),
        );
        //Call the parent constructor
        parent::__construct();
    }

    /**
     * Generator method for the document id
     * (required by \phpillowDocument)
     * 
     * @return string
     */
    protected function generateId() {
        return $this->stringToId($this->storage->name);
    }
    
    /**
     * Getter method for the document type
     *
     * @return string
     */
    public function getType() {
        return self::$type;
    }

}
