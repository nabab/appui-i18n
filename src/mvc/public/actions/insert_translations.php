<?php

if ( isset($ctrl->post['id_option']) ){
  $ctrl->post['routes'] = $ctrl->get_routes();
  $ctrl->action();
}