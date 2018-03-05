(() => {
  return {
    data(){
      return{
        source_glossary: this.source.source_glossary,
        pressedEnterKey: false,
      }
    },
    methods: {
      buttons(){
        let res = [];
        res.push({
          command: this.delete_expression,
          icon: 'fa fa-close',
          title: 'Delete original expression'
        })
        return res;
      },
      delete_expression(row){
        bbn.fn.log(row)
        let id_exp = row.id_exp,
            data = this.source.cached_model.res,
            idx = bbn.fn.search(data, { id_exp: id_exp });
        bbn.fn.confirm('Did you remove the the expression from code before to delete the row?', () => {
          bbn.fn.post('internationalization/actions/delete_expression', { id_exp: row.id_exp, exp: row.original_exp },  (d) => {
            if ( d.success ){
              data.splice(idx, 1);
              this.$refs.strings_table.updateData();
              appui.success('Expression deleted');
              this.remake_cache()
            }
            else{
              appui.error('An error occurred while deleting the expression');
            }
          } );
        })
      },
      insert_translation(row,idx){
        //use a different controller
        bbn.fn.post('internationalization/actions/insert_translations', {
          row: row,
          langs: this.source.langs
          }, (d) => {
          if (d.success){
            appui.success('Translation saved');
            this.$refs.strings_table.updateData();
            this.remake_cache();
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      remake_cache(){
        bbn.fn.post('internationalization/actions/reload_cache', { id_option: this.source.id_option }, (d) => {
          if ( d.success ){
            this.source.cached_model.res = d.cached_model.res;
            this.$refs.strings_table.updateData();
          }
        })
      },
      render_first_col(row){
        let st = '';
        if ( row[this.source.source_lang] !== row.original_exp ){
          st += row[this.source.source_lang] + '<i class="zmdi zmdi-alert-triangle bbn-s bbn-orange" style="float:right" title="Expression changed in its original language"></i>'
        }
        else{
          st = row[this.source.source_lang]
        }
        return st;
      },
      render_original_exp(row){
        return bbn.fn.get_field(this.source.langs[this.source.source_lang], 'code', row[this.source.source_lang], 'text')
      },
    },
    props: ['source'],
    computed: {
    	configured_langs(){
        let r = [],
            i = 0,
            def = null;
        for ( let n in this.source.langs ){
          r.push({
            field: n,
            title: this.source.langs[n].text,
            fixed: n === this.source.source_lang,
            editable: true
          });
          if ( n === this.source.source_lang ){
            def = i;
            r[i].render = this.render_first_col
          }
          i++;
        }
        if ( def ){
          r.splice(0, 0, r.splice(def, 1)[0]);
        }
        r.push({
          ftitle: 'Remove original expression',
          buttons: this.buttons,
          width: 40,
          cls: 'bbn-c'
        })

        return r;
      },

    },
    watch: {

    },
    components: {
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
        '<span class="bbn-l">File:</span>' +
        '<a v-text="s" @click="link_ide(s)" style="width:100%;cursor:pointer" title="Open the file in i.d.e"></a>' +
        ' </li>' +
        '</ul>',
        props: ['source'],
      },
    }
  }
})();