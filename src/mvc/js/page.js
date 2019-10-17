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
        configured_langs: [],
      }
    },
    computed: {
      id_project(){
        if (this.closest('bbn-container') && this.closest('bbn-container').currentURL.length ){
          let bits = this.closest('bbn-container').currentURL.split('/');
          return bits[bits.length - 1]
        }
      
      },
      source_lang(){
        return  bbn.fn.get_field(this.source.projects, 'id', this.id_project, 'lang') || ''
      },
      src(){
        if ( this.source_lang === 'en' ){
          return 'https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg';
        }
        else {
          return 'https://lipis.github.io/flag-icon-css/flags/4x3/' + this.source_lang + '.svg'
        }
      },
      dd_source_lang(){
        let res = [];
        bbn.fn.each(this.primary, (v, i) => {
          res.push({
            value: v.lang,
            text: bbn.fn.get_field(this.primary, 'code', v.lang, 'text')
          })
        })
        return res;
      },
      dd_translation_lang(){
        let res = [];
        bbn.fn.each(this.source.primary, (v, i) => {
          res.push({text: v.text, value: v.code })
        })
        return res;
      },
    },
    methods: {
      get_field: bbn.fn.get_field,
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
              primary: this.source.primary
            },
            row: {
              configured_langs: this.configured_langs,
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
        this.getPopup().open({
          scrollable: false,
          width: 400,
          height: 250,
          source: {
            source_lang: false,
            translation_lang: false,
            primary: this.primary,
            dd_source_lang: this.dd_source_lang,
            dd_translation_lang: this.dd_translation_lang,
          },
          component: this.$options.components.cfg_translations_form,
          title: bbn._('Config your translation tab')
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
      'cfg_translations_form': {
        template:'#cfg_translations_form',
        props:['source'],
        methods: {
          link(){
            bbn.fn.link('internationalization/page/glossary/' + this.source.source_lang + '/' + this.source.translation_lang);
            this.getPopup().close();
          },
          cancel(){
            this.getPopup().close();
          },
        }
      },
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
              bbn.vue.closest(this, 'bbn-container').getComponent().configured_langs.splice(idx, 1);
              //bbn.vue.closest(this, 'bbn-container').getComponent().$forceUpdate();
            }
            else {
              bbn.vue.closest(this, 'bbn-container').getComponent().configured_langs.push(obj.id)
              //bbn.vue.closest(this, 'bbn-container').getComponent().$forceUpdate();
            }
          }
        },
        props: ['source'],
      }
    },
    watch:{
      // when the dashboard of the project is created it takes the array configured_langs to use it in the toolbar
      id_project(val){
        let activeCont = bbn.fn.filter(this.findAll('bbn-container'), (a) => {
          return a.visible
        })
        if ( activeCont.length && activeCont[0].find('appui-i18n-dashboard') ){
          this.configured_langs = activeCont[0].find('appui-i18n-dashboard').source.configured_langs
        }
        else{
          this.configured_langs = [];
        }
      }
    }
  };
})();