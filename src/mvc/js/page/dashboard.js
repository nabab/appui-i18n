(() => {

  return {
    props:['source'],
    data(){
      return {
        id_project: null,
        project_name: '',
        primary: this.closest('bbn-router').$parent.source.primary,
        changingProjectLang: false,
        language: ''
      }
    },
    
    computed: {
      languageText(){
        return bbn.fn.getField(this.source.primary, 'text', 'code', this.language);
      },
      dd_primary(){
        let res = []
        this.primary.forEach( (v, i) => {
          res.push({value: v.code, text: v.text})
        })
        return res;
      },
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
                title: v.title,
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
            text: bbn.fn.getField(this.primary, 'text', 'code', v.lang)
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
      set_project_language(){
        this.post('internationalization/actions/project_lang', {
          id_project: this.id_project,
          language: this.language
        }, (d) => {
          if ( d.success ){
            let idx = bbn.fn.search(this.source.projects, 'id', this.id_project);
            if ( idx > -1 ){
              this.source.projects[idx].lang = this.language;
            }
            this.changingProjectLang = false
            appui.success(bbn._('Project source language successfully updated'))
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
            title: bbn._('Config your translation tab')
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
              primary: this.primary,
              language: this.language
            },
            row: {
              configured_langs: this.source.configured_langs,
              id: this.id_project
            }
          }
        })
      },
      getField: bbn.fn.getField,
      load_widgets(){
        this.source.data = [];
        if ( this.id_project !== 'options' ){
          this.post('internationalization/page/dashboard', { id_project: this.id_project }, (d) => {
            if ( d.data.success ){
              this.source.data = d.data.data;
              this.source.configured_langs = d.data.configured_langs;
              this.project_name = bbn.fn.getField(this.source.projects, 'name', 'id', this.id_project)
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
    watch : { 
      id_project(val){
        if (val){
          this.language = bbn.fn.getField(this.source.projects, 'lang', 'id', val)
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
          },
          cancel(){
            this.getPopup().close();
          },
        }
      },
      'languages-form': {
        template: '#languages-form',
        mounted(){
          bbn.fn.each(this.source.data.primary, (v, i)=>{
            bbn.fn.log(v.id, this.source.data.language, (v.id === this.source.data.language))
          })
          
        },
        methods:{
          success(d){
            if ( d.success ){
              appui.success(bbn._('Languages successfully updated'))
            }
          },
          inArray(l, arr){
            if ( bbn.fn.isArray(arr) ){
              return arr.indexOf(l)
            }
          },
          change_checked_langs(val, obj){
            let idx = this.source.row.configured_langs.indexOf(obj.id);
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
      this.id_project = this.source.projects[0].id;
      this.project_name = this.source.projects[0].name;
      this.language =  bbn.fn.get_field(this.source.projects,'id', this.id_project,'lang');
    }
  }

})();