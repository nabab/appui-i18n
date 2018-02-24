(() => {
  return {
    data(){
      return{
        source_glossary: this.source.source_glossary,
        pressedEnterKey: false,
      }
    },
    methods: {
      insert_translation(row,idx){
        //use a different controller
        bbn.fn.post('internationalization/actions/insert_translations',
          row, (success) => {
          if (success){
            appui.success('Translation saved');
            bbn.vue.find(this, 'bbn-table').updateData();
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      remake_cache(){
        bbn.fn.post('internationalization/actions/reload_cache', { id_option: this.source.id_option }, (d) => {
          if ( d.success ){
            bbn.fn.log()
            this.source.cached_model.res = d.res;
            this.$nextTick(() => {
              this.$refs.strings_table.updateData();
            });
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

        return r;
      },

    },
    watch: {
      'source_glossary': {
        deep: true,
        handler(val, oldval){
          if ( val ){
            $(".bbn-input input", this.$refs.strings_table.$el).off('keyup');
            $(".bbn-input input", this.$refs.strings_table.$el).on('keyup', (e) => {
              if ( e.keyCode === 13){
                this.pressedEnterKey = true;
                e.preventDefault();
              }
              else {
                this.pressedEnterKey = false;
              }
            })
          }
        }
      },

      pressedEnterKey(val){
        if ( val ){
          let editedRow = bbn.vue.find(this, 'bbn-table').editedRow;

          bbn.fn.post('internationalization/languages/insert_translation', { row: editedRow }, (d) => {
            if ( d.success ){
              bbn.fn.log('Expression successfully updated!');
            }
            else {
              bbn.fn.log('Something went wrong while updating this expression, please contact system\'s admin');
            }
          } );
        }
      }
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