//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    data(){
      return{
        source_glossary: this.source.source_glossary,
        pressedEnterKey: false,
      }
    },
    props: ['source'],
    computed: {
      first_column_title(){
        return 'Source language for is ' + this.source.source_lang
      },
    	configured_langs(){
        let r = [],
            i = 0,
            def = null;
        for ( let n in this.source.langs ){
          r.push({
            field: n,
            title: this.source.langs[n].text,
            fixed: n === this.source.source_lang,
            editable: true,
          });
          if ( n === this.source.source_lang ){
            def = i;
          }
          i++;
        }
        if ( def ){
          r.splice(0, 0, r.splice(def, 1)[0]);
        }
        return r;
      }
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
    }
  }
})();