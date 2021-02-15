<?php
if ( strpos($ctrl->baseURL, APPUI_I18N_ROOT.'page/') !== 0 ){

  // the folder of components templates
  $templates = \bbn\File\Dir::getFiles($ctrl->pluginPath().'mvc/html/templates');
  $ctrl->data['templates'] = [];
  $ctrl->obj->url = 'internationalization/page';

  if ( !empty($templates) ){
    $ctrl->data['templates'] = array_map(
      function ($t) use ($ctrl){
        return [
          'id' => basename($t, '.php'),
          'html' => $ctrl->getView('./templates/'.basename($t, '.php'))
        ];
      },
      $templates
    );
  }
  $ctrl->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}