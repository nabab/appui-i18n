<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51
 *
 *  @var $ctrl \bbn\mvc\controller
 */

if ( !empty($ctrl->arguments[0]) ){

  if( \bbn\str::is_uid($ctrl->arguments[0]) ){
    $id_option = $ctrl->arguments[0];
  }
  else {
   // die(var_dump($ctrl->arguments[1], $ctrl->arguments[0]));
    $id_option = $ctrl->arguments[1];
    $id_project = $ctrl->arguments[0];
  }
  /** add id_option to data and routes needed to instantiate the class ide */
  $ctrl->add_data([
    'id_project'=> $id_project,
    'id_option' => $id_option,
    'routes' => $ctrl->get_routes()
  ])->combo('$pageTitle', true);
}