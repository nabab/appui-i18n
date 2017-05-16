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
  if ( strpos(basename($d), 'bbn-') === 0 ){
    $name = str_replace('-', '_', basename($d));
    foreach ( $langs as $ln ){
      \bbn\file\dir::create_path($d.'/src/locale/'.$ln);
      $st = 'cd "'.\bbn\str::escape_dquotes($d.'/src/locale/'.$ln).'";';
      $st .= 'find ../../../mvc -iname "*.php" | xargs xgettext -d '.$name.' -j --from-code';
      if ( is_file($d.'src/locale/'.$ln.'/'.$name.'.po') ){
        $st .= ' -j';
      }
      $st .= ' --from-code';
      exec($st, $todo[]);
    }
  }
}
$file = $d.'/src/locale/'.$ln.'/'.$name.'.po';
//echo '<pre>'.implode(PHP_EOL, $todo).';</pre>';
//echo exec(implode(';'.PHP_EOL, $todo), $r);

var_dump($todo);