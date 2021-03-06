<?php
/**
 * Zira project.
 * records.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Records extends Model {
    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_DELETE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $types = Zira\Request::post('types');

        $category_error = false;
        foreach($data as $i=>$id) {
            if (!array_key_exists($i, $types)) continue;
            if ($types[$i]=='category') {
                $category = new Zira\Models\Category($id);
                if (!$category->loaded()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                };

                $category_empty = true;

                $rows = Zira\Models\Category::getCollection()
                    ->where('name', '=', $category->name)
                    ->union()
                    ->where('name', 'like', $category->name . '/%')
                    ->merge()
                    ->get();

                foreach($rows as $row) {
                    $co=Zira\Models\Record::getCollection()
                        ->count()
                        ->where('category_id','=',$row->id)
                        ->get('co');
                    if ($co>0) {
                        $category_empty = false;
                        break;
                    }
                }

                if ($category_empty) {
                    Zira\Models\Widget::getCollection()
                                ->where('name','=',Zira\Models\Category::WIDGET_CLASS)
                                ->and_where('params','=', $category->id)
                                ->delete()
                                ->execute();

                    Zira\Models\Widget::getCollection()
                                ->where('category_id','=',$category->id)
                                ->delete()
                                ->execute();

                    $subrows = Zira\Models\Category::getCollection()
                                    ->where('name', 'like', $category->name . '/%')
                                    ->get();

                    foreach($subrows as $subcategory) {
                        Zira\Models\Widget::getCollection()
                                    ->where('name','=',Zira\Models\Category::WIDGET_CLASS)
                                    ->and_where('params','=', $subcategory->id)
                                    ->delete()
                                    ->execute();

                        Zira\Models\Widget::getCollection()
                                    ->where('category_id','=',$subcategory->id)
                                    ->delete()
                                    ->execute();
                    }

                    Zira\Models\Category::getCollection()
                        ->delete()
                        ->where('name', 'like', $category->name . '/%')
                        ->execute();

                    $category->delete();
                } else {
                    $category_error = true;
                }
            } else if ($types[$i]=='record') {
                $record = new Zira\Models\Record($id);
                if (!$record->loaded()) {
                    return array('error' => Zira\Locale::t('An error occurred'));
                };
                $record->delete();

                if ($record->thumb) {
                    $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $record->thumb);
                    if (file_exists($thumb)) @unlink($thumb);
                }

                $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$record->id)
                            ->get();

                $gthumbs = array();
                foreach($images as $image) {
                    if (!$image->thumb) continue;
                    $thumb = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image->thumb);
                    if (!file_exists($thumb)) continue;
                    $gthumbs []= $thumb;
                }

                Zira\Models\Image::getCollection()
                            ->delete()
                            ->where('record_id','=',$record->id)
                            ->execute();

                foreach($gthumbs as $thumb) {
                    @unlink($thumb);
                }

                Zira\Models\Search::clearRecordIndex($record);
            }
        }

        Zira\Cache::clear();

        if (!$category_error) {
            return array('reload' => $this->getJSClassName());
        } else {
            return array('reload' => $this->getJSClassName(), 'error'=>Zira\Locale::t('Cannot delete category that contains records'));
        }
    }

    public function setCategoryDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category = new Zira\Models\Category($id);
        if (!$category->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$category->description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category->description = Zira\Helper::utf8Clean(strip_tags($description));
        $category->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function setRecordDescription($id, $description) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$record->description && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record->description = Zira\Helper::utf8Clean(strip_tags($description));
        // keep draft
        //$record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function setRecordImage($id, $image) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $image)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        if (!$record->image && !$record->thumb && !Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        } else if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $record->category_id, $record->id);
        if (!$thumb) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->thumb = $thumb;
        $record->image = str_replace(DIRECTORY_SEPARATOR, '/', $image);
        // keep draft
        //$record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function copyRecord($root, $id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = trim($root,'/');

        if (!empty($root)) {
            $category = Zira\Models\Category::getCollection()
                ->where('name', '=', $root)
                ->get(0);

            if (!$category) {
                return array('error' => Zira\Locale::t('Category not found'));
            }
            $category_id = $category->id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $name = $record->name;
        $co=0;
        do {
            if ($co>0) $_name = $name .'-'.$co;
            else $_name = $name;
            $query = Zira\Models\Record::getCollection();
            $query->count();
            $query->where('category_id', '=', $category_id);
            $query->and_where('language', '=', $record->language);
            $query->and_where('name', '=', $_name);
            $co++;
        } while($query->get('co') > 0);

        $recordArr = $record->toArray();
        unset($recordArr['id']);
        $recordArr['name'] = $_name;
        $recordArr['category_id'] = $category_id;
        $recordArr['thumb'] = null;
        $recordArr['creation_date'] = date('Y-m-d H:i:s');
        $recordArr['modified_date'] = date('Y-m-d H:i:s');
        $new = new Zira\Models\Record();
        $new->loadFromArray($recordArr);
        $new->save();

        if ($new->image) {
            $image = str_replace('/', DIRECTORY_SEPARATOR, $new->image);
            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $new->category_id, $new->id);
            if ($thumb) {
                $new->thumb = $thumb;
                $new->save();
            }
        }

        $images = Zira\Models\Image::getCollection()
                            ->where('record_id','=',$record->id)
                            ->order_by('id', 'asc')
                            ->get();

        foreach($images as $_image) {
            $image = str_replace('/', DIRECTORY_SEPARATOR, $_image->image);
            $thumb = Zira\Page::createRecordThumb(ROOT_DIR . DIRECTORY_SEPARATOR . $image, $new->category_id, $new->id, true);
            if (!$thumb) continue;

            $imageObj = new Zira\Models\Image();
            $imageObj->record_id = $new->id;
            $imageObj->thumb = $thumb;
            $imageObj->image = $_image->image;
            $imageObj->description = $_image->description;
            $imageObj->save();
        }

        Zira\Models\Search::indexRecord($new);

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function moveRecord($root, $id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = trim($root,'/');

        if (!empty($root)) {
            $category = Zira\Models\Category::getCollection()
                ->where('name', '=', $root)
                ->get(0);

            if (!$category) {
                return array('error' => Zira\Locale::t('Category not found'));
            }
            $category_id = $category->id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->category_id = $category_id;
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName());
    }

    public function publishRecord($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->published = Zira\Models\Record::STATUS_PUBLISHED;
        $record->save();

        Zira\Models\Search::indexRecord($record);

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function publishRecordOnFrontPage($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CREATE_RECORDS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }

        $record->front_page = Zira\Models\Record::STATUS_FRONT_PAGE;
        $record->save();

        Zira\Cache::clear();

        return array('reload' => $this->getJSClassName(),'message' => Zira\Locale::t('Successfully saved'));
    }

    public function createCategoryWidget($id) {
        if (empty($id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $category = new Zira\Models\Category(intval($id));
        if (!$category->loaded()) return array('error' => Zira\Locale::t('An error occurred'));

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Zira\Models\Category::WIDGET_CLASS;
        $widget->module = 'zira';
        $widget->placeholder = Zira\Models\Category::WIDGET_PLACEHOLDER;
        $widget->params = $category->id;
        $widget->category_id = null;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Activated %s widgets', 1));
    }

    public function info($id) {
        if (!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array();
        }

        $info = array();

        $record = new Zira\Models\Record($id);
        if (!$record->loaded()) return array();

        if ($record->front_page) {
            $info[] = '<span class="glyphicon glyphicon-home"></span> ' . Zira\Locale::t('ID: %s', $record->id);
        } else {
            $info[] = '<span class="glyphicon glyphicon-tag"></span> ' . Zira\Locale::t('ID: %s', $record->id);
        }
        $info[] = '<span class="glyphicon glyphicon-paperclip"></span> ' . Zira\Helper::html($record->title);
        $info[] = '<span class="glyphicon glyphicon-thumbs-up"></span> ' . Zira\Locale::t('Rating: %s', Zira\Helper::html($record->rating));
        $info[] = '<span class="glyphicon glyphicon-comment"></span> ' . Zira\Locale::t('Comments: %s', Zira\Helper::html($record->comments));
        $info[] = '<span class="glyphicon glyphicon-time"></span> ' . date(Zira\Config::get('date_format'), strtotime($record->creation_date));

        return $info;
    }
}