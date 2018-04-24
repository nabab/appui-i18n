(() => {
  var this_tab;
  return {
    created(){
      this_tab = this;
    },
    data(){
      return {
        primary: this.languages.source.primary,
        column_length: true,
        hidden_cols: []
      }
    },
    methods: {
      /** generate po files for all columns of the table */
      generate(){
        if ( this.source.res.languages.length ){
          bbn.fn.post(this.source.root + 'actions/generate', {id_option: this.source.id_option, languages: this.source.res.languages}, (d) => {
            if ( d.success ){
              d.languages = d.languages.map( (v) => {
                return bbn.fn.get_field(this.primary, 'code', v, 'text');
              })
              appui.success('Files of translation successfully updated for '+ d.languages.join(' and ') );
              this.remake_cache();
            }
          });
        }
        else {
          bbn.fn.alert('You have to configure at least a language using the button <i class="fa fa-flag"></i> of the widget in the dashboard')
        }
      },
      /** checks if there are new strings in the files of the path */
      find_strings(){
        bbn.fn.post(this.source.root + 'actions/find_strings', {
          id_option: this.source.id_option,
          language: this.source.res.path_source_lang
        }, (d) => {
          if ( d.success ){
            if ( d.done > 0 ){
              this.remake_cache();
              appui.success(d.done + ' new strings found')
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
          icon: 'fa fa-close',
          title: 'Delete original expression'
        });
        return res;
      },
      /** deletes the original expression from db, if the expression is not deleted before from the file (using the link of the expander to the code) it will be again in the table when the table is reloaded or updated */
      delete_expression(row){
        let id_exp = row.id_exp,
          data = this.mapData,
          idx = bbn.fn.search(data, { id_exp: id_exp });
        bbn.fn.confirm('Did you remove the expression from code before to delete the row?', () => {
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
        bbn.fn.post(this.source.root + 'actions/insert_translations', {
          row: row,
          langs: this.source.res.languages,
          id_option: this.source.id_option
        }, (d) => {
          if (d.success && !d.deleted){
            appui.success('Translation saved');
            row = d.row;
            let table = bbn.vue.find(this, 'bbn-table');
            //wanted to update the widget from the table
            /*tab = bbn.vue.closest(this, 'bbn-tab'),
            tabnav = bbn.vue.closest(tab, 'bbn-tabnav'),
            dashboard = bbn.vue.find(tabnav, 'bbn-dashboard'),
            widgets = bbn.vue.findAll(dashboard, 'bbn-widget')
          ;*/
            this.mapData[idx] = d.row
            table.updateData();

          }
          else if ( !d.success && !d.deleted ){
            appui.error('An error occurred while saving translation');
          }
          else if ( d.success && d.deleted ){
            appui.warning('Translation deleted from db');
          }
        });
      },
      /** remakes the model of table in cache */
      remake_cache(){
        this.column_length = false;
        bbn.fn.post('internationalization/actions/reload_table_cache', {
          id_option: this.source.id_option
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
              //bbn.vue.find(this, 'bbn-table').updateData();
            }
            else if ( diff = 0 ){
              appui.warning('There are no changes in data')
            }
            this.column_length = true
          }
        })
      },

    },
    props: ['source'],
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
                  translation_db = vm.translations_db[idx][this_field];
              if ( ( translation_db !== false ) && ( translation_db === row[this_field] ) ){
                return row[this_field] + '<i class="fa fa-check bbn-large bbn-green" title="Expression found in translation file" style="float:right"><i/>'
              }
              else if ( ( row[this_field ] !== "" ) && ( translation_db !== row[this_field ] ) &&  ( this.source.id_project !== 'options') ){
                return row[this_field] + '<i style="float:right" class="fa fa-thumbs-up bbn-large bbn-green" title="Translation files updated"><i/>'
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
        if ( this.source.id_project !== 'options'){
          this.source.res.strings.forEach( (obj, idx ) => {
            let ob = {};
            for (let prop in obj){
              //number of occurrence of the strings in files of the path
              //   ob['occurrence'] = this.source.res.strings[idx][source_lang].paths.length || 0;
              //takes the path of the string from file po
              if ( this.source.res.strings[idx][source_lang].occurrence ){
                ob['occurrence'] =  this.source.res.strings[idx][source_lang].occurrence;
              }
              else {
                ob['occurrence'] = 0;
              }
              ob['path'] = this.source.res.strings[idx][source_lang].paths;
              ob['original_exp'] = this.source.res.strings[idx][source_lang].original;
              ob['id_exp'] = this.source.res.strings[idx][source_lang].id_exp;
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
    watch: {
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
            hide_source_language: false
          }
        },
        methods: {
          /** takes the methods from the parent component using @var this_tab declared at created */
          generate(){
            return this_tab.generate();
          },
          find_strings(){
            return this_tab.find_strings();
          },
          remake_cache(){
            return this_tab.remake_cache();
          },
          hide_col(val){
            if ( val ){
              let idx = $.inArray(val, this_tab.hidden_cols);
              if ( idx > -1 ){
                this_tab.hidden_cols.splice(idx, 1);
              }
              else {
                this_tab.hidden_cols.push(val);
              }
            }

          }
        },
        watch: {
           /** v-model of bbn-switch used to hide the column of original language */
          hide_source_language(val, oldVal){
            /** get the index of the column of source language */
            var idx = bbn.fn.search(this_tab.columns, 'field', this_tab.source.res.path_source_lang );
            if ( ( val === true ) && ( idx > -1) ){
              this_tab.columns[idx].hidden = true;
              this_tab.$forceUpdate()
            }
            else if (( val === false )){
              this_tab.columns[idx].hidden = false;
              this_tab.$forceUpdate()
            }
          },

        },
        computed: {
          languages(){
            return this_tab.source.res.languages
          },
          id_project(){
            return this_tab.source.id_project
          },

        }

      },
      /** expander of the table, shows the path of the files containing the string */
      'file_linker': {
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
        template:
          '<ul style="width:100%; list-style-type:none; padding-left:0">' +
          '<li class="bbn-vspadded bbn-grid-fields" :source="source" v-for="s in source.path">' +
          '<span class="bbn-lg">File:</span>' +
          '<a v-text="s" @click="link_ide(s)" style="width:100%;cursor:pointer" title="Open the file in i.d.e"></a>' +
          ' </li>' +
          '</ul>',
        props: ['source'],
      },
    }
  }
})();