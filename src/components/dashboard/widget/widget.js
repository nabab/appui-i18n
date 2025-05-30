(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
      }
    },
    data(){
      return {
        /** @todo this property should be true after the success of the form in the case of return of d.no_strings = true*/
        noStrings : false
      }
    },
    computed: {
      widgetIndex(){
        if (this.dashboard?.data) {
          const idx = bbn.fn.search(this.dashboard.data, 'id', this.source.id);
          return idx > -1 ? idx : null;
        }

        return null;
      },
      //used to render the widget, language of locale folder
      data(){
        if (this.source?.language && this.source?.data_widget?.result) {
          let result = bbn.fn.clone(this.source.data_widget.result, true);
          bbn.fn.iterate(result, (r, l) => {
            r.class = '';
            r.class_db = '';
            if (r.num_translations >= 0) {
              r.val = !r.num ? 0 : r.num_translations/r.num * 100
              /** the css class for progress bar */
              if ((r.val >= 0) && (r.val <= 30)) {
                r.class = 'low'
              }
              else if ((r.val > 30) && (r.val <= 70)) {
                r.class = 'medium'
              }
              else if ((r.val > 70) && (r.val <= 100)) {
                r.class = 'high'
              }
            }

          });

          return result;
        }

        return [];
      },
      //if the source language of the path is set takes the array result from dashboard source
      localeDirs(){
        return this.source?.data_widget?.locale_dirs;
      },
      ddPrimary(){
        return bbn.fn.map(this.primary || [], p => {
          return {
            value: p.code,
            text: p.text
          };
        });
      },

    },
    methods: {
      search: bbn.fn.search,
      normalize(val){
        return val && (val > 0) ? parseFloat(val.toFixed(2)) : 0;
      },
      setCfg(){
        this.post(this.root + 'options/set_lang', {
          id_option: this.source.id,
          language: this.source.language,
          id_project: this.idProject
        }, d => {
          if (d.success) {
            this.source.language = null
            appui.success(bbn._('Source language reset'));
          }
        })
      },
      /** removes the property language of the path */
      removeLanguage(){
        this.confirm(bbn._('Are you sure you want to remove the source language for this path?'), () => {
          this.post(this.root + '/actions/delete_path_lang', {
            id_option: this.source.id,
            language: this.source.language,
            id_project: this.idProject
          }, d => {
            if (d.success) {
              this.source.language = null
              appui.success(bbn._('Source language reset'));
            }
          })
        });
      },
      deleteLocaleFolder(){
        this.confirm(bbn._('Are you sure you want to delete the folder locale for this path?'), () => {
          this.post(this.root + 'actions/delete_locale_folder', {
            id_option: this.source.id,
            id_project: this.idProject
          }, d => {
            if (d.success) {
              this.remakeCache()
              appui.success(bbn._('Folder locale successfully deleted'));
            }
            else{
              appui.error(bbn._('Something went wrong while deleting locale folder'));
            }
          });
        })
      },
      openStringsTable(){
        //open the table of strings of this path combining new strings found in the files with strings present in db
        //send arguments[0] (id_option of the path) to 'page/expressions/'
        //only if the the language of the path is set
        //page/expressions/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if (this.configuredLangs?.length) {
          bbn.fn.link(this.baseURL + 'expressions/' + this.projectCode + '/' + this.source.id);
        }
        else if (!this.isOptionsProject) {
          this.alert(bbn._('You have to configure at least a language of translation using the button %s of the widget before to open the strings table', '<i class="nf nf-fa-flag"></i>'));
        }
      },
      openTranslate(){
        if (this.configuredLangs) {
          bbn.fn.link(this.baseURL + 'translate/' + this.projectCode + '/' + this.source.id);
        }
        else if (!this.isOptionsProject) {
          this.alert(bbn._('You have to configure at least a language of translation using the button %s of the widget before to open the strings table', '<i class="nf nf-fa-flag"></i>'));
        }
      },
      remakeCache(){
        if (this.dashboard?.data && (this.source.language != null)) {
          this.post(this.root + 'actions/reload_widget_cache', {
            id_option: this.source.id,
            id_project: this.idProject
          }, d => {
            if (d.success) {
              appui.success(bbn._('Widget updated'));
              this.$set(this.source, 'data_widget', d.data);
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
        let url = this.root + 'actions/reload_widget_cache';
        this.post(url, { id_option: this.source.id },  (d) => {
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
      findOptions(){
        if (this.isOptionsProject) {
          let url = this.root + 'options/find_options';
          this.post(url, {
            id_option : this.source.id,
            language: this.source.language
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
        if ( this.source.language !== null ){
          this.getPopup({
            width: 500,
            label: bbn._("Define languages for the translation"),
            scrollable: true,
            component: this.$options.components.languagesForm,
            source: {
              row: {
                id_option: this.source.id,
                languages: this.localeDirs
              },
              data: {
                id_project: this.idProject,
                primary: this.primary,
                language: this.source.language,
                widget_idx : this.widgetIndex,
                //langs configured for the project
                configured_langs: this.configuredLangs,
                /** widget is needed to make operations on the current widget*/
                //widget: this,
              }
            }
          })

        }
        else {
          this.alert(bbn._('Set a source language using the dropdown before to create translation file(s)'))
        }
      },
      generateFiles(){
        this.post(this.root + 'actions/generate', {
          id_option: this.source.id,
          languages: this.localeDirs,
          id_project: this.idProject,
          language: this.source.language
        }, d => {
          if (d.success && !!d.widget) {
            this.$set(this.source, 'data_widget', d.widget);
          }
          else {
            appui.error();
          }
        });
      }
    },
    watch: {
      /** define the css class for the progressbar*/
      /*locale_dirs(val, oldVal){
        if ( val.length ){
          this.$forceUpdate();
        }
      }*/
    },
    components: {
      languagesForm: {
        template: `
          <bbn-form :source="source.row"
                    :data="{
                      id_project: source.data.id_project,
                      language: source.data.language
                    }"
                    ref="form-locale"
                    :action="root + (source.data.id_project === 'options' ? 'options/find_options' : 'actions/generate')"
                    confirm-leave="`+ bbn._('Are you sure you want to exit without saving changes?') +`"
                    :prefilled="true"
                    @success="success"
                    @close="update"
                    @hook:mounted="formMounted">
            <div class="bbn-padding"
                 style="padding-bottom: 4rem">
              <div>
                ` + bbn._('Check the box to create local folder of translation\'s files for the language in this path') + `
              </div>
              <div class="bbn-vpadding bbn-grid bbn-grid-gap"
                  style="grid-template-columns: repeat(2, 1fr)">
                <div bbn-for="l in source.data.configured_langs"
                    class="bbn-spadding bbn-radius bbn-alt-background"
                    :key="l"
                    ref="checkbox">
                  <bbn-checkbox :checked="source.row.languages.includes(getCode(l))"
                                @change="toggleLanguage"
                                :disabled="getCode(l) === source.data.language"
                                component="appui-i18n-lang"
                                :component-options="{code: getCode(l)}"
                                :value="l"/>
                </div>
              </div>
            </div>
            <div class="bbn-s bbn-padding bbn-bottom-left bbn-bottom-right"
                 style="bottom: 1rem"
                 bbn-html="message"/>
          </bbn-form>
        `,
        props: ['source', 'data'],
        data(){
          return {
            root: appui.plugins['appui-i18n'] + '/',
            widget: this.closest('bbn-floater').opener
          };
        },
        computed: {
          message(){
            return bbn._('If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form') + ' ( <i class="nf nf-fa-cogs"></i> ) ' + bbn._('in the dashboard')
          },
        },
        methods: {
          formMounted(){
            //push the source language of the path in the array row.languages to have it as default language
            if (!this.source.row.languages.includes(this.source.data.language)) {
              this.source.row.languages.push(this.source.data.language);
            }
          },
          getCode(id){
            return bbn.fn.getField(this.source.data.primary, 'code', {id: id});
          },
          //change the languages of locale dirs
          toggleLanguage(val, obj) {
            let code = this.getCode(obj.value);
            if (this.source.row.languages.includes(code)) {
              this.source.row.languages.splice(this.source.row.languages.indexOf(code), 1);
            }
            else {
              this.source.row.languages.push(code);
            }
          },
          update(){
            this.widget.remakeCache();
          },
          success(d){
            this.send_no_strings = false;
            if (!!d.success) {
              this.widget.remakeCache();
              if ( d.ex_dir.length ){
                d.ex_dir.forEach((v, i) => {
                  //this.source.data.widget.remakeCache();
                  this.$nextTick(() => {
                    let st = bbn._(
                      '%s translation files successfully files deleted',
                      bbn.fn.getField(this.source.data.primary, 'text', 'code', v)
                    );
                    appui.success(st)
                  });
                });
              }
              if ( d.new_dir.length ){
                d.new_dir.forEach((v, i) => {
                  //this.source.data.widget.remakeCache();
                  appui.success(bbn._(
                    '%s translation files successfully created',
                    bbn.fn.getField(this.source.data.primary, 'text', 'code', v)
                  ));
                });
              }

              if (d.done > 0) {
                appui.success(bbn._('%s new strings found in this path', d.done));
              }

              if ( this.closest('bbn-router') ){
                let tabs = this.closest('bbn-router').findAll('bbn-container');
                bbn.fn.log('tabs', tabs, this.source.row.id_option)
                tabs.forEach((v, i) => {
                  if ( v.url === 'expressions/'+ this.source.row.id_option ){
                    var tab = v;
                    bbn.fn.log('tab',v)
                    v.reload();
                  }
                });
              }
            }
            else if (!!d.no_strings) {
              /** change the property no_strings of the widget to render html */
              this.widget.noStrings = true
              appui.warning(bbn._("There are no strings in this path"));
            }
          }
        }
      }
    }
  }
})();
