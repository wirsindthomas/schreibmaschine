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

//@todo Loader
require_once APPLICATION_PATH . '/controllers/DefaultController.php';
require_once APPLICATION_PATH . '/models/Post.php';

/**
 * @todo Namespaces are currently not working for Controller Classes
 */

use WST\Schreibmaschine\Model as Model;

/**
 * Controller class for generating and delivering feeds in RSS 2.0 and Atom format
 *
 * @todo Caching must be implemented asap
 *
 * @see        http://www.blogger.com/developers/api/1_docs/
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class FeedController extends \WST\Schreibmaschine\Controllers\DefaultController {

    public $feed;

    /**
     * Initializes the feed and stores it as a class member.
     */
    public function init() {
        parent::init();

        //Header
        $blogConfig = \Zend\Registry::get('blogConfig');


        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/User.php';
        $user = new Model\User();
        $user->fetchById($blogConfig->revisions[0]['blogOwner']);

        if ($user->_id == '') {

            throw new \Exception('Blog owner could not be found in the database.');
        }

        $this->feed = new \Zend\Feed\Writer\Feed();
        $this->feed->setTitle($blogConfig->revisions[0]['blogTitle']);
        $this->feed->setLink($this->view->hostUrl);
        $this->feed->setFeedLink($this->view->hostUrl . '/feed/atom', 'atom');
        $this->feed->setFeedLink($this->view->hostUrl . '/feed/rss', 'rss');
		$description = strip_tags($blogConfig->revisions[0]['blogDescription']);
        $this->feed->setDescription($description);
        $this->feed->addAuthor(array(
            'name' => $user->name,
            'email' => $user->email,
            'uri' => $this->view->hostUrl,
        ));

        $this->feed->setDateModified(time());


        //Content

        //Set params for calling the CouchDb View
        $listPostsViewParams = array(
            'descending' => true,
            'limit' => 10);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/ListPosts.php';
        $posts = WST\Schreibmaschine\Model\View\ListPosts::posts($listPostsViewParams);
        $authors = array();

        foreach ($posts->rows as $post) {

            //if author was not fetched yet
            if (array_key_exists($post['value']['author'], $authors) === false) {

                require_once APPLICATION_PATH . '/models/User.php';
                $author = new Model\User();

                $author->fetchById($post['value']['author']);

                //if author could not be found throw exception
                if ($author->_id == '') {

                    throw new \Exception('Author with id '.$post['value']['author'].' could not be found in the database.');

                //else add the author to the authors array
                } else {

                    $authors[$post['value']['author']] = $author;
                }

                //else use the authors array
            } else {

                $author = $authors[$post['value']['author']];
            }


            //Create feed entry
            $entry = $this->feed->createEntry();
            $entry->setTitle($post['value']['title']);
            $entry->setLink($this->view->hostUrl . '/post/view/id/' . $post['id']);
            $entry->addAuthor(array(
                'name' => $author->name,
                'email' => $author->email,
                'uri' => $this->view->hostUrl,
            ));

            $entry->setDateModified(time());
            $entry->setDateCreated(new \Zend\Date\Date($post['value']['created']));

            $entry->setDescription($post['value']['text']);

            //$entry->setContent($post['value']['text']);

            $this->feed->addEntry($entry);
        }
    }

    /**
     * Index action forwards to rssAction()
     * @see #rssAction()
     */
    public function indexAction() {
        $this->_forward('rss');
    }

    /**
     * Outputs the feed as a atom XML feed
     */
    public function atomAction() {
        header("Content-Type: application/xml;");
        $out = $this->feed->export('atom');

        echo $out;
        exit;
    }

    /**
     * Outputs the feed as a rss 2.0 XML feed
     */
    public function rssAction() {
        header("Content-Type: application/xml;");
        $out = $this->feed->export('rss');
        echo $out;
        exit;
    }

}

