(() => {
  return {
    data(){
      return {
        isOptionsProject: this.source.id === 'options'
      }
    },
    computed: {
      localeDirs(){
        return this.source?.locale_dirs || [];
      },
      data(){
        if (this.source?.language && this.source?.result) {
          let result = bbn.fn.clone(this.source.result, true);
          bbn.fn.iterate(result, (r, l) => {
            r.class = '';
            r.class_db = '';
            if (r.num_translations >= 0) {
              r.val = !r.num ? 0 : r.num_translations/r.num * 100
              /** the css class for progress bar */
              if ((r.val >= 0) && (r.val <= 30)) {
                r.class = 'low'
              }
              else if ((r.val > 30) && (r.val <= 70)) {
                r.class = 'medium'
              }
              else if ((r.val > 70) && (r.val <= 100)) {
                r.class = 'high'
              }
            }

          });

          return result;
        }

        return [];
      },configuredLangs(){
        return this.source?.languages || [];
      }
    },
    methods: {
      normalize(val){
        return val && (val > 0) ? parseFloat(val.toFixed(2)) : 0;
      }
    }
  }
})();