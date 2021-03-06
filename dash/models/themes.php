<?php
/**
 * Zira project.
 * themes.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Themes extends Model {
    public function activate($theme) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS) || !Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $available_themes = $this->getWindow()->getAvailableThemes();
        $current_theme = Zira\Config::get('theme');

        if (!array_key_exists($theme, $available_themes)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!$theme != $current_theme) {
            $option = Zira\Models\Option::getCollection()
                                                ->select('id')
                                                ->where('name','=','theme')
                                                ->get(0);

            if (!$option) {
                $optionObj = new Zira\Models\Option();
            } else {
                $optionObj = new Zira\Models\Option($option->id);
            }

            $optionObj->name = 'theme';
            $optionObj->value = $theme;
            $optionObj->module = 'zira';
            $optionObj->save();

            Zira\Models\Option::raiseVersion();
        }

        return array('reload'=>$this->getJSClassName());
    }
}