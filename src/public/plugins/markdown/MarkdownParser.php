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
 * @package    WST\Schreibmaschine
 * @version    $Id$
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */

namespace WST\Schreibmaschine\Plugins\Markdown;

require_once PLUGIN_PATH . '/markdown/library/Markdown/markdown.php';

/**
 * Plugin for parsing markdown formatted text into html
 *
 * @uses       \Zend\Application\Bootstrap
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class MarkdownParser extends \Zend\View\Helper\AbstractHelper {

    /**
     *
     * @var \Markdown_Parser
     */
    private $markdown;

    /**
     * Constructor initializes the Markdown_Parser object
     */
    public function __construct() {
        $this->markdown = new \Markdown_Parser();
    }

    /**
     * View helper method for transforming a markdown formatted string into html
     * @param string $text
     * @return html formatted string
     */
    public function direct($text = '') {
        if (!empty($text)) {
            return $this->markdown->transform($text);
        } else {
            return null;
        }
    }

}
