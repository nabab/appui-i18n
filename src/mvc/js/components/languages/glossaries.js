//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    props: ['source'],

    computed: {
      primary(){
        return this.languages.$props.source['primary'];
      }
    },
    methods: {
      buttons(){
        let res = [];
        res.push({
          command: this.delete_expression,
          icon: 'fa fa-close',
          title :'Delete original expression'
        })
        return res;
      },
      delete_expression(row){
        bbn.fn.confirm('Do you really want to delete the original expression and it\'s translation?', () => {
          bbn.fn.post('internationalization/actions/delete_expression', { id_exp: row.idExp, exp: row.original_exp },  (d) => {
            this.$refs.glossary_table.remove(row)
          } );
        })
      },
      insert_translation(row,idx){
        bbn.fn.post('internationalization/actions/insert_translation',
          {
            'id_exp' : row.idExp,
            'expression': row.translation,
            'translation_lang': this.source.translation_lang
          }, (success) => {
          if (success){
            appui.success('Translation saved');
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      icons(row){
        let res = '';
        if ( ( row.original_expression === row.translation ) && ( this.source.source_lang === this.source.translation_lang ) ){
          res = ('<i class="fa fa-check bbn-bg-purple bbn-xl" title="Expressions are identical" ></i>')
        }
        else if ( ( row.translation !== null ) && ( row.translation !== row.original_expression ) && ( this.source.source_lang === this.source.translation_lang ) ){
          res= ('<i class="zmdi zmdi-alert-triangle bbn-xl bbn-bg-orange" title="Expression changed in its original' +
            ' language" ></i>')
        }
        else if ( ( row.translation !== null ) && ( row.original_expression !== row.translation ) && ( this.source.source_lang !== this.source.translation_lang ) ) {
          res = ('<i class="fa fa-smile-o bbn-xl bbn-bg-green bbn-xl" title="Expression translated" ></i>')

        }
        else {
          res = '<i class="fa fa-frown-o bbn-xl bbn-bg-red" title="Expression not translated."' +
            ' ></i>'
        }
        return res;
      },

    },
    components:{
      'delete_button': {
        template: '<bbn-button icon="fa fa-frown-o"></bbn-button>',
        props: ['source']
      }
    }
  }
})();