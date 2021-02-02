<?php
/**
 * Created by PhpStorm.
 * User: BBN
 * Date: 29/11/2016
 * Time: 05:45
 */
$langs = ['en', 'fr', 'it'];
$todo = [];
foreach ( \bbn\File\Dir::getDirs(BBN_LIB_PATH.'bbn') as $d ){
  if ( strpos(basename($d), 'appui-') === 0 ){
    $name = str_replace('-', '_', basename($d));
    foreach ( $langs as $ln ){
      \bbn\File\Dir::createPath($d.'/src/locale/'.$ln);
      $st = 'cd "'.\bbn\Str::escapeDquotes($d.'/src/locale/'.$ln).'";';
      $st .= 'find ../../../mvc -iname "*.php" | xargs xgettext -d '.$name;
      if ( is_file($d.'src/locale/'.$ln.'/'.$name.'.po') ){
        $st .= ' -j';
      }
      $st .= ' --from-code';
      $todo[] = $st.PHP_EOL;
      //var_dump(exec($st, $todo[]));
    }
  }
}
//$file = $d.'/src/locale/'.$ln.'/'.$name.'.po';
echo '<pre>'.implode(PHP_EOL, $todo).';</pre>';
//echo exec(implode(';'.PHP_EOL, $todo), $r);

\bbn\X::dump($todo);