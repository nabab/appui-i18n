(() => {
  return {
    props: ['source'],
    data(){
      return {
        //primary is used to render the name of languages in the table
        primary: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary,
        ready: false,

      }
    },
    methods: {
      insert_translation(row,idx){
        bbn.fn.post('internationalization/actions/insert_translation', row, (success) => {
          if (success){
            appui.success('Translation saved');
          }
          else{
            appui.error('An error occurred while saving translation');
          }
        });
      },
      render_status(row){
        let st = '';
        if ( ( row.original_lang === row.translation_lang ) && ( row.expression === row.original_expression ) ){
          st += '<i class="fa fa-check bbn-bg-purple bbn-xl" style="color:white" title="Expressions are identical"></i>'
        }
        else if ( ( row.original_lang === row.translation_lang ) && ( row.expression !== row.original_expression ) ){
          st += '<i class="zmdi zmdi-alert-triangle bbn-xl bbn-bg-orange" title="Expression changed in its' +
            ' original language">' +
            ' </i>'
        }
        else if ( ( row.original_lang !== row.translation_lang ) && ( row.expression !== row.original_expression ) ){
          st += '<i class="fa fa-smile-o bbn-bg-green bbn-xl" title="Expression translated"></i>'
        }
        return st;
      },
      render_lang(row){
        let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.translation_lang , 'text')
        return st;
      },
      render_original_lang(row){
        let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.original_lang , 'text')
        return st;
      },
    },
  }
})();