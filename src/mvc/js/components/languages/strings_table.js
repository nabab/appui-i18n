//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    props: ['source'],
    methods: {

    },
    computed: {
      first_column_title(){
        return 'Source language for ' + this.source.this_path + ' is ' + this.source.source_lang
      },
      languages(){
        let langs = this.source.configured_langs,
          res = [],
          a = {};
        if ( langs.length ){
          $.each( langs, (i, v) => {
            a.title = v;
            res.push(a);
          });
        }
        return res;
      }
    }
  }
})();