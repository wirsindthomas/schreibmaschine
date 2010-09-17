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
 * CouchDB view for listing all posts.
 *
 * @uses       \phpillowView
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine\Model\View
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class ListPosts extends \phpillowView {

    /**
     * View definitions
     * @var array
     */
    protected $viewDefinitions = array(
        'posts' => 'function( doc )
	{
		if ( doc.type == "post" )
		{
			emit(Date.parse(doc.created), doc);
		}
	}',
    );

    protected function getViewName() {
        return 'list_posts';
    }

}
