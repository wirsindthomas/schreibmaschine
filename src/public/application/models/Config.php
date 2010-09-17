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
 * Model class for the blog configuration document.
 * This model class is intended to be unique over a Schreibmaschine installation.
 *
 * @uses       \phpillowDocument
 * @uses       \phpillowStringValidator
 * @uses       \phpillowArrayValidator
 * @uses       \phpillowNoValidator
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Model
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class Config extends \phpillowDocument {

    /**
     * Document type
     * @var string
     */
    protected static $type = 'wst.schreibmaschine.blog';

    /**
     *
     * @var array
     */
    protected $requiredProperties = array(
    );

    /**
     * The constructor contains setup of all document properties
     * and the corresponding validators.
     */
    public function __construct() {
        $this->properties = array(
            'blogTitle'         => new \phpillowStringValidator(),
            'blogDescription'   => new \phpillowStringValidator(),
            'defaultTheme'      => new \phpillowStringValidator(),
            'currentTheme'      => new \phpillowStringValidator(),
            'plugins'           => new \phpillowArrayValidator(),
            'articlesPerPage'   => new \phpillowStringValidator(),
            'blogOwner'         => new \phpillowNoValidator(),
            'blogKeywords'      => new \phpillowStringValidator(),
        );
        //Call the parent constructor
        parent::__construct();
    }

    /**
     * Generator method for the document id
     * (required by \phpillowDocument)
     * 
     * @see #getId()
     * @return string
     */
    protected function generateId() {
        return $this->getId();
    }

    /**
     * As this document should be unique this getter method
     * returns a static document id!
     *
     * @return string
     */
    public function getId() {
        return 'configuration';
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
