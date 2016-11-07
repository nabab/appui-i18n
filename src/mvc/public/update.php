<?php
/** Updates i18n */
$dirs = \bbn\file\dir::get_dirs(BBN_LIB_PATH.'bbn');
$langs = ['fr', 'it', 'es'];
$commands = [];
foreach ( $dirs as $d ){
  if ( is_dir($d.'/src/mvc') ){
    $lib = basename($d);
    \bbn\x::hdump("Adding library $lib");
    foreach ( $langs as $lang ){
      \bbn\file\dir::create_path($d.'/src/locale/$lang/LC_MESSAGES');
      if ( !is_file($d.'/src/locale/$lang/LC_MESSAGES/'.$lib.'.po') ){
        system('find '.BBN_APP_PATH.'mvc -iname "*.php" | xargs xgettext -d '.$lib.' --from-code '.$d.'/src/locale/$lang/LC_MESSAGES', $r);
        var_dump($r);
        $content = file_get_contents($d.'/src/locale/$lang/LC_MESSAGES/'.$lib.'.po');
        $content = str_replace('PACKAGE_VERSION', '1.0', $content);
        $content = str_replace('FULL NAME <EMAIL@ADDRESS>', '1.0', $content);
        $content = str_replace('<LL@li.org>', "<$lang@li.org>", $content);
        $content = str_replace('CHARSET', 'UTF-8', $content);
        $content = str_replace('YEAR-MO-DA HO:MI+ZONE', '1.0', $content);
        file_put_content($d.'/src/locale/$lang/LC_MESSAGES/'.$lib.'.po', $content);
      }
      else{
        system('find '.BBN_APP_PATH.'mvc -iname "*.php" | xargs xgettext -d '.$lib.' -j --from-code '.$d.'/src/locale/'.$lang.'/LC_MESSAGES');
        var_dump($r);
      }
      system("msgfmt -o $d/src/locale/$lang/LC_MESSAGES/$lib.mo $d/src/locale/$lang/LC_MESSAGES/$lib.po", $r);
      var_dump($r);
    }
  }
}
die(implode('<br>', $commands));
