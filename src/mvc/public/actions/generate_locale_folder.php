<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 09/03/18
 * Time: 17.21
 */
if ( isset($ctrl->post['id_option']) ) {
  $ctrl->delete_cached_model($ctrl->plugin_url('appui-i18n') . '/languages_tabs/data/widgets', ['id_option'
  => $ctrl->post['id_option']]);
  $ctrl->action();
}