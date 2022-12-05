<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 12/12/17
 * Time: 14.20
 */
use \bbn\X;

if ($model->hasData('idProject', true)
  && $model->hasData('langs')
) {
  $idProject = $model->data['idProject'];
  $postLangs = $model->data['langs'] ?: [];
  $i18nCls = new \bbn\Appui\I18n($model->db, $idProject);
  $projectCls = new \bbn\Appui\Project($model->db, $idProject);

  //delete the cache of the option
  $model->inc->options->deleteCache($idProject, true);
  // Get current languages
  $currentLangs = $projectCls->getLangsIds();

  //$idLang the id of the option 'lang'
  if (($idLang = $projectCls->getIdLang())
    //$currentLangs the items of the option lang before the change
    && ($currentLangs !== null)
  ) {
    $primaries = $i18nCls->getPrimariesLangs();
    //the difference among the langs sent from the post and the existing options
    //foreach langs activated using the form ($postLangs) adds the child
    $added = 0;
    $res = [];
    if ($toActivate = array_diff($postLangs, $currentLangs)) {
      foreach ($toActivate as $n) {
        if ($model->inc->options->add([
          'text' => X::getField($primaries, ['id' => $n], 'text'),
          'code' => X::getField($primaries, ['id' => $n], 'code'),
          'id_parent' => $idLang,
          'id_alias' => $n,
        ])) {
          $added++;
        }
      }
    }
    //foreach langs deactivated using the form ($postLangs)
    $removed = 0;
    if ($toDeactivate = array_diff($currentLangs, $postLangs)) {
      foreach ($toDeactivate as $d) {
        //takes the option corresponding to this alias
        if (($options = $model->inc->options->optionsByAlias($d))
          && ($id = X::getField($options, ['id_parent' => $idLang], 'id'))
          //removes the option
          && $model->inc->options->removeFull($id)
         ){
          $removed++;
        };
      }
    }
    if (!empty($removed) || !empty($added)) {
      $model->inc->options->deleteCache($idProject, true);
      return [
        'success' => true,
        'langs' => $projectCls->getLangsIds()
      ];
    }
  }
}
return [
  'success' => false
];