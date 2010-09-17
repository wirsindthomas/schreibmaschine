<?php
namespace WST\Schreibmaschine\Themes\Standard\Helpers;

class FormatDate extends \Zend\View\Helper\AbstractHelper
{
    public function direct($dateString = null, $format = \Zend\Date\Date::DATETIME_MEDIUM) {
        $date = new \Zend\Date\Date($dateString);

        return $date->get($format);
    }
}
