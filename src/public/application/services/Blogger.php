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
 * @package    WST\Schreibmaschine\Service
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\Service;

use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/models/Post.php';

/**
 * Service class implementing the Blogger API
 *
 * @see        http://www.blogger.com/developers/api/1_docs/
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Service
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class Blogger {

    /**
     * Returns information on all the blogs a given user is a member of.
     * @param string $appkey
     * @param string $username
     * @param string $password
     * @return struct
     * @todo Password is not veryfied yet!
     */
    public function getUsersBlogs($appkey, $username, $password) {

        //Set params for calling the CouchDb View
        $findUserByIdParams = array(
            'limit' => 1,
            'key' => $username);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/User.php';

        //Fetch user from the database
        $user = Model\View\User::byLogin($findUserByIdParams);

        //Exit if user was not found
        if ($user->rows[0]['id'] == '') {

            return false;
        }

        $config = \Zend\Registry::get('config');
        $blogConfig = \Zend\Registry::get('blogConfig');

        $structArray = array();
        $structArray[] = array(
            'url' => $config->hostUrl,
            'blogid' => 1,
            'blogName' => $blogConfig->revisions[0]['blogTitle'],
        );
        return $structArray;
    }

    /**
     * Authenticates a user and returns basic user info (name, email, userid, etc.).
     * @param string $appkey
     * @param string $username
     * @param string $password
     * @return struct
     */
    public function getUserInfo($appkey, $username, $password) {

        //Set params for calling the CouchDb View
        $findUserByIdParams = array(
            'limit' => 1,
            'key' => $username);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/User.php';
        $user = Model\View\User::byLogin($findUserByIdParams);

        if ($user->rows[0]['id'] == '') {

            return false;


        } else if ($user->rows[0]['password'] == Model\User::hashPassword($password)) {

            $structArray = array();
            $structArray[] = array(
                'nickname' => $user->rows[0]['login'],
                'userid' => $user->rows[0]['_id'],
                'email' => $user->rows[0]['email'],
                'lastname' => $user->rows[0]['name'],
                'firstname' => ''

                );

            return $structArray;
        }
    }

    /**
     * Delete a post from the database
     * @param string $appkey
     * @param string $postid
     * @param string $username
     * @param string $password
     * @param bool   $publish
     * @return bool
     * @todo Password is not veryfied yet!
     */
    public function deletePost($appkey, $postid, $username, $password, $publish) {

        //Set params for calling the CouchDb View
        $findUserByIdParams = array(
            'limit' => 1,
            'key' => $username);
        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/User.php';
        $user = Model\View\User::byLogin($findUserByIdParams);

        if ($user->rows[0]['id'] == '') {

            return false;
        }

        $doc = new Model\Post();
        /**
         * @todo error handling
         */
        $doc->fetchById($postid);

        $db = \phpillowConnection::getInstance();

        // Delete document in database
        $response = $db->delete(
                        \phpillowConnection::getDatabase() . urlencode($doc->_id) . '?rev=' . $doc->_rev
        );

        return $response->ok;
    }

}
