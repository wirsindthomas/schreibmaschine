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
 * @package    WST\Schreibmaschine\Model\View
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\Model\View;

/**
 * CouchDB view for accessing and listing user documents
 *
 * @uses       \phpillowView
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Model\View
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class User extends \phpillowView {

    /**
     * View definitions
     * @var array
     */
    protected $viewDefinitions = array(
        // Add plain view on all users
        'all' => 'function( doc ){
            if ( doc.type == "user" ){
            emit( null, doc._id );
            }
        }',
        // Add view for all users indexed by their login name
        'byLogin' => 'function( doc ){
            if ( doc.type == "user" ){
                emit( doc.login, doc );
            }
        }',
        // Add view for unregistered users waiting for activation
        'unregistered' => 'function( doc ){
            if ( doc.type == "user" &&
                doc.valid !== "0" &&
                doc.valid !== "1" ){
               emit( doc.valid, doc._id );
            }
        }',
    );

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

    /**
     * Get name of view
     * @return string
     */
    protected function getViewName() {
        return 'users';
    }

}
