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
      configured_langs(){
        let r = [],
            i = 0,
            def = null;
        for ( let n in this.source.langs ){
          r.push({
            field: n,
            title: this.source.langs[n].text,
            fixed: n === this.source.source_lang
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
    }
  }
})();