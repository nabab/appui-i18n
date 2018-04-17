<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 19/03/18
 * Time: 19.34
 */
if ( $ctrl->post['language'] && $ctrl->post['id_option'] ){
  $ctrl->post['routes'] = $ctrl->get_routes();
  $ctrl->action();
}
