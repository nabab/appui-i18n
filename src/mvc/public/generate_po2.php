<?php
/**
 * Created by PhpStorm.
 * User: BBN
 * Date: 29/11/2016
 * Time: 05:45
 */
echo '<div class="bbn-h-100">';
$langs = ['en', 'fr', 'it'];
$todo = [];
foreach ( \bbn\file\dir::get_dirs(BBN_LIB_PATH.'bbn') as $d ){
  if ( strpos(basename($d), 'appui-') === 0 ){
    $name = str_replace('-', '_', basename($d));
    foreach ( $langs as $ln ){
      $dir = $d.'/locale/'.$ln.'/LC_MESSAGES';
      \bbn\file\dir::create_path($dir);
      chdir($dir);
      $files = \bbn\file\dir::scan('../../../mvc', 'php');
      $translations = new \Gettext\Translations();
      foreach ( $files as $f ){
        if ( $tmp = \Gettext\Translations::fromPhpCodeFile($f, ['functions' => ['_' => 'gettext']]) ){
          $translations->mergeWith($tmp);
        }
      }
      //var_dump(get_class_methods(\get_class($translations)), $translations->count());

      foreach ( $translations->getIterator() as $r => $tr ){
        //die(var_dump(get_class_methods($tr)));
        $todo[] = $tr->getOriginal();
      }
    }
  }
}
//$file = $d.'/src/locale/'.$ln.'/'.$name.'.po';
//echo '<pre>'.implode(PHP_EOL, $todo).';</pre>';
//echo exec(implode(';'.PHP_EOL, $todo), $r);

\bbn\x::hdump($todo);
echo '</div>';