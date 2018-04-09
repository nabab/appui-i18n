<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 13/03/18
 * Time: 11.45
 */
//VEDERE CON QUALCUNO PERCHE NON HO CAPITO COME FARE
if ( $ctrl->post['id_option'] ){

  $ctrl->delete_cached_model($ctrl->plugin_url('appui-i18n').'/page/data/widgets', ['id_option'
  => $ctrl->post['id_option'] ]);

 // $ctrl->cached_action(0);
  return $ctrl->data['success'] = true;
}