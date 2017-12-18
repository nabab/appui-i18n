<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */


if( !empty($ctrl->arguments[0]) ){
  $ctrl->obj->url = 'strings_tab/'.$ctrl->arguments[0];
  //to send $ctrl->arguments[0] to the $model
  $ctrl->combo('Strings to translate in '.$ctrl->arguments[1], $ctrl->get_model('', ['id_option' =>
    $ctrl->arguments[0]]));

}
