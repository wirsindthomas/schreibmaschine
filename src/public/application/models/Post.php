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
 * @todo Loader
 */
require_once LIBRARY_PATH . '/Phpillow/classes/document.php';

/**
 * Model class for blog posts.
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
class Post extends \phpillowDocument {

    /**
     * Document type
     * @var string
     */
    protected static $type = 'post';
    /**
     * Definition of required properties
     * @var array
     */
    protected $requiredProperties = array(
        'title',
        'text',
        'created',
        'publish'
    );

    /**
     * The constructor contains setup of all document properties
     * and the corresponding validators.
     */
    public function __construct() {
        $this->properties = array(
            'title' => new \phpillowStringValidator(),
            'text' => new \phpillowTextValidator(),
            'created' => new \phpillowDateValidator(),
            'comments' => new \phpillowArrayValidator(),
            'tags' => new \phpillowArrayValidator(),
            'author' => new \phpillowStringValidator(),
            'publish' => new \phpillowNoValidator(),
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
        return $this->stringToId($this->storage->title);
    }

    /**
     * Getter method for the document type
     *
     * @return string
     */
    protected function getType() {
        return self::$type;
    }

}
