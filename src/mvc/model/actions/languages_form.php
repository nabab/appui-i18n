<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 14.20
 */

//this model is called when the form to config languages for a project is submitted

$model->data['success'] = false;

if ( $id_project = $model->data['id'] ){
  $translations = new \bbn\appui\i18n($model->db, $id_project);
  $project = new \bbn\appui\project($model->db, $id_project);
  
  //delete the cache of the option
  $model->inc->options->delete_cache($id_project, true);
  
  $initial_langs = $project->get_langs_ids();
  
  //$id_langs the id of the option 'lang' 
  //$initial_langs the items of the option lang before the change 
  
  if ( ($id_langs = $project->get_id_lang()) && ($project->get_langs_ids() !== null) ){
    $initial_langs = $project->get_langs_ids();
    // case of form from dashboard active langs arriving from the post
    if ( !empty($model->data['configured_langs']) ){
      $post_langs = $model->data['configured_langs'];
    }
    $primaries = $translations->get_primaries_langs();
    //the difference among the langs sent from the post and the existing options
    //foreach langs activated using the form ($post_langs) adds the child
    $new = 0;
    $res = [];
    if ( !empty( $new_active_langs = array_diff($post_langs, $initial_langs) ) ){
      foreach( $new_active_langs as $n ) {
        if ( $new = $model->inc->options->add([
          'text' => \bbn\x::get_field($primaries, ['id' => $n], 'text'),
          'code' => \bbn\x::get_field($primaries, ['id' => $n], 'code'),
          'id_parent' => $project->get_id_lang(),
          'id_alias' => $n, 
        ]) ){
          $new ++;
        }
      }
    }
    //foreach langs deactivated using the form ($post_langs)
    $removed = 0;
    if( ($langs_deactivated = array_diff($initial_langs, $post_langs)) ){
      foreach ( $langs_deactivated as $d ){
        //takes the option corresponding to this alias
        if (
           ($options = $model->inc->options->options_by_alias($d)) &&
           ($id = \bbn\x::get_field($options, ['id_parent' => $id_langs], 'id'))
         ){
          //removes the option
          if ( $model->inc->options->remove_full($id) ){
            $removed ++;
          }
        };
        
      }
    }
    if ( !empty($removed) || !empty($new) ){
      $model->inc->options->delete_cache($id_project, true);
      $model->data['success'] = true;
    }
    //returns the options of langs for the project 
    $configured_langs = $project->get_langs_ids();
  }
}
return[
  'configured_langs' => $configured_langs,
  'success' => $model->data['success']
];