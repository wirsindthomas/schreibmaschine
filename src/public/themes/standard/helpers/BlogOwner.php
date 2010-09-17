<?php
namespace WST\Schreibmaschine\Themes\Standard\Helpers;
use WST\Schreibmaschine\Model as Model;

class BlogOwner extends \Zend\View\Helper\AbstractHelper
{
    private $blogOwner;

    public function __construct() {
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
         $this->blogOwner = $user;
    }


    public function direct($key = null) {

        if(isset($this->blogOwner->$key) && $key != 'password'){
            return $this->blogOwner->$key;
        }

    }
}
