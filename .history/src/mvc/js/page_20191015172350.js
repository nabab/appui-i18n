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
    computed: {
      id_project(){
        if (this.closest('bbn-container') && this.closest('bbn-container').currentURL.length ){
          let bits = this.closest('bbn-container').currentURL.split('/');
          return bits[bits.length - 1]
        }
      
      },
      src(){
        let lang = bbn.fn.get_field(this.source.projects, 'id', this.id_project, 'lang');
        if ( lang === 'en' ){
          return 'https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg';
        }
        else {
          return 'https://lipis.github.io/flag-icon-css/flags/4x3/' + lang + '.svg'
        }
      }
    },
    methods: {
      makeSrc(code){
        return  'https://lipis.github.io/flag-icon-css/flags/4x3/' + ( code !== 'en' ? code : 'gb') + '.svg'
      },
      cfg_project_languages(){
        this.getPopup().open({
          width: 600,
          height: 500,
          title: bbn._("Config translation languages for the project"),
          component: this.$options.components['languages-form'],
          //send the configured langs for this id_project
          source: {
            data: {
              primary: this.primary
            },
            row: {
              configured_langs: this.source.configured_langs,
              id: this.id_project
            }
          }
        })
      },
      open_users_activity(){
        bbn.fn.link('internationalization/page/history/');
      },
      open_user_activity(){
        bbn.fn.link('internationalization/page/user_history');
      },
      open_glossary_table(){
        //open a component popup to select source language and translation language for the table glossary
          var tab = bbn.vue.closest(this, 'bbn-container').getComponent();
          this.getPopup().open({
            scrollable: false,
            width: 400,
            height: 250,
            source: {
              source_lang: false,
              translation_lang: false,
              primary: tab.primary,
              dd_source_lang: tab.dd_source_lang,
              dd_translation_lang: tab.dd_translation_lang,
            },
            component: tab.$options.components.cfg_translations_form,
            title: 'Config your translation tab'
          })

        },
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
    },
    components: {
      'languages-form': {
        template: '#languages-form',
        methods:{
          inArray(l, arr){
            if ( bbn.fn.isArray(arr) ){
              return arr.indexOf(l)
            }
          },
          change_checked_langs(val, obj){
            let form = bbn.vue.find(this, 'bbn-form'),
              //idx =  $.inArray(obj.id, this.source.row.configured_langs);
              idx = this.source.row.configured_langs.indexOf(obj.id);

            if ( idx > -1 ){
              bbn.vue.closest(this, 'bbn-container').getComponent().source.configured_langs.splice(idx, 1);
              bbn.vue.closest(this, 'bbn-container').getComponent().$forceUpdate();
            }
            else {
              bbn.vue.closest(this, 'bbn-container').getComponent().source.configured_langs.push(obj.id)
              bbn.vue.closest(this, 'bbn-container').getComponent().$forceUpdate();
            }
          }
        },
        props: ['source'],
      }
    }
  };
})();