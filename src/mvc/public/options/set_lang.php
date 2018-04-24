<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 24/04/18
 * Time: 12.51
 */
if ( !empty($ctrl->post['id_option']) && !empty($ctrl->post['language']) ){
  $ctrl->action();
}