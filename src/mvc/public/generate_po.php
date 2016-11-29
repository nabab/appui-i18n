<?php
/**
 * Created by PhpStorm.
 * User: BBN
 * Date: 29/11/2016
 * Time: 05:45
 */
$langs = ['en', 'fr', 'it'];
$todo = [];
foreach ( \bbn\file\dir::get_dirs(BBN_LIB_PATH.'bbn') as $d ){
  if ( strpos(basename($d), 'appui-') === 0 ){
    $name = str_replace('-', '_', basename($d));
    foreach ( $langs as $ln ){
      \bbn\file\dir::create_path($d.'/src/locale/'.$ln);
      array_push($todo, 'cd "'.\bbn\str::escape_dquotes($d.'/src/locale/'.$ln).'"');
      if ( is_file($d.'src/locale/'.$ln.'/'.$name.'.po') ){
        array_push($todo, 'find ../../../mvc -iname "*.php" | xargs xgettext -d '.$name.' -j --from-code');
      }
      else{
        array_push($todo, 'find ../../../mvc -iname "*.php" | xargs xgettext -d '.$name.' --from-code');
      }
    }
  }
}
echo '<pre>'.implode(PHP_EOL, $todo).';</pre>';