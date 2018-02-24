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
      delete_expression(){
        alert('deleted')
      },
      insert_translation(row,idx){
        bbn.fn.post('internationalization/actions/insert_translation',
          {
            'id_exp' : row.id,
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
      buttons(row){
        let res = [];
        if ( ( row.original_expression === row.translation ) && ( this.source.source_lang === this.source.translation_lang ) ) {
          res.push({
            icon: 'fa fa-check',
            disabled: true,
            title: "Expressions are identical"
          });
        }
        else if ( ( row.translation !== null ) && ( row.translation !== row.original_expression ) && ( this.source.source_lang === this.source.translation_lang ) ){
          res.push({
            icon: 'zmdi zmdi-alert-triangle ',
            disabled: true,
            title: "Expression changed in its original language"
          });
        }
        else if ( ( row.translation !== null ) && ( row.original_expression !== row.translation ) && ( this.source.source_lang !== this.source.translation_lang ) ) {
          res.push({
            icon: 'fa fa-smile-o',
            disabled: true,
            title: "Expression translated"
          })
        }
        else {
          res.push({
            icon: 'fa fa-frown-o ',
            command: this.delete_expression,
            title: "Expression not translated. Click to remove the original"
          })

        }
        return res;
      },
      render_user(row){
        if (row.user){
          return row.user;
        }
        else {
          return '<span class="bbn-i" style="color:grey; opacity:0.4">Unknown user</span>'
        }
      }
    },
    components:{
      'delete_button': {
        template: '<bbn-button icon="fa fa-frown-o"></bbn-button>',
        props: ['source'],
        data(){
          return {
          }
        },
      }
    }
  }
})();