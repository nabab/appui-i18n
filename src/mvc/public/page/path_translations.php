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
  /** add id_option to data and routes needed to instantiate the class ide */
  $ctrl->add_data([
    'id_option' => $ctrl->arguments[0],
    'routes' => $ctrl->get_routes()
  ])->combo('$pageTitle', true);
}