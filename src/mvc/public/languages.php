<?php

//non so perchè torna vuoto
$templates = \bbn\file\dir::get_files($ctrl->plugin_path().'mvc/html/templates');
$ctrl->data['templates'] = [];
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

