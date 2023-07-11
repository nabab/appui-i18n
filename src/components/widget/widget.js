(() => {
  return {
    props: ['source'],
    data(){
      return {
        /** @todo this property should be true after the success of the form in the case of return of d.no_strings = true*/
        no_strings : false,
        id_option: this.source.id,
        //source language of the path
        language: this.source.language,
        /** the css class for progress bar the value is decided by the watch of progress_bar_val */
        progress_bar_class: '',
        root: appui.plugins['appui-i18n'] + '/',
        dashboard: appui.getRegistered('appui-i18n-dashboard')
      }
    },
    computed: {
      container(){
        let ct = this.closest('bbn-container');
        if (ct) {
          return ct.getComponent()
        }
        return null;
      },
      primary(){
        return this.dashboard.primary;
      },
      id_project(){
        return this.dashboard.idProject;
      },
      projectName(){
        return !!this.dashboard
          && !!this.dashboard.currentProject
          && !!this.dashboard.currentProject.name ?
          this.dashboard.currentProject.name :
          '';
      },
      projectCode(){
        return !!this.dashboard
          && !!this.dashboard.currentProject
          && !!this.dashboard.currentProject.code ?
          this.dashboard.currentProject.code :
          '';
      },
      parentSource(){
        if (this.container) {
          let ct = this.container.closest('bbn-container');
          if (ct) {
            return ct.source;
          }
        }
      },
      widget_idx(){
        if (this.parentSource) {
          //return the real index of this widget in the array of data of dashboard it works also after drag and drop
          let data = this.parentSource.data;
          return bbn.fn.search(data, 'id', this.source.id);
        }
        return null;
      },
      //used to render the widget, language of locale folder
      data_widget(){
        //if the source language of the path is set takes the array result from dashboard source
        let result = bbn.fn.clone(this.source.data_widget.result);
        if ( this.source.language && result ){
          bbn.fn.iterate(result, (r, l) => {
            result[l].class = '';
            result[l].class_db = '';

            if (r.num_translations >= 0) {
              result[l].val = !r.num ? 0 : r.num_translations/r.num * 100
              /** the css class for progress bar */
              if ((r.val >= 0) && (r.val <= 30)) {
                result[l].class = 'low'
              }
              else if ((r.val > 30) && (r.val <= 70)) {
                result[l].class = 'medium'
              }
              else if ((r.val > 70) && (r.val <= 100)) {
                result[l].class = 'high'
              }
            }

          });


          return result;
        }
        else {
          return [];
        }
      },
      //if the source language of the path is set takes the array result from dashboard source
      locale_dirs(){
        return this.source.data_widget.locale_dirs;
      },
      configured_langs(){
        return appui.getRegistered('appui-i18n-dashboard').source.configured_langs;
      },
      dd_primary(){
        let res = []
        if (this.primary) {
          this.primary.forEach((v, i) => {
            res.push({value: v.code, text: v.text})
          });
        }
        return res;
      },

    },
    methods: {
      normalize(val){
        if (val && (val > 0)){
          return parseFloat(val.toFixed(2))
        }
        else{
          return 0
        }

      },
      search: bbn.fn.search,
      getField: bbn.fn.getField,
      /** set the property language in db for this path */
      set_language(){
        /* the data coming from the post change the source of the dashboard at the index of this specific widget*/
        if (this.parentSource) {
          this.post(
            this.root + 'actions/define_path_lang', {
              id_option: this.source.id,
              language: this.source.language,
              id_project: this.id_project
            }, (d) => {
              if ( d.success ){
                delete d.success;
                delete d.time;
                this.$nextTick( () => {
                  this.source.data_widget = d.data_widget;
                  this.remake_cache();
                  appui.success(bbn._('Source language set'));
                  this.$forceUpdate();
                });
              }
            }
          );
        }
      },
      set_cfg(){
        this.post(this.root + 'options/set_lang', {
          id_option: this.source.id,
          language: this.source.language,
          id_project: this.id_project
        }, (d) => {
          if ( d.success ){
            this.source.language = null
            appui.success(bbn._('Source language reset'));
          }
        })
      },
      /** removes the property language of the path */
      remove_language(){
        this.post(this.root + '/actions/delete_path_lang', {
          id_option: this.source.id,
          language: this.source.language,
          id_project: this.id_project
        }, (d) => {
          if ( d.success ){
            this.source.language = null
            appui.success(bbn._('Source language reset'));
          }
        })
      },
      /** removes the property language of the option from its cfg */
      remove_cfg(){
        this.post(this.root + '/options/remove_lang', {
          id_option: this.source.id
        }, (d) => {
          if ( d.success ){
            this.source.language = null
            appui.success(bbn._('Source language reset for this option'));
          }
        })
      },
      delete_locale_folder(){
        this.confirm('Are you sure you want to delete the folder locale for this path?',()=>{
          this.post(this.root + 'actions/delete_locale_folder', {
            id_option: this.source.id,
            id_project: this.id_project
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
        //send arguments[0] (id_option of the path) to 'page/expressions/'
        //only if the the language of the path is set
        //page/expressions/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if (this.configured_langs !== undefined) {
          bbn.fn.link(this.root + 'page/expressions/' + this.projectCode + '/' + this.source.id);
        }
        else if (this.id_project !== 'options') {
          this.alert(bbn._('You have to configure at least a language of translation using the button') +' <i class="nf nf-fa-flag"></i> ' + bbn._('of the widget before to open the strings table'));
        }
      },
      remake_cache(){
        if (this.parentSource && (this.source.language != null)) {
          this.post(this.root + 'actions/reload_widget_cache', {
            id_option: this.source.id,
            id_project: this.id_project
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
      find_options(){
        if ( this.id_project === 'options' ){
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
            title: bbn._("Define languages for the translation"),
            scrollable: true,
            component: this.$options.components.languagesForm,
            source: {
              row: {
                id_option: this.source.id,
                languages: this.locale_dirs
              },
              data: {
                id_project: this.id_project,
                primary: this.primary,
                language: this.source.language,
                widget_idx : this.widget_idx,
                //langs configured for the project
                configured_langs: this.configured_langs,
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
          languages: this.locale_dirs,
          id_project: this.id_project,
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
            <div class="bbn-padded"
                 style="padding-bottom: 4rem">
              <div>
                ` + bbn._('Check the box to create local folder of translation\'s files for the language in this path') + `
              </div>
              <div class="bbn-vpadded bbn-grid"
                  style="grid-template-columns: repeat(2, 1fr)">
                <div v-for="l in source.data.configured_langs"
                    class="bbn-spadded bbn-radius bbn-alt-background"
                    :key="l"
                    ref="checkbox">
                  <bbn-checkbox :checked="source.row.languages.includes(getCode(l))"
                                @change="toggleLanguage"
                                :disabled="getCode(l) === source.data.language"
                                component="appui-i18n-lang"
                                :componentOptions="{code: getCode(l)}"
                                :value="l"/>
                </div>
              </div>
            </div>
            <div class="bbn-s bbn-padded bbn-bottom-left bbn-bottom-right"
                 style="bottom: 1rem"
                v-html="message"/>
          </bbn-form>
        `,
        props: ['source', 'data'],
        data(){
          return {
            root: appui.plugins['appui-i18n'] + '/'
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
            return;

            let dashboard = this.closest('bbn-container').getComponent(),
              widgets = dashboard.findAll('bbn-widget'),
              this_widget = dashboard.source.data[this.source.data.widget_idx],
              //idx = $.inArray(obj.value, dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs );
              idx = dashboard.source.data[this.source.data.widget_idx].data_widget.locale_dirs.indexOf(obj.value);
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
              //if ( $.inArray(obj.value, this.source.row.languages ) < 0 ){
              if ( this.source.row.languages.indexOf(obj.value) < 0 ){
                this.source.row.languages.push(obj.value);
              }
              dashboard.source.data[this.source.data.widget_idx].data_widget.result[obj.value] = {
                lang: obj.value,
                num_translations: 0,
                num: 0
              }
            }
          },
          get_widget(){
            let widgets = this.closest('bbn-container').findAll('bbn-widget'),
            widget = bbn.fn.filter(widgets, (a) => {
              return a.uid === this.source.row.id_option
            });
            return widget[0].$children[0];
          },
          update(){
            this.get_widget().remake_cache();
          },
          success(d){
            this.send_no_strings = false;
            if (!!d.success) {
              this.get_widget().remake_cache();
              if ( d.ex_dir.length ){
                d.ex_dir.forEach((v, i) => {
                  //this.source.data.widget.remake_cache();
                  this.$nextTick(() => {
                    let st = bbn.fn.getField(this.source.data.primary, 'text', 'code', v) + ' translation files successfully files deleted';
                    appui.success(st)
                  })
                });
              }
              if ( d.new_dir.length ){
                d.new_dir.forEach((v, i) => {
                  //this.source.data.widget.remake_cache();
                  appui.success(  bbn.fn.getField(this.source.data.primary, 'text', 'code', v) + ' translation files successfully created')
                });
              }
              if (d.done > 0) {
                appui.success(d.done + ' ' + bbn._('new strings found in this path') )
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
              this.get_widget().no_strings = true
              appui.warning(bbn._("There are no strings in this path"));
            }
          }
        }
      }
    }
  }
})();
