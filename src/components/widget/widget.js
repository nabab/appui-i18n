(() => {
  return {
    props: ['source'],
    name: 'widget',
    data(){
      return {
        /** @todo this property should be true after the success of the form in the case of return of d.no_strings = true*/
        no_strings : false,
        id_option: bbn.vue.closest(this, 'bbn-widget').uid,
        primary: this.closest('bbn-router').$parent.source.primary,
        //source language of the path
        language: null,
        /** the css class for progress bar the value is decided by the watch of progress_bar_val */
        progress_bar_class: '',
        root: appui.plugins['appui-i18n'] + '/'
      }
    },
    computed: {
      id_project(){
        return this.closest('bbn-container').getComponent().id_project
      },
      project_name(){
        return this.closest('bbn-container').getComponent().project_name
      },
      widget_idx(){
        //return the real index of this widget in the array of data of dashboard it works also after drag and drop
        let data = this.parentSource.data;
        return bbn.fn.search(data, 'id', this.id_option);
      },
      parentSource() {
        return this.closest('bbn-container').getComponent().source;
      },
      //used to render the widget, language of locale folder
      data_widget(){
        //if the source language of the path is set takes the array result from dashboard source
        let result = this.parentSource.data[this.widget_idx].data_widget.result;
        if ( this.language && result ){
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
        let tab = bbn.vue.closest(this,'bbn-container'),
            cp = tab.getComponent(),
            locale_dirs = [];
        if ( cp && cp.source.data && (this.widget_idx > -1) && cp.source.data[this.widget_idx].data_widget){
           locale_dirs = cp.source.data[this.widget_idx].data_widget.locale_dirs
        }
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
        this.post(this.root + 'actions/define_path_lang', {
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
              appui.success(bbn._('Source language set'));
              this.$forceUpdate();
            });
          }
        })
      },
      set_cfg(){
        this.post(this.root + 'options/set_lang', {
          'id_option': this.id_option,
          'id_project': this.id_project,
          'language': this.language
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language reset'));
          }
        })
      },
      /** removes the property language of the path */
      remove_language(){
        this.post(this.root + '/actions/delete_path_lang', {
          'id_option': bbn.vue.closest(this.$parent, 'bbn-container').getComponent().widgets[bbn.vue.closest(this, 'bbn-widget').index].key,
          'language': this.language,
          'id_project': this.id_project
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language reset'));
          }
        })
      },
      /** removes the property language of the option from its cfg */
      remove_cfg(){
        this.post(this.root + '/options/remove_lang', {
          id_option: this.id_option
        }, (d) => {
          if ( d.success ){
            this.language = null
            appui.success(bbn._('Source language reset for this option'));
          }
        })
      },
      delete_locale_folder(){
        this.confirm('Are you sure you want to delete the folder locale for this path?',()=>{
          this.post(this.root + 'actions/delete_locale_folder', {
            id_option: this.id_option,
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
        //send arguments[0] (id_option of the path) to 'internationalization/page/path_translations/'
        //only if the the language of the path is set
        //internationalization/page/path_translations/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if ( ( this.configured_langs !== undefined ) && ( this.id_project !== 'options')){
          bbn.fn.link(this.root + 'page/path_translations/' +this.project_name +'/'+ this.id_option);
        }
        else if ( (this.configured_langs === undefined) && ( this.id_project !== 'options')){
          this.alert(bbn._('You have to configure at least a language of translation using the button') +' <i class="nf nf-fa-flag"></i> ' + bbn._('of the widget before to open the strings table') );
        }
        else if ( ( this.configured_langs !== undefined ) && ( this.id_project === 'options') ){
          bbn.fn.link(this.root + 'page/path_translations/options/' + this.id_option );
        }
      },
      remake_cache(){
        if ( this.language != null ){
          this.post(this.root + 'actions/reload_widget_cache', {
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
        let url = this.root + 'actions/reload_widget_cache';
        this.post(url, { id_option: this.source.id_option },  (d) => {
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
          this.getPopup({
            width: 500,
            height: 600,
            title: bbn._("Define languages for the translation"),
            component: this.$options.components['languages-form-locale'],
            source: {
              row: {
                id_option: bbn.vue.closest(this, 'bbn-widget').uid,
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
                //widget: this,
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
      //this.language = this.parentSource.data[this.$parent.index].language;
      this.language = this.parentSource.data[this.widget_idx].language;
    },
    mounted(){
      this.closest('bbn-dashboard').onResize();
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
      'languages-form-locale': {
        template: `
<bbn-form :source="source.row"
          :data="{id_project: source.data.id_project, language: source.data.language}"
          ref="form-locale"
          :action="( source.data.id_project === 'options' ) ? 'internationalization/options/find_options' : 'internationalization/actions/generate'"
          confirm-leave="`+ bbn._('Are you sure you want to exit without saving changes?') +`"
          :prefilled="true"
          @success="success"
          @close="update"
>
  <div class="bbn-grid-fields">
    <div style="height:300px;" class="bbn-padded bbn-middle">
      <span>`+ bbn._('Check the box to create local folder of translation\'s files for the language in this path') +`</span>
    </div>
    <div class="bbn-padded">
      <div v-for="l in source.data.configured_langs"
           class="bbn-vlpadded"
           :key="l"
           ref="checkbox">
        <bbn-checkbox :value="getField(source.data.primary, 'code', {id: l})"
                      :checked="checked_lang(l)"
                      @change="change_languages"
                      :disabled="getField(source.data.primary, 'code', {id: l}) === source.data.language"
                      :label="getField(source.data.primary, 'text', {id: l})"/>
      </div>
    </div>
   
  </div>
  <div class="bbn-s bbn-padded"
       v-html="message"
       style="position:absolute; bottom:0;left: 0;margin-bottom: 6px;margin-right:6px;"/>
</bbn-form>
        `,
        data(){
          return {}
        },
        mounted(){
          //push the source language of the path in the array row.languages to have it as default language
          //if ( $.inArray(this.source.data.language, this.source.row.languages) <= -1 ){
          
          if ( this.source.row.languages.indexOf(this.source.data.language) <= -1 ){  
            this.source.row.languages.push(this.source.data.language)
          }
        },
        computed: {
          message(){
            return bbn._('If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form') + ' ( <i class="nf nf-fa-cogs"></i> ) ' + bbn._('in the dashboard')
          },
        },
        methods: {
          getField: bbn.fn.getField,          
          //inArray: $.inArray,
          //change the languages of locale dirs
          change_languages(val, obj) {
            let dashboard = bbn.vue.closest(this, 'bbn-container').getComponent(),
              widgets = bbn.vue.findAll(dashboard, 'bbn-widget'),
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
          checked_lang(l){
            let code = bbn.fn.getField(this.source.data.primary, 'code', 'id', l);
            //if ( $.inArray(code, this.source.row.languages) > -1 ){
            if ( this.source.row.languages.indexOf(code) > -1 ){
              return true;
            }
            else {
              return false;
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
          generate_mo() {
            
            let id_option = this.get_widget().id_option;
            this.post(this.root + 'actions/generate_mo', {
              id_option: id_option, 
              id_project: this.id_project
            }, (d) => {
              if (d.success === true) {
                appui.success('Mo files correctly generated');
              }
            })
          },
          success(d){
            this.send_no_strings = false;
            if ( d.success ){
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
              } )
              
              
              }
              
              this.generate_mo();
              
              if ( d.done > 0 ){
                appui.success(d.done + ' ' + bbn._('new strings found in this path') )
              }
              if ( this.closest('bbn-router') ){
                let tabs = bbn.vue.findAll(this.closest('bbn-router') , 'bbns-container');
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
              this.get_widget().no_strings = true
              appui.warning(bbn._("There are no strings in this path"));
            }
          }
        },
        props: ['source', 'data'],
      }
    }
  }
})();