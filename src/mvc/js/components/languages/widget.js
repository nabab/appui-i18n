(() => {
  var current_widget;
  return {
    created(){
      current_widget = this;
    },
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
          this.source.new + bbn._(' new strings found in ') + this.source.path
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
          'id_option': bbn.vue.closest(bbn.vue.closest(this, 'bbn-widget'), 'bbn-tab').getComponent().widgets[this.widget_idx].key
        }, (d) => {
          if ( d.success ){
            delete d.success;
            delete d.time;
            this.$nextTick( () => {
              bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget = d.data_widget;

              appui.success(bbn._('Source language updated'));
              this.$forceUpdate();
            });


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
            appui.success(bbn._('Source language resetted'));
          }
        })
      },
      open_strings_table(){
        //open the table of strings of this path combining new strings found in the files with strings present in db
        //send arguments[0] (id_option of the path) to 'internationalization/page/path_translations/'
        //only if the the language of the path is set
        //internationalization/page/path_translations/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if ( this.configured_langs !== undefined ){
          bbn.fn.link('internationalization/page/path_translations/' + this.id_option);
        }
        else {
          bbn.vue.closest(this, 'bbn-tab').popup().alert(bbn._('You have to configure at least a language of translation using the button') +' <i class="fa fa-flag"></i> ' + bbn._('of the widget before to open the strings table') );

        }
      },
      remake_cache(){
        if ( this.language != null ){
          bbn.fn.post('internationalization/actions/reload_widget_cache', { id_option: this.id_option }, (d) => {
            if ( d.success ){
              appui.success(bbn._('Widget updated'));
              bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget = d.data_widget;
              this.$forceUpdate();

            }
          })
        }
        else {
          bbn.vue.closest(this, 'bbn-tab').popup().alert(bbn._('Select a source language for the path before to update the widget'))
        }
      },

      find_strings(){
        let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'actions/reload_widget_cache';
        bbn.fn.post(url, { id_option: this.source.id_option },  (d) => {
          if ( d.success ){
            this.source.res = d.res;
            let diff = ( d.total - this.source.total );
            if ( diff > 0 ){
              appui.warning(diff + bbn._(' new string(s) found in ') + this.source.path);
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + bbn._(' string(s) deleted from ') + this.source.path + bbn._(' files'));
            }
            this.source.total = d.total;
          }
        });

      },
      config_locale_dir(){
        if ( this.language !== null ){
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
                language: this.language,
                widget_idx : this.widget_idx,
                //langs configured for the project
                configured_langs: this.configured_langs,
              }
            }
          })
        }
        else {
          bbn.vue.closest(this, 'bbn-tab').popup().alert(bbn._('Set a source language using the dropdown before to create translation file(s)'))

        }
      },
    },
    computed: {
      //no strings in the path
      no_strings(){
        if ( this.data_widget && this.data_widget[this.language] ){
          if (this.data_widget[this.language].num > 0 ){
            return false;
          }
          else {
            return true;
          }
        }
      },
      widget_idx(){
        //return the real index of this widget in the array of data of dashboard it works also after drag and drop
        let data = bbn.vue.closest(this, 'bbn-tab').getComponent().source.data;
        return bbn.fn.search(data, 'id', this.id_option);
      },
      //used to render the widget, language of locale folder
      data_widget(){
        //if the source language of the path is set takes the array result from dashboard source
        let result = bbn.vue.closest(this, 'bbn-tab').getComponent().source.data[this.widget_idx].data_widget.result;
        if ( this.language && result){
          return result;
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
      configured_langs(){
        if ( this.language ){
          return bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs
        }
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
    watch: {
      locale_dirs(val, oldVal){
        if(val){
          this.$forceUpdate();
        }
      }
    },
    components: {
      'appui-languages-form-locale': {
        template: '#appui-languages-form-locale',
        mounted(){
          //push the source language of the path in the array row.languages to have it as default language
          if ( $.inArray(this.source.data.language, this.source.row.languages) <= -1 ){
            this.source.row.languages.push(this.source.data.language)
          }
        },
        computed: {
          widget(){
            return current_widget;
          },
          message(){
            return bbn._( 'If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form ( <i class="fa fa-cogs"></i> ) in the dashboard')
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
                this_widget = dashboard.source.data[this.source.data.widget_idx],
                idx = $.inArray(obj.value, dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs );
            //if I want source language in locale dir as default can create error if the po file doesn't exists//
            /*if ( $.inArray(this.source.data.language, dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs ) < 0 ){
              dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs.push(this.source.data.language);
            }*/
            //case uncheck language
            if (idx > -1) {
              if (this_widget.data_widget.locale_dirs.length) {
                if ( this_widget.data_widget.result[obj.value] !== undefined){
                  this_widget.data_widget.locale_dirs.splice(idx, 1);
                  delete this_widget.data_widget.result[obj.value];
                }
              }
            }
            //case check language
            else {
              dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs.push(obj.value);
              dashboard.source.data[this.source.data.widget_idx].data_widget.result[obj.value] = {
                lang: obj.value,
                num_translations: 0,
                num: 0
              }
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
          success(d){
            if ( d.success ){
              var st = '';
              if ( d.ex_dir.length ){
                bbn.fn.log('ex', d.ex_dir, typeof(d.ex_dir))
                d.ex_dir.forEach( (v, i) => {
                  this.widget.remake_cache();
                  appui.success( bbn.fn.get_field(this.primary, 'code', v, 'text') + ' translation files successfully files deleted')
                });

              }
              if ( d.new_dir.length){
                d.new_dir.forEach((v, i) => {
                  //this.widget.remake_cache();
                  appui.success(  bbn.fn.get_field(this.primary, 'code', v, 'text') + ' translation files successfully created')
                } )
              }
            }
            else if (d.no_strings === true){
              appui.warning(bbn._("There are no strings in this path"))
            }
          }

        },
        props: ['source'],
      }
    }
  }
})();