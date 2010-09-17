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

/**
 * @todo Namespaces are not yet working for controller classes as they're obviously not recognised by the frontcontroller
 */
//namespace WST\Schreibmaschine\Modules\Base\Controller;
use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/controllers/DefaultController.php';
require_once APPLICATION_PATH . '/models/Post.php';

/**
 * Controller class for generating feeds
 *
 * @uses       \WST\Schreibmaschine\Controllers\DefaultController
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class FeedController extends \WST\Schreibmaschine\Controllers\DefaultController {

    /**
     *
     * @var \Zend\Feed\Writer\Feed
     */
    public $feed;

    /**
     * Initializes the feed and stores it as a class member.
     * @todo Should be outsourced into a service class
     * @todo Implement caching!
     */
    public function init() {
        parent::init();

        //Get blog configuration document from the registry
        $blogConfig = \Zend\Registry::get('blogConfig');


        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/User.php';

        //Fetch blog owner from the database
        $user = new Model\User();
        $user->fetchById($blogConfig->revisions[0]['blogOwner']);

        if ($user->_id == '') {
            /**
             * @todo Implement proper error handling
             */
            throw new \Exception('Blog owner could not be found in the database.');
        }

        //Setup feed object
        $this->feed = new \Zend\Feed\Writer\Feed();
        $this->feed->setTitle($blogConfig->revisions[0]['blogTitle']);
        $this->feed->setLink($this->view->hostUrl);
        $this->feed->setFeedLink($this->view->hostUrl . '/feed/atom', 'atom');
        $this->feed->setFeedLink($this->view->hostUrl . '/feed/rss', 'rss');
        $this->feed->setDescription($blogConfig->revisions[0]['blogDescription']);
        $this->feed->addAuthor(array(
            'name' => $user->name,
            'email' => $user->email,
            'uri' => $this->view->hostUrl,
        ));

        $this->feed->setDateModified(time());


        //Fetch posts for displaying it in the feed
        //Set params for calling the CouchDb View
        $listPostsViewParams = array(
            'descending' => true,
            'limit' => 10);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/ListPosts.php';

        //Query the database for a list of the latest posts
        $posts = WST\Schreibmaschine\Model\View\ListPosts::posts($listPostsViewParams);

        //Temporary cache for author documents
        $authors = array();

        //Add posts to the feed
        foreach ($posts->rows as $post) {

            //if author was not fetched yet
            if (array_key_exists($post['value']['author'], $authors) === false) {

                /**
                 * @todo Loader
                 */
                require_once APPLICATION_PATH . '/models/User.php';

                //Fetch author document from the database
                $author = new Model\User();
                $author->fetchById($post['value']['author']);

                //if author could not be found throw exception
                if ($author->_id == '') {
                    /**
                     * @todo Implement proper error handling
                     */
                    throw new \Exception('Author with id ' . $post['value']['author'] . ' could not be found in the database.');

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

            /**
             * @todo Posts don't have a modified date yet.
             */
            $entry->setDateModified(time());
            $entry->setDateCreated(new \Zend\Date\Date($post['value']['created']));

            /**
             * @todo How can markdown formatted posts be handled (Markdown is implemented as a view related plugin)?
             */
            $entry->setDescription($post['value']['text']);

            //$entry->setContent($post['value']['text']);
            //Add post to feed
            $this->feed->addEntry($entry);
        }
    }

    /**
     * Index action forwards to rssAction()
     * @see #rssAction()
     */
    public function indexAction() {
        $this->_forward('list');
    }

    /**
     * Outputs the feed as an atom XML feed
     *
     * @return xml formated string
     */
    public function atomAction() {
        //Set output header
        header("Content-Type: application/xml;");

        //Get feed formatted as atom
        $out = $this->feed->export('atom');

        echo $out;
        exit;
    }

    /**
     * Outputs the feed as a rss 2.0 XML feed
     *
     * @return xml formated string
     */
    public function rssAction() {
        //Set output header
        header("Content-Type: application/xml;");

        //Get feed formatted as rss
        $out = $this->feed->export('rss');

        echo $out;
        exit;
    }

}

