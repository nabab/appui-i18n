<?php
$templates = \bbn\file\dir::get_files($ctrl->plugin_path().'mvc/html/templates');
$ctrl->data['templates'] = [];
$ctrl->obj->url = APPUI_I18N_ROOT.'languages';
if ( !empty($templates) ){
  $ctrl->data['templates'] = array_map(function($t)use($ctrl){
    return [
      'id' => basename($t, '.php'),
      'html' =>$ctrl->get_view('./templates/'.basename($t, '.php'))
    ];
  }, $templates);
}



$ctrl->combo('Translations home', true);
return[ $ctrl->obj->url => APPUI_INTERNATIONALIZATION_ROOT . 'languages' ];