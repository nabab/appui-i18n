<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 28/11/17
 * Time: 12.51 */

if ( !empty($id_option = $ctrl->arguments[0]) ){
  //add id_option to data
  $ctrl->add_data(['id_option' => $id_option])->combo('$pageTitle', true);
}