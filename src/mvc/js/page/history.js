(() => {
  return {
    props: ['source'],
    data(){
      return {
        //primary is used to render the name of languages in the table
        primary : bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary,
      }
    },
    computed: {

    },
    methods: {
      renderUser(row){
        return appui.app.getUserName(row.user)
      },
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
        if ( ( row.original_lang === row.translation_lang ) && ( row.expression === row.original_exp ) ){
          st += '<i class="nf nf-fa-check bbn-bg-purple bbn-xl" style="color:white" title="Expressions are identical"></i>'
        }
        else if ( ( row.original_lang === row.translation_lang ) && ( row.expression !== row.original_exp ) ){
          st += '<i class="zmdi zmdi-alert-triangle bbn-xl bbn-bg-orange" title="Expression changed in its' +
            ' original language">' +
            ' </i>'
        }
        else if ( ( row.original_lang !== row.translation_lang ) && ( row.expression !== row.original_exp ) ){
          st += '<i class="nf nf-oct-smiley bbn-bg-green bbn-xl" title="Expression translated"></i>'
        }
        else if ( ( row.original_lang !== row.translation_lang ) && ( row.expression === row.original_exp ) ){
          st += '<i title="Translated! Expression is the same in the two languages" class="nf nf-oct-smiley bbn-green bbn-xl"></i>'
        }
        return st;
      },
      render_lang(row){
        let st = '';
        if ( row.translation_lang === 'fr' ){
          st += '<img class="flag" style="width:25px;height:15px" src="https://lipis.github.io/flag-icon-css/flags/4x3/fr.svg" alt="France Flag">'
        }
        else if ( row.translation_lang === 'en' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'de' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/de.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'es' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/es.svg" alt="United Kingdom Flag">'
        }
        else if ( row.translation_lang === 'it' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/it.svg" alt="United Kingdom Flag">'
        }
        return st;
        /*let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.translation_lang , 'text')
        return st;*/
      },
      render_original_lang(row){
        let st = '';
        if ( row.original_lang === 'fr' ){
          st += '<img class="flag" style="width:25px;height:15px" src="https://lipis.github.io/flag-icon-css/flags/4x3/fr.svg" alt="France Flag">'
        }
        else if ( row.original_lang === 'en' ){
          st += '<img class="flag" style="width:30px;height:20px" src="https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg" alt="United Kingdom Flag">'
        }
        return st;
        /*let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.original_lang , 'text')
        return st;*/
      },
    },
  }
})();
