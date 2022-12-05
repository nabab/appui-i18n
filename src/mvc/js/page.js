/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 27/02/17
 * Time: 10.25
 */
/* jshint esversion: 6 */
(() => {
  return {
    name: 'appui-i18n',
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    },
    created(){
      appui.register('appui-i18n', this);
    }
  };
})();