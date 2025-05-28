//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: ['source'],
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    },
    methods: {
      buttons(){
        return [{
          action: this.deleteExpression,
          icon: 'nf nf-fa-times',
          label: bbn._('Delete original expression')
        }];
      },
      deleteExpression(row){
        this.confirm(
          bbn._('Do you really want to delete the original expression and it\'s translation?'),
          () => {
            this.post(this.root + 'actions/delete_expression', {
              id_exp: row.idExp,
              exp: row.original_exp
            }, d => {
              this.getRef('table').removeItem(row);
            });
          }
        );
      },
      insertTranslation(row){
        if (row?.translation
          && row?.idExp
        ) {
          this.post(this.root + 'actions/insert_translation', {
            'id_exp' : row.idExp,
            'expression': row.translation,
            'lang': this.source.translation_lang
          }, d => {
            if (d.success){
              appui.success('Translation saved');
            }
            else{
              appui.error('An error occurred while saving translation');
            }
          });
        }
      },
    }
  }
})();