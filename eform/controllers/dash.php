<?php
/**
 * Zira project.
 * dash.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Controllers;

use Zira;
use Eform;

class Dash extends \Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getFormWindowModel() {
        $window = new Eform\Windows\Eforms();
        return new Eform\Models\Eforms($window);
    }

    protected function getFieldWindowModel() {
        $window = new Eform\Windows\Eformfields();
        return new Eform\Models\Eformfields($window);
    }

    public function drag() {
        if (Zira\Request::isPost()) {
            $eform = Zira\Request::post('item');
            $fields = Zira\Request::post('fields');
            $orders = Zira\Request::post('orders');
            $response = $this->getFieldWindowModel()->drag($eform, $fields, $orders);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $eform = Zira\Request::post('item');
            $response = $this->getFormWindowModel()->info($eform);
            Zira\Page::render($response);
        }
    }
}