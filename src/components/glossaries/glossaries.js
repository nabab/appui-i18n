//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    props: ['source'],
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    },
    computed: {
      primary(){
        return this.closest('bbn-router').$parent.source.primary;
      }
    },
    methods: {
      render_user(row){
        return appui.app.getUserName(row.id_user) || 'User not found';
      },
      buttons(){
        let res = [];
        res.push({
          action: this.delete_expression,
          icon: 'nf nf-fa-times',
          title :'Delete original expression'
        })
        return res;
      },
      delete_expression(row){
        this.getPopup().confirm(
          bbn._('Do you really want to delete the original expression and it\'s translation?'),
          () => {
            this.post(
              this.root + 'actions/delete_expression',
              {id_exp: row.idExp, exp: row.original_exp},
              (d) => {
                this.$refs.glossary_table.remove(row);
              }
            );
          }
        );
      },
      insert_translation(row,idx){
        this.post(
          this.root + 'actions/insert_translation',
          {
            'id_exp' : row.idExp,
            'expression': row.translation,
            'translation_lang': this.source.translation_lang
          },
          (success) => {
            if (success){
              appui.success('Translation saved');
            }
            else{
              appui.error('An error occurred while saving translation');
            }
          }
        );
      },
      icons(row){
        let res = '';
        if ( ( row.original_expression === row.translation ) && ( this.source.source_lang === this.source.translation_lang ) ){
          res = ('<i class="nf nf-fa-check bbn-purple bbn-xl" title="Expressions are identical" ></i>')
        }
        else if ( ( row.translation !== null ) && ( row.translation !== row.original_expression ) && ( this.source.source_lang === this.source.translation_lang ) ){
          res= ('<i class="nf nf-fa-exclamation_triangle bbn-xl bbn-bg-orange" title="Expression changed in its original' +
            ' language" ></i>')
        }
        else if ( ( row.translation !== null ) && ( row.original_expression !== row.translation ) && ( this.source.source_lang !== this.source.translation_lang ) ) {
          res = ('<i class="nf nf-oct-smiley bbn-xl bbn-green bbn-xl" title="Expression translated" ></i>')

        }
        else {
          res = '<i class="nf nf-fa-frown bbn-xl bbn-red" title="Expression not translated."' +
            ' ></i>'
        }
        return res;
      },

    },
    components:{
      'delete_button': {
        template: '<bbn-button icon="nf nf-fa-frown"></bbn-button>',
        props: ['source']
      }
    }
  }
})();