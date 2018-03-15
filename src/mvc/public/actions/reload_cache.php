<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 21/02/18
 * Time: 10.53
 */

//post from the toolbar's button reload_cache of strings table
$id_option = $ctrl->post['id_option'];

if ( !empty($id_option) ){

  //delete the model from the cache
  $ctrl->delete_cached_model($ctrl->plugin_url('appui-i18n').'/languages_tabs/data/widgets', ['id_option'
  => $id_option ]);

  //create a new model in the cache
  $ctrl->set_cached_model($ctrl->plugin_url('appui-i18n').'/languages_tabs/data/widgets', ['id_option'
  => $id_option]);

  //get the model from the cache
  $cached_model  = $ctrl->get_cached_model('/languages_tabs/data/widgets', $ctrl->post);

  $ctrl->data['cached_model'] = $cached_model;


 //$ctrl->cached_action(0);
  $ctrl->action();
}
