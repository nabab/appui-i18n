<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 07/03/18
 * Time: 10.27 
 */

if ( $ctrl->arguments[0] ){

  $ctrl->post = [];
  $ctrl->data = ['id_option' => $ctrl->arguments[0]];
  //temporary commented 'cause I'm making changes on model
  $ctrl->cached_action(0);
  //$ctrl->action();
}