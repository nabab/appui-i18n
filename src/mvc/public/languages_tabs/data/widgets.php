<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 07/03/18
 * Time: 10.27 
 */

if ( $ctrl->arguments[0] ){
  $ctrl->data['id_option'] = $ctrl->arguments[0];
  /*$ctrl->delete_cached_model($ctrl->plugin_url('appui-i18n').'/languages_tabs/data/widgets', ['id_option'
  => $ctrl->data['id_option'] ]);*/


  $ctrl->cached_action(0);
}