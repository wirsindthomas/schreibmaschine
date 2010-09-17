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
 * Model class for blog users.
 *
 * @uses       \phpillowDocument
 * @uses       \phpillowRegexpValidator
 * @uses       \phpillowEmailValidator
 * @uses       \phpillowStringValidator
 * @uses       \phpillowDateValidator
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Model
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class User extends \phpillowDocument {

    /**
     * Document type
     * @var string
     */
    protected static $type = 'user';
    /**
     * List of required properties. For each required property, which is not
     * set, a validation exception will be thrown on save.
     *
     * @var array
     */
    protected $requiredProperties = array(
        'login',
        'password'
    );

    /**
     * Construct new book document and set its property validators.
     *
     * @return void
     */
    public function __construct() {
        $this->properties = array(
            'login' => new \phpillowRegexpValidator('(^[\x21-\x7e]+$)i'),
            'email' => new \phpillowEmailValidator(),
            'name' => new \phpillowStringValidator(),
            'password' => new \phpillowRegexpValidator('(^0|1|[a-f0-9]{32}$)'),
            'description' => new \phpillowStringValidator(),
            'created' => new \phpillowDateValidator(),
        );

        parent::__construct();
    }

    /**
     * Builds a new password hash which gets stored into the database.
     *
     * @uses Salt value set in the application configuration
     * @param string $password	The password that has to be hashed.
     * @return string with hashed password
     * @throws \Exception
     */
    public static function hashPassword($passwordPlain) {

        //Fetch config object from registry
        $config = \Zend\Registry::get('config');

        if (isset($config->base->salt)) {

            //Read static salt from configuration and hash it
            $salt = hash('md5', $config->base->salt);

            //Hash password with salt
            $passwordHashed = hash('sha1', $passwordPlain . $salt);

        }else{
            
            /**
             * @todo Define a typed Exception
             */
            throw new \Exception('No salt value set in configuration.');

        }

        //return hashed password
        return $passwordHashed;
    }

    /**
     * Get ID from document
     *
     * The ID normally should be calculated on some meaningful / unique
     * property for the current ttype of documents. The returned string should
     * not be too long and should not contain multibyte characters.
     *
     * You can return null instead of an ID string, to trigger the ID
     * autogeneration.
     *
     * @return mixed
     */
    protected function generateId() {
        return $this->stringToId($this->storage->login);
    }

    /**
     * Return document type name
     *
     * This method is required to be implemented to return the document type
     * for PHP versions lower then 5.2. When only using PHP 5.3 and higher you
     * might just implement a method which does "return static:$type" in a base
     * class.
     *
     * @return void
     */
    protected function getType() {
        return self::$type;
    }

    /**
     * Create a new instance of the document class
     *
     * Create a new instance of the statically called document class.
     * Implementing this method should only be required when using PHP 5.2 and
     * lower, otherwise the class can be determined using LSB.
     *
     * Do not pass a parameter to this method, this is only used to maintain
     * the called class information for PHP 5.2 and lower.
     *
     * @param mixed $docType
     * @returns phpillowDocument
     */
    public static function createNew($docType = null) {
        return parent::createNew($docType === null ? __CLASS__ : $docType);
    }

}

