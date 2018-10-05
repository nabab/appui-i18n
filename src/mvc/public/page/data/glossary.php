<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 17/01/18
 * Time: 12.42
 */

//$ctrl->post['data']['lang'] is sent by :data of bbn-table

if ( !empty($ctrl->post['limit']) && !empty($ctrl->post['data']['translation_lang']) ){
  $ctrl->action();
}
