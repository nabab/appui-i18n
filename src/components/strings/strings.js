(() => {
  return {
    props: ['source'],
    data(){
      return {
        primary: bbn.vue.closest(this,'bbn-tabnav').$parent.source.primary,
        column_length: true,
        hidden_cols: [],
        showAlert: false
      }
    },
    mounted(){
      //bbn.fn.log('this',this, this.mapData, this.hidden_cols, this.column_length)
    },
    computed: {
      /** the source language of this id_option */
      source_lang(){
        return bbn.fn.get_field(this.primary, 'code' , this.source.res.path_source_lang, 'text')
      },
      /**array of columns for the table*/
      columns(){
        let r = [],
          i = 0,
          def = null;
        var field = '',
          vm = this;
        for ( let n in this.source.res.languages ){
          var obj = {
            field: this.source.res.languages[n],
            title:  ( this.source.res.languages[n] === this.source.res.path_source_lang) ? (bbn.fn.get_field(this.primary, 'code', this.source.res.languages[n], 'text') + '  <i class="fa fa-asterisk" title="This is the original language of the expression"></i>') : bbn.fn.get_field(this.primary, 'code', this.source.res.languages[n], 'text'),
            editable: true
          };
          /**render for the columns when the  project is not options */
          if ( ( this.source.id_project !== 'options' )  ){
            obj.render = (row) => {
              var columns = bbn.vue.find(vm, 'bbn-table').columns,
                this_field;

              columns.forEach( (v, i) => {
                if ( v.field === this.source.res.languages[n] ){
                  this_field = columns[i].field;
                }
              });
              var idx = bbn.fn.search(vm.translations_db, 'id_exp', row.id_exp ),
                translation_db = vm.translations_db[idx][this_field],
                translation_po = vm.mapData[idx][this_field];
              if ( ( translation_db !== false )  && ( translation_db === translation_po ) ){
                return row[this_field] + '<i class="fas fa-check bbn-large bbn-green" title="Expression correctly inserted in db and po file" style="float:right"><i/>'
              }

              else if ( ( translation_db !== false ) && ( translation_db !== row[this_field ] ) &&  ( this.source.id_project !== 'options') ){
                return translation_db + '<i style="float:right" class="fas fa-exclamation-triangle bbn-large bbn-red" title="Expression correctly inserted in db but not in po files, be sure to update translations files from the orange button of the toolbar"><i/>'
              }

              else if ( ( translation_db !== translation_po ) && ( row[this_field ] !== '' ) &&  ( this.source.id_project !== 'options') ){
                return  vm.mapData[idx][this_field] + '<i style="float:right" class="fas fa-exclamation-triangle bbn-large bbn-red" title="Expression correctly inserted in db but not in po files, be sure to update translations files from the orange button of the toolbar"><i/>'
              }

              else {
                return row[this_field ]
              }
            }
          }
          r.push(obj);
          if ( n === this.source.res.source_lang ){
            def = i;
          }
          i++;
        }
        /** column occurrence --- doesn't exist for project option */
        if ( this.source.id_project !== 'options' ){
          r.push({
            ftitle: bbn._('Number of occurrences of the strings in the path files'),
            title: '#',
            field: 'occurrence',
            editable: false,
            render(row){
              return row.occurrence ? row.occurrence : 0;
            },
            width: 40,
            cls: 'bbn-c'
          });
        }
        r.push({
          ftitle: bbn._('Remove original expression'),
          buttons: this.buttons,
          width: 40,
          cls: 'bbn-c'
        });
        return r;
      },
      /** contains original from po file and translations from db, used to render differences between the strings in the po file and the one in db */
      translations_db(){
        let res = [],
          source_lang = this.source.res.path_source_lang;
        this.source.res.strings.forEach( (obj, idx ) => {
          let ob = {};
          for (let prop in obj){
            //bbn.fn.log('translation_db', idx, source_lang,'prop', prop)
            ob['original_exp'] = this.source.res.strings[idx][source_lang].original;
            ob['id_exp'] = this.source.res.strings[idx][source_lang].id_exp;
            ob[prop] = this.source.res.strings[idx][prop].translations_db;
          }
          res.push(ob);
        });
        return res
      },
      /** the source of the table */
      mapData(){
        let res = [],
          source_lang = this.source.res.path_source_lang;
        if ( ( this.source.id_project !== 'options') && (this.source.res.strings)){
          this.source.res.strings.forEach( (obj, idx ) => {
            let ob = {};
            for (let prop in obj){
              //number of occurrence of the strings in files of the path
              //   ob['occurrence'] = this.source.res.strings[idx][source_lang].paths.length || 0;
              //takes the path of the string from file po
              if (this.source.res.strings[idx][source_lang] && this.source.res.strings[idx][source_lang].occurrence ){
                ob['occurrence'] =  this.source.res.strings[idx][source_lang].occurrence;
              }
              else {
                ob['occurrence'] = 0;
              }
              ob['path'] = this.source.res.strings[idx][source_lang] ? this.source.res.strings[idx][source_lang].paths : [];
              ob['original_exp'] = this.source.res.strings[idx][source_lang] ? this.source.res.strings[idx][source_lang].original : false;
              ob['id_exp'] = this.source.res.strings[idx][source_lang] ? this.source.res.strings[idx][source_lang].id_exp : false;
              ob[prop] = this.source.res.strings[idx][prop].translations_po;
            }
            res.push(ob);
          })
        }
        else {
          res = this.source.res.strings.map((o) => {
            if ( o.original ){
              o.original_exp = o.original;
              delete o.original;
              return o;
            }
          });
        }
        return res;
      }
    },
    methods: {
      /** generate po files for all columns of the table */
      generate(){
        this.showAlert = true;
        if ( this.source.res.languages.length ){
          bbn.fn.post(this.source.root + 'actions/generate', {
            id_option: this.source.id_option,
            languages: this.source.res.languages,
            id_project: this.source.id_project
          }, (d) => {
            if ( d.success ){
              d.languages = d.languages.map( (v) => {
                return bbn.fn.get_field(this.primary, 'code', v, 'text');
              });
              let tabnav = bbn.vue.closest(this, 'bbn-tabnav');
              if ( bbn.vue.find(tabnav, 'bbn-dashboard') !== undefined ){

                let dashboard = bbn.vue.find(tabnav, 'bbn-dashboard'),
                  widgets = bbn.vue.findAll(dashboard, 'bbns-widget');
                if ( widgets !== undefined ){
                  widgets.forEach((v, i) => {
                    if ( v.uid === this.source.id_option ){
                      let widget = v;
                      bbn.vue.find(v, 'appui-i18n-widget').remake_cache()

                    }
                  });
                }

              }
              this.source.res.strings = d.strings;
              if ( this.closest('bbn-tabnav') ) {
                bbn.fn.log('before table update', this.closest('bbn-tabnav'))
                //if the dashboard have already been created it replace data of the widget with new data arriving from the new cache of the widget.
                if (bbn.vue.find(this.closest('bbn-tabnav'), 'bbn-dashboard') !== undefined) {
                  let dashboard = bbn.vue.find(this.closest('bbn-tabnav'), 'bbn-dashboard'),
                    widgets = bbn.vue.findAll(dashboard, 'bbns-widget'),
                    idx = bbn.fn.search(widgets, 'uid', this.source.id_option),
                    cp = bbn.vue.closest(dashboard, 'bbns-tab').getComponent();
                   cp.source.data[idx].data_widget = d.widget;
                }
              }
              appui.success('Files of translation successfully updated for '+ d.languages.join(' and ') );

              //this.remake_cache();
              this.$nextTick(() => {
                bbn.vue.find(this, 'bbn-table').updateData();
                this.showAlert = false;
              });
            }
          });
        }
        else {
          this.alert('You have to configure at least a language using the button <i class="fa fa-flag"></i> of the widget in the dashboard');
          this.showAlert = false;
        }
      },
      /** checks if there are new strings in the files of the path */
      find_strings(){
        //bbn.fn.log('id_option',this.source.id_option);
        bbn.fn.post(this.source.root + 'actions/find_strings', {
          id_option: this.source.id_option,
          language: this.source.res.path_source_lang,
          languages: this.source.res.languages
        }, (d) => {
          if ( d.success ){
            if ( ( d.done > 0 ) && ( d.news.length ) ){
              //devo fare generate per poterle avere in tabella...
              appui.success(d.done + ' new strings found')
              this.generate();

            }
            else {
              appui.warning('No new strings found')
            }
          }
        } );
      },
      /** button delete row of table */
      buttons(){
        let res = [];
        res.push({
          command: this.delete_expression,
          icon: 'fas fa-times',
          title: 'Delete original expression'
        });
        return res;
      },
      /** deletes the original expression from db, if the expression is not deleted before from the file (using the link of the expander to the code) it will be again in the table when the table is reloaded or updated */
      delete_expression(row){
        let id_exp = row.id_exp,
          data = this.mapData,
          idx = bbn.fn.search(data, { id_exp: id_exp });
        this.getPopup().confirm('Did you remove the expression from code before to delete the row?', () => {
          bbn.fn.post(this.source.root + 'actions/delete_expression', { id_exp: row.id_exp, exp: row.original_exp },  (d) => {
            if ( d.success ){
              data.splice(idx, 1);
              this.$refs.strings_table.updateData();
              appui.success('Expression deleted');
            }
            else{
              appui.error('An error occurred while deleting the expression');
            }
          } );
        })
      },
      /** called at @change of the table (when the idx of the row focused changes), insert translation in db and remake the po file */
      insert_translation(row,idx){
        var to_delete = [];
        //creates an array of languages to delete
        this.source.res.languages.forEach((v, i) => {
          if ( row[v] === '' ){
            to_delete.push(v)
          }
        })
        bbn.fn.post(this.source.root + 'actions/insert_translations', {
          to_delete : to_delete,
          row: row,
          langs: this.source.res.languages,
          id_option: this.source.id_option,
          id_project: this.source.id_project,
          row_idx: idx
        }, (d) => {
          if (d.success && !d.deleted.length && d.modified_langs.length ){
            d.modified_langs.forEach((v, i) => {
              this.source.res.strings[idx][v]['translations_db'] = d.row[v];
              //I have a bug, after the update of the table this.mapData, the source of the table is wrong for the row if i don't force it to be correct
              this.mapData[idx][v] = this.source.res.strings[idx][v]['translations_po'];
            } )
            let table = bbn.vue.find(this, 'bbn-table');
            table.updateData();
            appui.success('Translation saved');
          }
          else if ( !d.success && !d.deleted.length ){
            appui.error('An error occurred while saving translation');
          }
          else if ( d.success && d.deleted.length ){
            d.deleted.forEach((v, i) => {
              this.source.res.strings[idx][v]['translations_db'] = d.row[v];
              this.mapData[idx][v] = this.source.res.strings[idx][v]['translations_po'];
              appui.warning('Translation deleted from db');
            })
          }
          if ( this.closest('bbn-tabnav') ) {
            bbn.fn.log('before table update', this.closest('bbn-tabnav'))
            //if the dashboard have already been created it replace data of the widget with new data arriving from the new cache of the widget.
            if (bbn.vue.find(this.closest('bbn-tabnav'), 'bbn-dashboard') !== undefined) {
              let dashboard = bbn.vue.find(this.closest('bbn-tabnav'), 'bbn-dashboard'),
                widgets = bbn.vue.findAll(dashboard, 'bbns-widget'),
                idx = bbn.fn.search(widgets, 'uid', this.source.id_option),
                cp = bbn.vue.closest(dashboard, 'bbns-tab').getComponent();
               cp.source.data[idx].data_widget = d.widget;
            }
          }
        });
      },
      /** remakes the model of table in cache */
      remake_cache(){
        this.column_length = false;
        //this.generate();
        this.showAlert = true;
        bbn.fn.post('internationalization/actions/reload_table_cache', {
          id_option: this.source.id_option,
          id_project: this.source.id_project,
          routes: this.source.root
        }, (d) => {
          if ( d.success ){
            let diff = ( d.res.total - this.source.res.total );
            this.source.res.languages = d.res.languages;
            this.source.res.strings = d.res.strings;
            //bbn.vue.find(this, 'bbn-table').updateData();
            if ( diff > 0 ){
              appui.warning(diff + ' new string(s) found in ' + this.source.res.path);
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
              //bbn.vue.find(this, 'bbn-table').updateData();
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.res.path + ' files');
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
            }
            else if ( diff = 0 ){
              appui.warning('There are no changes in data')
            }
            this.$nextTick(() => {
              bbn.vue.find(this, 'bbn-table').updateData();
              this.column_length = true;
              this.showAlert = false;
            });
            let tabnav = bbn.vue.closest(this, 'bbn-tabnav');
            /*if( bbn.vue.find(tabnav, 'bbn-dashboard') !== undefined ){
              let dashboard = bbn.vue.find(tabnav, 'bbn-dashboard'),
                tab = bbn.vue.closest(dashboard, 'bbns-tab'),
                cp = tab.getComponent(),
                data = cp.source.data,
                widget_idx = bbn.fn.search(data, 'id', this.source.id_option);
            }*/
          }
        })
      },
    },
    watch: {
      showAlert(val){
        if ( val){
          this.alert(bbn._('Wait for the ending of the process before to make other actions in this tab') );
        }
        else{
          this.alert().close()
        }
      },
      hidden_cols(val){
        /** function to make the difference between two arrays */
        Array.prototype.diff = function (a) {
          return this.filter(function (i) {
            return a.indexOf(i) === -1;
          });
        };
        /** creates the array not hidden making the diff between checked and unchecked langs */
        var not_hidden = this.source.res.languages.diff(val);
        /** val is the array of checked langs */
        val.forEach( (v, i) => {
          /** the index of the column to hide */
          let col_idx = bbn.fn.search(this.columns, 'field', v),
            /** the column to hide */
            col = this.columns[col_idx],
            idx = $.inArray(v, this.hidden_cols);
          if ( idx > -1 ){
            col.hidden = true;
          }
        });
        /** loop on the array of not to hide langs */
        if ( not_hidden.length ){
          not_hidden.forEach( (v, i) => {
            let col_idx = bbn.fn.search(this.columns, 'field', v),
              col = this.columns[col_idx];
            col.hidden = false;

          } )
        }

      },
    },
    components:{
      /** the toolbar of the table, the template is on html/template folder */
      'toolbar-strings-table': {
        template:'#toolbar-strings-table',
        props: ['source'],
        data(){
          return {
            /** v-model of multiselect when project === options */
            to_hide_col:[],
            hide_source_language: false,
            tab: null
          }
        },
        methods: {
          /** takes the methods from the parent component using @var this.tab declared at created */
          generate(){
            return this.tab ? this.tab.generate() : null;
          },
          find_strings(){

            return this.tab ? this.tab.find_strings() : null;

          },
          remake_cache(){

            return this.tab ? this.tab.remake_cache() : null;
          },
          hide_col(val){
            if ( val && this.tab ){
              let idx = $.inArray(val, this.tab.hidden_cols);
              if ( idx > -1 ){
                this.tab.hidden_cols.splice(idx, 1);
              }
              else {
                this.tab.hidden_cols.push(val);
              }
            }

          }
        },
        mounted(){
          this.tab = bbn.vue.closest(this, 'appui-i18n-strings');
        },
        watch: {
          /** v-model of bbn-switch used to hide the column of original language */
          hide_source_language(val, oldVal){
            // get the index of the column of source language
            var idx = bbn.fn.search(this.tab.columns, 'field', this.tab.source.res.path_source_lang );
            if ( ( val === true ) && ( idx > -1) ){
              this.tab.columns[idx].hidden = true;
              this.tab.$forceUpdate()
            }
            else if (( val === false )){
              this.tab.columns[idx].hidden = false;
              this.tab.$forceUpdate()
            }
          },

        },
        computed: {
          languages(){
            return this.tab ? this.tab.source.res.languages : []
          },
          id_project(){
            return this.tab ? this.tab.source.id_project : null
          },

        }

      },
      /** expander of the table, shows the path of the files containing the string */
      'file_linker': {
        props: ['source'],
        data(){
          return {
            id_project : null
          }
        },
        methods: {
          link_ide(path){
            let idx = path.lastIndexOf('/'),
              st = 'ide/editor/file/',
              ext = path.slice(idx, path.length);
            st += path.slice(0, idx);
            st += '/_end_';
            if (ext === '/public'){
              st += '/php'
            }
            else{
              st += ext;
            }

            bbn.fn.link(st)
          }
        },
        template: `
          <ul v-if="id_project!== 'options' " style="width:100%; list-style-type:none; padding-left:0">
          	<li class="bbn-vspadded bbn-grid-fields" :source="source" v-for="s in source.path">
          		<span class="bbn-lg">File:</span>
          		<a v-text="s" @click="link_ide(s)" style="width:100%;cursor:pointer" title="Open the file in i.d.e"></a>
            </li>
          </ul>`,
        mounted(){
          this.id_project = bbn.vue.closest(this, 'appui-i18n-strings').source.id_project
        }
      },
    }
  }
})();
