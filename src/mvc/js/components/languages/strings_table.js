(() => {
  return {
    data(){
      return {
        primary: this.languages.source.primary
      }
    },
    methods: {
      mapData(row){
        if ( row.translation ){
          row =  $.extend(row.translation, {
            id_exp: row.id_exp,
            original_exp: row.original_exp,
            path: row.path,
          })
        }
        return row;
      },
      generate(){
        bbn.fn.post(this.source.root + 'actions/generate', {id: this.source.id_option}, (d) => {
          bbn.fn.log(d);
        })
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
            data = this.source.res,
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
          langs: this.source.languages,
          id_option: this.source.id_option
          }, (d) => {
          if (d.success){
            appui.success('Translation saved');
            row = d.row;
            let table = bbn.vue.find(this, 'bbn-table');
            this.source.res[idx] = d.row
            //table.currentData[idx] = d.row;
            table.updateData();
            //this.remake_cache();
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      remake_cache(){
        bbn.fn.post('internationalization/actions/reload_cache', { id_option: this.source.id_option }, (d) => {
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
            this.$nextTick( () => {
              bbn.vue.find(this, 'bbn-table').updateData();
            } )
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
    	  var field = '';
        for ( let n in this.source.languages ){
          r.push({
            field: this.source.languages[n],
            title:  ( this.source.languages[n] === this.source.source_lang) ? (bbn.fn.get_field(this.primary, 'code', this.source.languages[n], 'text') + '  <i class="fa fa-asterisk" title="This is the original language of the expression"></i>') : bbn.fn.get_field(this.primary, 'code', this.source.languages[n], 'text'),
            //fixed: n === this.source.source_lang,
            editable: true,
            //render: this.render_columns
          });
          if ( n === this.source.source_lang ){
            def = i;
            //r[i].title = 'Original Exp'
          }
          i++;
        }
        if ( def ){
        //  r.splice(0, 0, r.splice(def, 1)[0]);
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
    mounted(){
      //this.remake_cache();
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
        '<span class="bbn-lg">File:</span>' +
        '<a v-text="s" @click="link_ide(s)" style="width:100%;cursor:pointer" title="Open the file in i.d.e"></a>' +
        ' </li>' +
        '</ul>',
        props: ['source'],
      },
    }
  }
})();