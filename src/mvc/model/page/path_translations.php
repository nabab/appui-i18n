<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 14/12/17
 * Time: 13.05
 */

/* @var string ID of the path to analyze is expected */

if ( !empty($model->data['id_option']) && ($id_project = $model->data['id_project']) ){
  //REMAKE THE CACHE OF THE WIDGETS
  /** @var  $translation instantiate the class appui\i18n*/
  $translation = new \bbn\appui\i18n($model->db, $id_project);
  
  //if the table has no cache it creates cache
  if ( !$translation->cache_has($model->data['id_option'], 'get_translations_table') && !empty($id_project) ){
    //set data in cache $translation->cache_set($id_option, (string)method name, (array)data)
    $translation->cache_set($model->data['id_option'], 'get_translations_table',
      $translation->get_translations_table($id_project, $model->data['id_option'])
    );
  }
  $res = $translation->cache_get($model->data['id_option'], 'get_translations_table');






  //case of project 'options'
  if ( !empty($id_project ) && ( $id_project === 'options') ){
    /**case project options*/
    /** @var  $cfg get the property i18n from option cfg to send it to the find_options*/
    if ( $cfg = $model->inc->options->get_cfg($model->data['id_option']) ){
      if ( !empty($cfg['i18n']) ){
        $model->data['language'] = $cfg['i18n'];

        $res = $model->get_model(APPUI_I18N_ROOT.'options/find_options', [
          'id_option' => $model->data['id_option'],
          'language' => $model->data['language']

        ], true);
      }
    }
  }

  return [
    'id_project' => $id_project ?: $id_project,
    'res' => $res,
    'pageTitle' =>  $model->inc->options->text($model->data['id_option']),
  ];
}
