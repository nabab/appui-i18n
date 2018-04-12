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
      }
    },

    methods: {
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
      find_strings(){
        bbn.fn.post(this.source.root + 'actions/find_strings', {
          id_option: this.source.id_option,
          language: this.source.res.path_source_lang
        }, (d) => {
          if ( d.success ){
            if ( d.done > 0 ){
              appui.success(d.done + ' new strings found')
            }
            else {
              appui.warning('No new strings found')
            }
          }
        } );
      },
      buttons(){
        let res = [];
        res.push({
          command: this.delete_expression,
          icon: 'fa fa-close',
          title: 'Delete original expression'
        });
        return res;
      },
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
      insert_translation(row,idx){
        bbn.fn.post(this.source.root + 'actions/insert_translations', {
          row: row,
          langs: this.source.res.languages,
          id_option: this.source.id_option
        }, (d) => {
          if (d.success){
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
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      remake_cache(){
        // look for new strings/translations/ remakes the model in cache
        //column_length just used on a v-if of the table to remake the table if data changes
        this.column_length = false;
        bbn.fn.post('internationalization/actions/reload_table_cache', {
          id_option: this.source.id_option
        }, (d) => {
          if ( d.success ){
            let diff = ( d.res.total - this.source.res.total );
            this.source.res.languages = d.res.languages;
            this.source.res.strings = d.res.strings;
            bbn.vue.find(this, 'bbn-table').updateData();
            if ( diff > 0 ){
              appui.warning(diff + ' new string(s) found in ' + this.source.res.path);
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
              bbn.vue.find(this, 'bbn-table').updateData();
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.res.path + ' files');
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
              bbn.vue.find(this, 'bbn-table').updateData();
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
      columns(){
        let r = [],
          i = 0,
          def = null;
        var field = '',
          vm = this;
        for ( let n in this.source.res.languages ){
          r.push({
            field: this.source.res.languages[n],
            title:  ( this.source.res.languages[n] === this.source.res.path_source_lang) ? (bbn.fn.get_field(this.primary, 'code', this.source.res.languages[n], 'text') + '  <i class="fa fa-asterisk" title="This is the original language of the expression"></i>') : bbn.fn.get_field(this.primary, 'code', this.source.res.languages[n], 'text'),
            //fixed: n === this.source.source_lang,
            editable: true,
            render(row){
              var idx = bbn.fn.search(vm.translations_db, 'id_exp', row.id_exp ),
                translation_db = vm.translations_db[idx][this.field];
              //why this is the column??
              if ( ( translation_db !== false ) && ( translation_db === row[this.field] ) ){
                return row[this.field] + '<i class="fa fa-check bbn-large bbn-green" title="Expression found in translation file" style="float:right"><i/>'
              }
              else if ( ( row[this.field] !== "" ) && ( translation_db !== row[this.field] ) ){
                return row[this.field] + '<i style="float:right" class="fa fa-thumbs-up bbn-large bbn-green" title="Translation files updated"><i/>'
              }
              else {
                return row[this.field]
              }

            }
            //render: this.render_columns();
            //render: this.render_columns
          });
          if ( n === this.source.res.source_lang ){
            def = i;
          }
          i++;
        }
        r.push({
          ftitle: bbn._('Remove original expression'),
          buttons: this.buttons,
          width: 40,
          cls: 'bbn-c'
        })

        return r;
      },
      //contains original from po file and translations from dib
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
        })
        return res

      },
      //contains strings from original and translations string from po file
      mapData(){
        let res = [],
          source_lang = this.source.res.path_source_lang;
        this.source.res.strings.forEach( (obj, idx ) => {
          let ob = {};
          for (let prop in obj){
            ob['original_exp'] = this.source.res.strings[idx][source_lang].original;
            ob['id_exp'] = this.source.res.strings[idx][source_lang].id_exp;
            ob[prop] = this.source.res.strings[idx][prop].translations_po;
          }
          res.push(ob);
        })
        return res
      }
    },

    components:{
      'toolbar-strings-table': {
        template:'#toolbar-strings-table',
        props: ['source'],
        data(){
          return {
            hide_source_language: false
          }
        },
        methods: {
          //this_tab is a var declared at created of the tab
          generate(){
            return this_tab.generate();
          },
          find_strings(){
            return this_tab.find_strings();
          },
          remake_cache(){
            return this_tab.remake_cache();
          }
        },
        watch: {
          hide_source_language(val, oldVal){
            //get the index of the column of source language
            var idx = bbn.fn.search(this_tab.columns, 'field', this_tab.source.res.path_source_lang);
            bbn.fn.log(idx);
            if ( ( val === true ) && ( idx > -1) ){
              this_tab.columns[idx].hidden = true;
              this_tab.$forceUpdate()
              bbn.fn.log(this_tab.columns)
            }
            else if (( val === false )){
              this_tab.columns[idx].hidden = false;
              this_tab.$forceUpdate()
            }
          }
        },

      },
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