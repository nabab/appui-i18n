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
      return{

      }
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
        },

      }];
      bbn.vue.setComponentRule('internationalization/components/', 'appui');
      bbn.vue.addComponent('languages/widget', mixins);

      bbn.vue.addComponent('languages/strings_table', mixins);
      bbn.vue.addComponent('languages/glossaries', mixins);
      bbn.vue.unsetComponentRule();
    },
  };
})();