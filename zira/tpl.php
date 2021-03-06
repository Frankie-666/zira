<?php
/**
 * Zira project
 * tpl.php
 * (c)2015 http://dro1d.ru
 */

function t($str, $arg = null) {
    return Zira\Locale::t($str, $arg);
}

function tm($str, $module, $arg = null) {
    return Zira\Locale::tm($str, $module, $arg);
}

function breadcrumbs() {
    echo Zira\Page::breadcrumbs();
}

function layout_head() {
    echo
        Zira\View::getLayoutData(Zira\View::VAR_HEAD_TOP) .
        Zira\View::getLayoutData(Zira\View::VAR_CHARSET) .
        Zira\View::getLayoutData(Zira\View::VAR_TITLE) .
        Zira\View::getLayoutData(Zira\View::VAR_META) .
        Zira\View::getLayoutData(Zira\View::VAR_STYLES) .
        Zira\View::getLayoutData(Zira\View::VAR_SCRIPTS) .
        Zira\View::getLayoutData(Zira\View::VAR_HEAD_BOTTOM)
    ;
    Zira\View::renderWidgets(Zira\View::VAR_HEAD_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_HEAD_BOTTOM);
}

function layout_body_top() {
    echo Zira\View::getLayoutData(Zira\View::VAR_BODY_TOP);
    Zira\View::includePlaceholderViews(Zira\View::VAR_BODY_TOP);
    Zira\View::renderWidgets(Zira\View::VAR_BODY_TOP);
}

function layout_body_bottom() {
    echo Zira\View::getLayoutData(Zira\View::VAR_BODY_BOTTOM);
    Zira\View::renderWidgets(Zira\View::VAR_BODY_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_BODY_BOTTOM);
}

function layout_content_top() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT_TOP);
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT_TOP);
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT_TOP);
}

function layout_content_bottom() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT_BOTTOM);
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT_BOTTOM);
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT_BOTTOM);
}

function layout_sidebar_left() {
    echo Zira\View::getLayoutData(Zira\View::VAR_SIDEBAR_LEFT);
    Zira\View::renderWidgets(Zira\View::VAR_SIDEBAR_LEFT);
    Zira\View::includePlaceholderViews(Zira\View::VAR_SIDEBAR_LEFT);
}

function layout_sidebar_right() {
    echo Zira\View::getLayoutData(Zira\View::VAR_SIDEBAR_RIGHT);
    Zira\View::renderWidgets(Zira\View::VAR_SIDEBAR_RIGHT);
    Zira\View::includePlaceholderViews(Zira\View::VAR_SIDEBAR_RIGHT);
}

function layout_header() {
    echo Zira\View::getLayoutData(Zira\View::VAR_HEADER);
    Zira\View::includePlaceholderViews(Zira\View::VAR_HEADER);
    Zira\View::renderWidgets(Zira\View::VAR_HEADER);
}

function layout_footer() {
    Zira\View::renderWidgets(Zira\View::VAR_FOOTER);
    Zira\View::includePlaceholderViews(Zira\View::VAR_FOOTER);
    echo Zira\View::getLayoutData(Zira\View::VAR_FOOTER);
}

function layout_content() {
    echo Zira\View::getLayoutData(Zira\View::VAR_CONTENT);
    Zira\View::renderContentData();
    Zira\View::renderWidgets(Zira\View::VAR_CONTENT);
    Zira\View::includePlaceholderViews(Zira\View::VAR_CONTENT);
}