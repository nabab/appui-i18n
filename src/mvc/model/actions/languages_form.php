<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 14.20
 */

//this controller is called when the form to config languages for a project is submitted

$model->data['success'] = false;
if( $id_project = $model->data['id'] ){
  $asset_type_lang = $model->inc->options->from_code('lang', 'assets','projects','appui');

//$initial_langs  = languages active for the project before changes
  $initial_langs = $model->db->get_field_values('bbn_projects_assets', 'id_option', [
    'id_project' => $id_project,
    'asset_type' => $asset_type_lang
  ]);


/** case of form from dashboard active langs arriving from the post*/
if ( !empty($model->data['configured_langs']) ){
  $post_langs = $model->data['configured_langs'];
}
/** case of form from projects_table active langs arriving from the post*/
else if ( !empty($model->data['langs']) ){
  $post_langs = $model->data['langs'];
}


//extrapolate the difference among the langs sent from the post and the original active langs for the project
  $new_active_langs = array_diff($post_langs, $initial_langs);

//foreach langs activated using the form ($post_langs)

  if ( !empty($new_active_langs ) ){
    foreach( $new_active_langs as $n ) {

      if($model->db->insert('bbn_projects_assets',
        [
          'id_option' => $n,
          'id_project' => $id_project,
          'asset_type' => $asset_type_lang
        ])
      ){
        $model->data['success'] = true;
      }
    }
  }



//foreach langs deactivated using the form ($post_langs)
  $langs_deactivated = array_diff($initial_langs, $post_langs);
   if( !empty($langs_deactivated) ){
    foreach( $langs_deactivated as $d ){
      if(
      $model->db->delete('bbn_projects_assets',
        [
          'id_option' => $d,
          'id_project' => $id_project,
          'asset_type' => $asset_type_lang
        ])
      ){
        $model->data['success'] = true;
      }


    }
  }

}
return['success' => $model->data['success']];