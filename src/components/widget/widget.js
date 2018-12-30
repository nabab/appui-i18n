(() => {
  return {
    props: ['source'],
    name: 'widget',
    data(){
      return {
        /** @todo this property should be true after the success of the form in the case of return of d.no_strings = true*/
        no_strings : false,
        id_option: bbn.vue.closest(this, 'bbns-widget').uid,
        primary: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary,
        //source language of the path
        language: null,
        /** the css class for progress bar the value is decided by the watch of progress_bar_val */
        progress_bar_class: '',
      }
    },
    computed: {
      id_project(){
        return this.closest('bbns-tab').getComponent().id_project
      },
      widget_idx(){
        //return the real index of this widget in the array of data of dashboard it works also after drag and drop
        let data = this.parentSource.data;
        return bbn.fn.search(data, 'id', this.id_option);
      },
      //used to render the widget, language of locale folder
      data_widget(){
        //if the source language of the path is set takes the array result from dashboard source
        let result = this.parentSource.data[this.widget_idx].data_widget.result;
        if ( this.language && result){
          for ( let r in result ){
            result[r].class = '';
            result[r].class_db = '';

            if (result[r].num_translations >= 0 ){
              result[r].val = result[r].num_translations/result[r].num * 100
              /** the css class for progress bar */
              if ( ( result[r].val >= 0 ) && (result[r].val <= 30) ){
                result[r].class = 'low'
              }
              else if ( ( result[r].val > 30 ) && ( result[r].val <= 70 ) ){
                result[r].class = 'medium'
              }
              else if ( ( result[r].val > 70 ) && ( result[r].val <= 100 ) ){
                result[r].class = 'high'
              }
            }

          }


          return result;
        }
        else {
          return [];
        }
      },
      locale_dirs(){
        //if the source language of the path is set takes the array result from dashboard source
        let tab = bbn.vue.closest(this,'bbns-tab'),
            cp = tab.getComponent(),
            locale_dirs = cp.source.data[this.widget_idx].data_widget.locale_dirs;

        if ( this.language && locale_dirs.length){
          return locale_dirs;
        }
        else {
          return [];
        }
      },
      configured_langs(){
        if ( this.language ){
          return this.parentSource.configured_langs
        }
      },

      dd_primary(){
        let res = []
        this.primary.forEach( ( v, i) => {
          res.push({value: v.code, text: v.text})
        })
        return res;
      },
      parentSource(){
        return this.closest('bbns-tab').getComponent().source;
      }
    },
    methods: {
      search: bbn.fn.search,
      get_field: bbn.fn.get_field,
      /** set the property language in db for this path */
      set_language(){
        /* the data coming from the post change the source of the dashboard at the index of this specific widget*/
        bbn.fn.post('internationalization/actions/define_path_lang', {
          'language': this.language,
          'id_option': this.id_option,
          'id_project': this.id_project,
        }, (d) => {
          if ( d.success ){
            delete d.success;
            delete d.time;
            this.$nextTick( () => {
              this.parentSource.data[this.widget_idx].data_widget = d.data_widget;
              this.remake_cache();
              appui.success(bbn._('Source language setted'));
              this.$forceUpdate();
            });
          }
        })
      },
      set_cfg(){
        bbn.fn.post('options/set_lang', {
          'id_option': this.id_option,
          'id_project': this.id_project,
          'language': this.language
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language resetted'));
          }
        })
      },
      /** removes the property language of the path */
      remove_language(){
        bbn.fn.post('internationalization/actions/delete_path_lang', {
          'id_option': bbn.vue.closest(this.$parent, 'bbns-tab').getComponent().widgets[bbn.vue.closest(this, 'bbns-widget').index].key,
          'language': this.language
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language resetted'));
          }
        })
      },
      /** removes the property language of the option from its cfg */
      remove_cfg(){
        bbn.fn.post('internationalization/options/remove_lang', {
          id_option: this.id_option
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language resetted for this option'));
          }
        })
      },
      delete_locale_folder(){
        this.confirm('Are you sure you want to delete the folder locale for this path?',()=>{
          bbn.fn.post(this.parentSource.root + 'actions/delete_locale_folder', {
            id_option: this.id_option
          }, (d) => {
            if ( d.success ){
              this.remake_cache()
              appui.success('Folder locale successfully deleted');
            }
            else{
              appui.error('Something went wrong while deleting locale folder')
            }
          });
        })
      },
      open_strings_table(){
        //open the table of strings of this path combining new strings found in the files with strings present in db
        //send arguments[0] (id_option of the path) to 'internationalization/page/path_translations/'
        //only if the the language of the path is set
        //internationalization/page/path_translations/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if ( ( this.configured_langs !== undefined ) && ( this.id_project !== 'options')){
          bbn.fn.link('internationalization/page/path_translations/' + this.id_option);
        }
        else if ( (this.configured_langs === undefined) && ( this.id_project !== 'options')){
          this.alert(bbn._('You have to configure at least a language of translation using the button') +' <i class="fas fa-flag"></i> ' + bbn._('of the widget before to open the strings table') );
        }
        else if ( ( this.configured_langs !== undefined ) && ( this.id_project === 'options') ){
          bbn.fn.link('internationalization/page/path_translations/options/' + this.id_option );
        }
      },
      remake_cache(){
        if ( this.language != null ){
          bbn.fn.post('internationalization/actions/reload_widget_cache', {
            id_option: this.id_option,
            id_project: this.id_project
          }, (d) => {
            if ( d.success ){
              appui.success(bbn._('Widget updated'));
              this.parentSource.data[this.widget_idx].data_widget = d.data_widget;
              this.$forceUpdate();
            }
          })
        }
        else {
          this.alert(bbn._('Select a source language for the path before to update the widget'))
        }
      },
      /*
      find_strings(){
        let url = this.parentSource.root + 'actions/reload_widget_cache';
        bbn.fn.post(url, { id_option: this.source.id_option },  (d) => {
          if ( d.success ){
            this.source.res = d.res;
            let diff = ( d.total - this.source.total );
            if ( diff > 0 ){
              appui.warning(diff + ' ' + bbn._('new string(s) found in') + ' ' + this.source.path);
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + ' ' + bbn._('string(s) deleted from') + ' ' + this.source.path + ' ' + bbn._('files'));
            }
            this.source.total = d.total;
          }
        });

      },*/
      /** method to find strings and translation for the option -works with db- only for the id_project === 'option' */
      find_options(){
        if ( this.id_project === 'options' ){
          let url = this.parentSource.root + 'options/find_options';
          bbn.fn.post(url, {
            id_option : this.id_option,
            language: this.language
          }, (d) => {
            if (d.success){
              if ( d.new > 0 ){
                appui.success(d.new + ' ' +'new options found in this category');
              }
              else {
                appui.warning('No new options found in this category')
              }

            }
          })
        }
      },
      generate(){
        if ( this.language !== null ){
          this.getPopup().open({
            width: 500,
            height: 600,
            title: bbn._("Define languages for the translation"),
            component: this.$options.components['languages-form-locale'],
            source: {
              row: {
                id_option: bbn.vue.closest(this, 'bbns-widget').uid,
                //locale dirs
                languages: this.locale_dirs
              },
              data: {
                id_project: this.id_project,
                primary: this.primary,
                language: this.language,
                widget_idx : this.widget_idx,
                //langs configured for the project
                configured_langs: this.configured_langs,
                /** widget is needed to make operations on the current widget*/
                widget: this,
              }
            }
          })
          
        }
        else {
          this.alert(bbn._('Set a source language using the dropdown before to create translation file(s)'))
        }
      },
    },
    beforeMount(){
      this.language = this.parentSource.data[this.$parent.index].language;
    },
    watch: {
      /** define the css class for the progressbar*/
      locale_dirs(val, oldVal){
        if(val.length){
          this.$forceUpdate();
        }
      }
    },
    components: {
      'languages-form-locale': {
        template: '#languages-form-locale',
        data(){
          return {}
        },
        mounted(){
          //push the source language of the path in the array row.languages to have it as default language
          if ( $.inArray(this.source.data.language, this.source.row.languages) <= -1 ){
            this.source.row.languages.push(this.source.data.language)
          }
        },
        computed: {
          message(){
            return bbn._('If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form') + ' ( <i class="fas fa-cogs"></i> ) ' + bbn._('in the dashboard')
          },
        },
        methods: {
          get_field: bbn.fn.get_field,
          inArray: $.inArray,
          //change the languages of locale dirs
          change_languages(val, obj) {
            let dashboard = bbn.vue.closest(this, 'bbns-tab').getComponent(),
              widgets = bbn.vue.findAll(dashboard, 'bbns-widget'),
              this_widget = dashboard.source.data[this.source.data.widget_idx],
              idx = $.inArray(obj.value, dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs );
            //if I want source language in locale dir as default can create error if the po file doesn't exists//

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
              if ( $.inArray(obj.value, this.source.row.languages ) < 0 ){
                this.source.row.languages.push(obj.value);
              }
              dashboard.source.data[this.source.data.widget_idx].data_widget.result[obj.value] = {
                lang: obj.value,
                num_translations: 0,
                num: 0
              }
            }
          },
          checked_lang(l){
            let code = bbn.fn.get_field(this.source.data.primary, 'id', l, 'code');
            if ( $.inArray(code, this.source.row.languages) > -1 ){
              return true;
            }
            else {
              return false;
            }
          },
          update(){
            this.source.data.widget.remake_cache();
          },
          generate_mo() {
            bbn.fn.log('-------------------------....................')
            let root = this.source.data.widget.closest('bbns-tab').getComponent().source.root, 
                id_option = this.source.data.widget.id_option;
            bbn.fn.post(root + 'actions/generate_mo', {
              id_option: id_option
            }, (d) => {
              if (d.success === true) {
                appui.success('Mo files correctly generated');
              }
            })
          },
          success(d){
            this.send_no_strings = false;
            if ( d.success ){
              var st = '';
              this.source.data.widget.remake_cache();
              if ( d.ex_dir.length ){
                
                d.ex_dir.forEach((v, i) => {
                  //this.source.data.widget.remake_cache();

                  this.$nextTick(() => {
                    appui.success(bbn.fn.get_field(this.source.data.primary, 'code', v, 'text') + ' translation files successfully files deleted')
                  })
                });
                
              }
              if ( d.new_dir.length ){
                
                d.new_dir.forEach((v, i) => {
                //this.source.data.widget.remake_cache();
                
                appui.success(  bbn.fn.get_field(this.source.data.primary, 'code', v, 'text') + ' translation files successfully created')
              } )
              
              
              }
              
              this.generate_mo();
              
              if ( d.done > 0 ){
                appui.success(d.done + ' ' + bbn._('new strings found in this path') )
              }
              if ( bbn.vue.closest(this, 'bbn-tabnav') ){
                let tabs = bbn.vue.findAll(bbn.vue.closest(this, 'bbn-tabnav') , 'bbns-tab');
                bbn.fn.log('tabs', tabs, this.source.row.id_option)
                tabs.forEach((v, i) => {
                  if ( v.url === 'path_translations/'+ this.source.row.id_option ){
                    var tab = v;
                    bbn.fn.log('tab',v)
                    v.reload();
                  }

                });

              }


            }
            else if (d.no_strings === true){
              /** change the property no_strings of the widget to render html */
              this.source.data.widget.no_strings = true
              appui.warning(bbn._("There are no strings in this path"));
            }
          }
        },
        props: ['source', 'data'],
      }
    }
  }
})();
