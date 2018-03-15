<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */

if( !empty($id_option = $ctrl->arguments[0])){

  $ctrl->obj->url = APPUI_I18N_ROOT.'languages/strings_tab/'.$id_option;
  //if a cached model for this id_option doesn't exist creates one
  if ( $cached_model = $ctrl->get_cached_model($ctrl->plugin_url('appui-i18n').'/languages_tabs/data/widgets', ['id_option'
  => $id_option]) ){
    

    //if the user is_dev allow to use the class appui\ide to load the clicked file in the ide
    if ( $ctrl->inc->user->is_dev() && $ctrl->has_plugin('appui-ide') ){
      $path = $ctrl->plugin_path('appui-ide');
      $ctrl->register_plugin_classes($path);
      $ide = new \appui\ide($ctrl->inc->options, $ctrl->data['routes'], $ctrl->inc->pref);
      //foreach expression in $cached_model['res']
      foreach ( $cached_model['res'] as $c => $val ){
        foreach ( $val['path'] as $i => $v ){
          //foreach file in which this expression is contained get the real_to_url to give to the js to open the file in ide
          $cached_model['res'][$c]['path'][$i] = $ide->real_to_url($v);
        }
      }
    }

    if ( !empty($cached_model['res']) ){
      //add data to return to js
      $ctrl->add_data([
          'id_option' => $id_option,
          'cached_model' => $cached_model
        ])
        ->combo('$pageTitle', true);
    }
    else{
      //case no strings in the path, now I don't open a tab but I can't
      return $ctrl->data['no_string'] = true;
    }

  }
}