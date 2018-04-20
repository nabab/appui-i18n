<?php
// the folder of components templates
$templates = \bbn\file\dir::get_files($ctrl->plugin_path().'mvc/html/templates');
$ctrl->data['templates'] = [];
$ctrl->obj->url = APPUI_I18N_ROOT.'page';
if ( !empty($templates) ){
  $ctrl->data['templates'] = array_map(function($t)use($ctrl){
    return [
      'id' => basename($t, '.php'),
      'html' =>$ctrl->get_view('./templates/'.basename($t, '.php'))
    ];
  }, $templates);
}
$ctrl->set_icon('fa fa-flag')->set_color('#ff9900', '#FFF')->combo('i18n', true);