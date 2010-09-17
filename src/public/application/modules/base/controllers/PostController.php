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
 * Controller for listing and displaying blog posts
 *
 * @uses       \WST\Schreibmaschine\Controllers\DefaultController
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Modules\Base\Controller
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class PostController extends \WST\Schreibmaschine\Controllers\DefaultController {

    /**
     * Index action forwards to the list of latest posts
     * @see #listAction()
     */
    public function indexAction() {
        $this->_forward('list');
    }

    /**
     * List of latest posts
     * 
     * @param int $page current page of displayed articles
     * @return html formatted string
     */
    public function listAction() {

        //Receive current page parameter (defaults to 0
        $page = (int) $this->_getParam('page', 0);


        //If $page is > 0 subtract one due to zero based paging
        if ($page > 0) {
            $page -= 1;
        }

        //get number of articles per page from blog config document
        $aPP = (int) $this->blogConfig()->revisions[0]['articlesPerPage'];

        //Set params for calling the CouchDb View
        $listPostsViewParams = array(
            'descending' => true,
            'limit' => $aPP,
            'skip' => $aPP * $page);

        /**
         * @todo Loader
         */
        require_once APPLICATION_PATH . '/models/views/ListPosts.php';

        //Query the database for a list of the latest posts
        $this->view->posts = WST\Schreibmaschine\Model\View\ListPosts::posts($listPostsViewParams);


        //Count if there are more posts left than articles per page
        $countPostsViewParams = array(
            'descending' => true,
            'skip' => $aPP * $page);

        //Query the database for a number of the posts left to display
        $countPostsLeft = WST\Schreibmaschine\Model\View\ListPosts::posts($countPostsViewParams);

        //Calculate next and previous page number
        $this->view->previousPage = $page;

        if (count($countPostsLeft->rows) > $aPP) {
            $this->view->nextPage = $page + 2;
        } else {
            $this->view->nextPage = null;
        }
    }

    /**
     * Displays a single post
     *
     * @param string $id ID of the post
     * @return html formatted string
     */
    public function showAction() {
        $id = $this->_getParam('id', '');

        /**
         * @todo Filter Id
         */
        if (!empty($id)) {
            //Create a new Post document
            $post = new Model\Post();
            try {
                //Try to find a document with $if in the databse
                $post->fetchById($id);

                //Handle tags
                $post->tags = is_array($post->tags) ? $post->tags : array();

                //Save the post as a member of the view
                $this->view->post = $post;

            } catch (\phpillowResponseNotFoundErrorException $e) {

                /**
                 * @todo Implement proper error handling
                 */
                var_dump($e);
            }
        }
    }

}
