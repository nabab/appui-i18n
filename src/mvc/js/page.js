/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 27/02/17
 * Time: 10.25
 */
/* jshint esversion: 6 */
(()=>{
  let languages;
  return {
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    },

    created(){
      languages = this;
      let mixins = [{
        props: {
          languages: {
            type: Object,
            default(){
              return languages;
            }
          }
        }
      }];
    },
  };
})();