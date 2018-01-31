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
      //original expression
      render_original_lang(row){
        if ( row.lang === row.original_lang ){
          return '<span style="color:grey; opacity:0.4">'+ this.source.lang_name +' is the original language of this expression</span>'
        }
        else {
          return bbn.fn.get_field(this.primary, 'code', row.original_lang, 'text');
        }
      },
      render_user(row){
        if (row.user){
          return row.user;
        }
        else {
          return '<span style="color:grey; opacity:0.4">No username</span>'
        }
      }
    }
  }
})();