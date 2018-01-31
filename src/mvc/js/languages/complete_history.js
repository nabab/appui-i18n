(() => {
  return {
    props: ['source'],
    data(){
      return {
        //primary is used to render the name of languages in the table
        primary : bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary
      }
    },
    methods: {
      render_empty_expression(row){
        return row.expression.length ? row.expression : '<span style="color:grey; opacity:0.4"> This expression has' +
          ' been' +
          ' deleted' +
          ' from db </span>'
      },
      render_lang(row){
        let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.lang , 'text')
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