(() => {
  return {
    props: ['source'],
    name: 'widget',
    data(){
      return {
        id_option: bbn.vue.closest(this, 'bbn-widget').uid,
        //source language of the path
        'language': bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.$parent.index].language ? bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.$parent.index].language : null,
      }
    },
    mounted(){
      if ( this.source.new > 0 ){
        appui.warning(
          this.source.new + ' new strings found in ' + this.source.path
        );
      }
    },
    methods: {
      search: bbn.fn.search,
      get_field: bbn.fn.get_field,
      set_language(){
        /* the data coming from the post change the source of the dashboard at the index of this specific */
        bbn.fn.post('internationalization/actions/define_path_lang', {
          'language': this.language,
          'id_option': bbn.vue.closest(this.$parent, 'bbn-tab').getComponent().widgets[bbn.vue.closest(this, 'bbn-widget').index].key
        }, (d) => {
          if ( d.success ){
            delete d.success;
            delete d.time;
            bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget = d.data_widget ;
            this.$set(this.data_widget, d);
            appui.confirm('Source language updated');
            this.$forceUpdate();

          }
        })
      },
      remove_language(){
        //removes the property language of the path
        bbn.fn.post('internationalization/actions/delete_path_lang', {
          'id_option': bbn.vue.closest(this.$parent, 'bbn-tab').getComponent().widgets[bbn.vue.closest(this, 'bbn-widget').index].key,
          'language': this.language
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.confirm('Source language resetted');
          }
        })
      },
      open_strings_table(){
        //open the table of strings of this path combining new strings found in the files with strings present in db
        //send arguments[0] (id_option of the path) to 'internationalization/languages_tabs/path_translations/'
        //only if the the language of the path is set
        //internationalization/languages_tabs/path_translations/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if ( this.data_widget ){
          bbn.fn.link('internationalization/languages_tabs/path_translations/' + this.id_option);
        }
        else{
          appui.error('There are no strings in this path')
        }
      },
      remake_cache(){
        bbn.fn.post('internationalization/actions/reload_widget_cache', { id_option: this.id_option }, (d) => {
          if ( d.success ){
            appui.confirm('Widget updated');
            this.$set(this.locale_dirs,  d.data_widget.locale_dirs);

            this.$forceUpdate();

          }
        })
      },

      find_strings(){
        let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'actions/reload_widget_cache';
        bbn.fn.post(url, { id_option: this.source.id_option },  (d) => {
          if ( d.success ){
            this.source.res = d.res;
            let diff = ( d.total - this.source.total );
            if ( diff > 0 ){
              appui.warning(diff + ' new string(s) found in ' + this.source.path);
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.path + ' files');
            }
            this.source.total = d.total;
          }
        });

      },
      config_locale_dir(){
        bbn.vue.closest(this, 'bbn-tab').popup().open({
          width: 500,
          height: 600,
          title: bbn._("Define languages for the translation"),
          component: this.$options.components['appui-languages-form-locale'],
          source: {
            row: {
              id_option: bbn.vue.closest(this, 'bbn-widget').uid,
              //locale dirs
              languages: this.locale_dirs
            },
            data: {
              widget_idx : this.widget_idx,
              //langs configured for the project
              configured_langs: this.configured_langs,
            }
          }
        })
      },
    },
    computed: {
      widget_idx(){
        return bbn.vue.closest(this, 'bbn-widget').index
      },
      //used to render the widget, language of locale folder
      data_widget(){
        //if the source language of the path is set takes the array result from dashboard source
        let result = bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget.result;
        if ( this.language && result){
          return Object.values(result);
        }
        else {
          return [];
        }
      },
      locale_dirs(){
        //if the source language of the path is set takes the array result from dashboard source
        let locale_dirs = bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget.locale_dirs;
        if ( this.language && locale_dirs.length){
          return locale_dirs;
        }
        else {
          return [];
        }
      },
      /*locale_langs(){
        let res = [];
        if ( this.data_widget.length ){
          this.data_widget.forEach( (v, i) => {
            res.push(v.lang);
          });
          return res;
        }
        return res;
      },*/
      configured_langs(){
        return bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs
      },
      primary(){
        return bbn.vue.closest(this, 'bbn-tab').getComponent().source.primary;
      },
      dd_primary(){
        let res = []
        this.primary.forEach( ( v, i) => {
          res.push({value: v.code, text: v.text})
        })
        return res;
      },

    },
    components: {
      'appui-languages-form-locale': {
        template: '#appui-languages-form-locale',
        computed: {
          message(){
            return 'If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form ( <i class="fa fa-cogs"></i> ) in the dashboard'
          },
          primary(){
            return bbn.vue.closest(this, 'bbn-tab').getComponent().source.primary;
          },

        },
        methods: {
          get_field: bbn.fn.get_field,
          inArray: $.inArray,

          //change the languages of locale dirs
          change_languages(val, obj) {
            let dashboard = bbn.vue.closest(this, 'bbn-tab').getComponent(),
                widgets = bbn.vue.findAll(dashboard, 'bbn-widget'),
                this_widget = widgets[this.source.data.widget_idx].$children,
                idx = $.inArray(obj.value, this_widget[0].locale_dirs );
            if (idx > -1) {
              this_widget[0].locale_dirs.splice(idx, 1)
            }
            else {
              this_widget[0].locale_dirs.push(val);
              }

          },
          checked_lang(l){
            let code = bbn.fn.get_field(this.primary, 'id', l, 'code');
            if ( $.inArray(code, this.source.row.languages) > -1 ){
              return true;
            }
            else {
              return false;
            }
          },
          

        },
        props: ['source'],
      }
    }
  }
})();