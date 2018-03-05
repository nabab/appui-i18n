(() => {
  return {
    props: ['source'],
    data(){
      return {
        //primary is used to render the name of languages in the table
        primary : bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary,
      }
    },
    computed: {
      //source to choose the source language using the popup
      dd_source_lang(){
        let res = [];
        $.each(this.source.source_langs, (i, v) => {
          res.push({
            value: v.lang,
            text: bbn.fn.get_field(this.primary, 'code', v.lang, 'text')
          })
        })
        return res;
      },
      //source to choose the translation language using the popup
      dd_translation_lang(){
        let res = [];
        $.each(this.primary, (i, v) => {
          res.push({text: v.text, value: v.code })
        })
        return res;
      }
    },
    methods: {
      insert_translation(row,idx){
        bbn.fn.post('internationalization/actions/insert_translation', row, (success) => {
          if (success){
            appui.success('Translation saved');
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      render_status(row){
        let st = '';
        if ( ( row.original_lang === row.translation_lang ) && ( row.expression === row.original_expression ) ){
          st += '<i class="fa fa-check bbn-bg-purple bbn-xl" style="color:white" title="Expressions are identical"></i>'
        }
        else if ( ( row.original_lang === row.translation_lang ) && ( row.expression !== row.original_expression ) ){
          st += '<i class="zmdi zmdi-alert-triangle bbn-xl bbn-bg-orange" title="Expression changed in its' +
            ' original language">' +
            ' </i>'
        }
        else if ( ( row.original_lang !== row.translation_lang ) && ( row.expression !== row.original_expression ) ){
          st += '<i class="fa fa-smile-o bbn-bg-green bbn-xl" title="Expression translated"></i>'
        }
        else if ( ( row.original_lang !== row.translation_lang ) && ( row.expression === row.original_expression ) ){
          st += '<i title="Translated! Expression is the same in the two languages" class="fa fa-smile-o bbn-green bbn-xl"></i>'
        }
        return st;
      },
      render_lang(row){
        let st = '';
        if ( row.translation_lang === 'fr' ){
          st += '<img class="flag" style="width:25px;height:15px" src="https://lipis.github.io/flag-icon-css/flags/4x3/fr.svg" alt="France Flag">'
        }
        else if ( row.translation_lang === 'en' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'de' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/de.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'es' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/es.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'it' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/it.svg" alt="United Kingdom Flag">'
        }
        return st;
        /*let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.translation_lang , 'text')
        return st;*/
      },
      render_original_lang(row){
        let st = '';
        if ( row.original_lang === 'fr' ){
          st += '<img class="flag" style="width:25px;height:15px" src="https://lipis.github.io/flag-icon-css/flags/4x3/fr.svg" alt="France Flag">'
        }
        else if ( row.original_lang === 'en' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg" alt="United Kingdom Flag">'
        }
        return st;
        /*let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.original_lang , 'text')
        return st;*/
      },
    },

    components : {
      //the toolbar of the table
      'toolbar': {
        template:'#toolbar',
        props:['source'],
        data(){
          return{
            langs: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.langs_in_db,
            primary: bbn.vue.closest(this, 'bbn-tab').getComponent().primary
          }
        },
        methods: {
          config_translations(){
            //open a component popup to select source language and translation language for the table glossary
            var tab = bbn.vue.closest(this, 'bbn-tab').getComponent();
            bbn.vue.closest(this, 'bbn-tab').popup().open({
              width:350,
              height:250,
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
          open_user_history(){
            bbn.fn.link('internationalization/languages_tabs/user_history');
          },
          open_statistic_list(){
            bbn.fn.link('internationalization/languages_tabs/statistic');
          },
        },
      },
      //popup to choise the languages for glossary table, IS NOT A FORM
      'cfg_translations_form': {
        template:'#cfg_translations_form',
        props:['source'],
        methods: {
          link(){
            bbn.fn.link('internationalization/languages_tabs/glossary/' + this.source.source_lang + '/' + this.source.translation_lang);
            bbn.vue.closest(this, 'bbn-popup').close();
          },
          cancel(){
            bbn.vue.closest(this, 'bbn-popup').close();
          },
        }
      	
        }
        }



      
    
  }
})();