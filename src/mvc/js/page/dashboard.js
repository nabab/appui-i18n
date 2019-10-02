(() => {

  return {
    props:['source'],
    data(){
      return {
        id_project: null,
        primary: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary

      }
    },
    
    computed: {
      widgets(){
        let res = [],
          buttons;
        if (this.source.data){
          if (this.id_project !== 'options'){
            buttons = [{
              text: bbn._('Update widget data'),
              icon: 'nf nf-fa-retweet',
              action: 'remake_cache'
            },{
              text: bbn._('Setup languages'),
              icon: 'nf nf-fa-flag',
              action: 'generate'
            },{
              text: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }, {
              text: bbn._('Delete locale folder'),
              icon: 'nf nf-fa-trash',
              action: 'delete_locale_folder',
            }]
          }
          else {
            buttons = [{
              text: bbn._('Update widget data'),
              icon: 'nf nf-fa-retweet',
              action: 'remake_cache'
            },{
              text: bbn._('Find new options or translations for this category'),
              icon: 'icon-database',
              action: 'find_options'
            },{
              text: bbn._('Open the table of strings of this path'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }]
          }
          this.source.data.forEach( (v, i) => {
            if ( v.id ){
              res.push({
                title: v.text,
                key: v.id,
                component : 'appui-i18n-widget',
                id_project: this.id_project,
                buttonsRight: buttons
              })
            }
          })
          return res;
        }

      },
      //source to choose the source language using the popup
      /*dd_source_lang(){
        let res = [];
        $.each(this.primary, (i, v) => {
          res.push({
            value: v.lang,
            text: bbn.fn.get_field(this.primary, 'code', v.lang, 'text')
          })
        })
        return res;
      },*/
      //source to choose the translation language using the popup
      dd_translation_lang(){
        let res = [];
        bbn.fn.each(this.primary, (v, i) => {
          res.push({text: v.text, value: v.code })
        })
        return res;
      },

      //the source of projects' dropdown
      dd_projects(){
        let res = [];
        bbn.fn.map(this.source.projects, (v, i) => {
          res.push({value: v.id, text: v.name})
        });
        return res;
      },
    },
    methods: {
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
      get_field: bbn.fn.get_field,
      load_widgets(){
        this.source.data = [];
        if ( this.id_project !== 'options' ){
          this.post('internationalization/page/dashboard', { id_project: this.id_project }, (d) => {
            if ( d.data.success ){
              this.source.data = d.data.data;
              this.source.configured_langs = d.data.configured_langs;
            }
          });
        }
        /** case of project options this controller will return only result of each language and locale dirs */
        else if ( this.id_project === 'options' ){
          this.post('internationalization/options/options_data', { id_project: this.id_project }, (d) => {
            if ( d.success && d.data ){
              this.source.data = d.data.data;
              this.source.configured_langs = d.configured_langs
            }
          })
        }
      }
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
    },
    mounted(){
      this.id_project = this.source.projects[0].id
    }
  }

})();
