<?php
/**
 * Zira project.
 * record.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class Records extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    protected function getWindowModel() {
        $window = new Dash\Windows\Records();
        return new Dash\Models\Records($window);
    }

    protected function getImagesModel() {
        $window = new Dash\Windows\Recordimages();
        return new Dash\Models\Recordimages($window);
    }

    protected function getSlidesModel() {
        $window = new Dash\Windows\Recordslides();
        return new Dash\Models\Recordslides($window);
    }

    protected function getEditorModel() {
        $class = (string)Zira\Request::post('class');
        $window = new Dash\Windows\Recordhtml();
        if ($window->getJSClassName() != $class) {
            $window = new Dash\Windows\Recordtext();
            $model = new Dash\Models\Recordtext($window);
        } else {
            $model = new Dash\Models\Recordhtml($window);
        }
        return $model;
    }

    public function description() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->setRecordDescription($id, $description);
            Zira\Page::render($response);
        }
    }

    public function image() {
        if (Zira\Request::isPost()) {
            $image = Zira\Request::post('image');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->setRecordImage($id, $image);
            Zira\Page::render($response);
        }
    }

    public function copy() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->copyRecord($root, $id);
            Zira\Page::render($response);
        }
    }

    public function move() {
        if (Zira\Request::isPost()) {
            $root = Zira\Request::post('root');
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->moveRecord($root, $id);
            Zira\Page::render($response);
        }
    }

    public function publish() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->publishRecord($id);
            Zira\Page::render($response);
        }
    }

    public function frontpage() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->publishRecordOnFrontPage($id);
            Zira\Page::render($response);
        }
    }

    public function addimage() {
        if (Zira\Request::isPost()) {
            $images = Zira\Request::post('images');
            $id = Zira\Request::post('item');
            $response = $this->getImagesModel()->addRecordImages($id, $images);
            Zira\Page::render($response);
        }
    }

    public function imagedesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getImagesModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }

    public function addslide() {
        if (Zira\Request::isPost()) {
            $images = Zira\Request::post('images');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->addRecordSlides($id, $images);
            Zira\Page::render($response);
        }
    }

    public function slidedesc() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $response = $this->getSlidesModel()->saveDescription($id, $description);
            Zira\Page::render($response);
        }
    }

    public function savedraft() {
        if (Zira\Request::isPost()) {
            $content = Zira\Request::post('content');
            $id = Zira\Request::post('item');
            $response = $this->getEditorModel()->saveDraft($id, $content);
            Zira\Page::render($response);
        }
    }

    public function draft() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getEditorModel()->loadDraft($id);
            Zira\Page::render($response);
        }
    }

    public function widget() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->createCategoryWidget($id);
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $id = Zira\Request::post('item');
            $response = $this->getWindowModel()->info($id);
            Zira\Page::render($response);
        }
    }
}