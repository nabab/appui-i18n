<?php
$ctrl->db->insert('bbn_i18n_exp', [
  'lang'=> 'it', 
	'id_exp'=> '11f742bc16f611e89bdf366237393031', 
  'expression'=> 'stiamo facendo un test'
]);
die();
$todo = [];
$dirs = \bbn\file\dir::get_dirs(BBN_APP_PATH.'locale');
echo '<div class="bbn-h-100">';
echo "<h1>Missing translations</h1>";

$default = 'fr';
foreach ( $dirs as $dir ){
  var_dump($dir);
  if ( is_file($dir.'/LC_MESSAGES/appui.app.po') ){
    $lang = basename($dir);
    $translations = new Gettext\Translations();
    $todo[$lang] = [];
    echo "<h2>$lang</h2>";
    Gettext\Extractors\Po::fromFile($dir.'/LC_MESSAGES/appui.app.po', $translations);
    foreach ( $translations->getIterator() as $r => $tr ){
      if ( !isset($shown) ){
        var_dump($tr);
        $shown = 1;
      }
      if ( !$tr->hasTranslation() ){
        if ( $lang === $default ){
          /** @var Gettext\Translation $tr */
          $tr->setTranslation($r);
          //die(var_dump(get_class_methods($translations), $translations->getLanguage(), $tr->getContext()));
        }
        else{
          echo $r.'<br>';
          array_push($todo[$lang], $r);
        }
      }
    }
    if ( $lang === $default ){
      $translations->setHeader("Content-Type", "text/plain; charset=UTF-8");
      Gettext\Generators\Po::toFile($translations, $dir.'/LC_MESSAGES/apst_copy.po');
    }
  }
}
echo '</div>';