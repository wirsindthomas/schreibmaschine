<?php

namespace WST\Schreibmaschine\Themes\Standard\Helpers;

use WST\Schreibmaschine\Model as Model;

/**
 * @todo Loader
 */
require_once APPLICATION_PATH . '/models/PluginDatastore.php';
require_once APPLICATION_PATH . '/views/helpers/AbstractHelper.php';

class GoogleAnalytics extends \WST\Schreibmaschine\View\Helpers\AbstractHelper {

    protected $name = 'google-analytics';

    public function direct() {

        $account = $this->getData('account');

        $ret = "<!-- asynchronous google analytics: mathiasbynens.be/notes/async-analytics-snippet -->
                <script>
                var _gaq = [['_setAccount', '".$account."'], ['_trackPageview']];
                (function(d, t) {
                    var g = d.createElement(t),
                    s = d.getElementsByTagName(t)[0];
                    g.async = true;
                    g.src = '//www.google-analytics.com/ga.js';
                    s.parentNode.insertBefore(g, s);
                    })(document, 'script');
                </script>";
 

        return $ret;
    }

public function getName() {
	return $this->name;
}

}
