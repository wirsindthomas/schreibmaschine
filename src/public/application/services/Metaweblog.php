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
 * Service class implementing the MetaWeblog API
 *
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Service
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class Metaweblog {

    /**
     * @param string $blogid
     * @param string $username
     * @param string $password
     * @param struct $struct
     * @param boolean $publish
     * @return string
     */
    public function newPost($blogid, $username, $password, $struct, $publish) {

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
        $log = \Zend\Registry::get('log');

        $doc->title = $struct['title'];
        $doc->text = $struct['description'] . PHP_EOL . $struct['mt_text_more'];
        $doc->created = date('r');
	$doc->author = $user->rows[0]['id'];

        if (isset($struct['mt_tags'])) {
            $tagsArray = explode(',', $struct['mt_tags']);
            $docTags = array();
            $log->info(print_r($tagsArray, 1));
            foreach ((array) $tagsArray as $tag) {
                $log->info(print_r($tag, 1));
                $docTags[] = trim($tag);
            }
            $doc->tags = (array) $docTags;
        }


        if ($publish == true) {
            $doc->publish = true;
        } else {
            $doc->publish = false;
        }
        /**
         * @todo error handling
         */
        $id = $doc->save();

        return $id;
    }

    /**
     * @param string $postid
     * @param string $username
     * @param string $password
     * @param struct $struct
     * @param boolean $publish
     * @return string
     */
    public function editPost($postid, $username, $password, $struct, $publish) {

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

        $doc->title = $struct['title'];
        $doc->text = $struct['description'] . PHP_EOL . $struct['mt_text_more'];
        $doc->created = isset($struct['date']) ? $struct['date'] : \date('r');
        $doc->author = $user->rows[0]['id'];

        if (isset($struct['mt_tags'])) {
            $tagsArray = explode(',', $struct['mt_tags']);
            $docTags = array();
            $log->info(print_r($tagsArray, 1));
            foreach ((array) $tagsArray as $tag) {
                $log->info(print_r($tag, 1));
                $docTags[] = trim($tag);
            }
            $doc->tags = (array) $docTags;
        }


        if ($publish == true) {
            $doc->publish = true;
        } else {
            $doc->publish = false;
        }
        /**
         * @todo error handling
         */
        $id = $doc->save();

        return $id;
    }

    /**
     * @param string $postid
     * @param string $username
     * @param string $password
     * @return struct
     */
    public function getPost($postid, $username, $password) {

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
        $date = new \Zend\Date\Date($doc->created);

        $post = array(
            'userId' => $user->rows[0]['id'],
            'dateCreated' => $date,
            'title' => $doc->title,
            'content' => $doc->text,
            'postid' => $doc->_id
        );


        return $post;
    }

    /**
     * @param string $blogid
     * @param string $username
     * @param string $password
     * @param int $numberOfPosts
     * @return struct
     */
    public function getRecentPosts($blogid, $username, $password, $numberOfPosts) {

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

        $ret = array();

        //Set params for calling the CouchDb View
        $listPostsViewParams = array(
            'descending' => true,
            'limit' => $numberOfPosts);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/ListPosts.php';
        $posts = Model\View\ListPosts::posts();

        $ret = array();

        if (isset($posts)) {
            foreach ($posts->rows as $post) {

                $date = new \Zend\Date\Date($post['value']['created']);

                $post = array(
                    'userId' => $user->rows[0]['id'],
                    'dateCreated' => $date,
                    'title' => $post['value']['title'],
                    'description' => $post['value']['text'],
                    'postid' => $post['id'],
                    'publish' => $post['value']['publish'],
                );
                $ret[] = $post;
            }
        }

        return $ret;
    }

    /**
     * @todo There are no Categories so this method is just a stub.
     * @param string $blogid
     * @param string $username
     * @param string $password
     * @return struct
     */
    public function getCategories($blogid, $username, $password) {

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

        /**
         * @todo This is a stub
         */

        $categories = array(
            array('title' => 'test',
                'description' => 'test',
                'htmlUrl' => 'http://localhost:8888',
                'rssUrl' => 'http://localhost:8888/feed/rss'
            )
        );

        return $categories;
    }

    /**
     * @todo not implemented!
     * @param <type> $blogid
     * @param <type> $username
     * @param <type> $password
     * @param <type> $struct
     */
    public function newMediaObject($blogid, $username, $password, $struct) {

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

        /**
         * @todo Implement me!
         */

        $log = \Zend\Registry::get('log');
        $log->info(print_r(func_get_args(), 1));
    }

}
