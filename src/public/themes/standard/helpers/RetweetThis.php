<?php

namespace WST\Schreibmaschine\Themes\Standard\Helpers;

use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/models/PluginDatastore.php';
require_once APPLICATION_PATH . '/views/helpers/AbstractHelper.php';

class RetweetThis extends \WST\Schreibmaschine\View\Helpers\AbstractHelper {

    protected $name = 'retweet-this';

    public function direct($title = null, $link = '', $options = array()) {
        $account = $this->getData('account');

        $ret = '<a href="http://twitter.com/home/?status=';
        $ret .= 'RT+' . $account . \rawurlencode(' ' . $title . ' (' . $this->view->hostUrl . $link . ')') . '"';

        foreach ((array) $options as $key => $value) {
            $ret .= ' ' . strtolower($key) . '="' . strtolower($value) . '"';
        }

        $ret .= 'title="Retweet this"';
        $ret .= '>Retweet this</a>';

        return $ret;
    }

	public function getName() {
		return $this->name;
	}
}
