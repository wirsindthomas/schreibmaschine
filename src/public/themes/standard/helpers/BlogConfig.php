<?php
namespace WST\Schreibmaschine\Themes\Standard\Helpers;

class BlogConfig extends \Zend\View\Helper\AbstractHelper
{
    private $blogConfig;

    public function __construct() {
        $this->blogConfig = \Zend\Registry::get('blogConfig');
    }


    public function direct($key = null) {

        if(isset($this->blogConfig->$key)){
            return $this->blogConfig->$key;
        }

    }
}
