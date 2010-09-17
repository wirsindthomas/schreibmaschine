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
namespace WST\Schreibmaschine\Plugins\Widgets;
use WST\Schreibmaschine\Model as Model;
/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/models/PluginDatastore.php';

/**
 * Plugin for displaying widgets
 *
 * @todo does not really implement the plugin datastore API yet.
 *
 * @uses       WST\Schreibmaschine\Model
 * @category   Schreibmaschine
 * @package    WST\Schreibmaschine
 * @copyright  Copyright (c) 2010 Alexander Thomas (at@wirsindthomas.com)
 * @license    http://creativecommons.org/licenses/LGPL/2.1/    GNU Lesser General Public License
 */
class DisplayWidget extends \Zend\View\Helper\AbstractHelper {

    private $name = 'widgets';
    private $datastore;

    public function __construct() {
        $this->datastore = new Model\PluginDatastore();
        try {
            $id = $this->datastore->getType() . '-' . $this->name;
            $this->datastore->fetchById($id);
        } catch (\phpillowResponseNotFoundErrorException $e) {

            $this->datastore->name = $this->name;
            $this->datastore->created = date('r');

            $this->datastore->save();
            
        }
    }

    public function direct($key = '') {
        if (isset($this->datastore->revisions[0]['data'][$key])) {
            return $this->datastore->revisions[0]['data'][$key];
        } else {
            return null;
        }
    }

}
