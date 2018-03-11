(() => {
  return {
    props:['source'],
    computed: {
      //the source of projects' dropdown
      dd_projects(){
        let res = [];
        $.map(this.source.projects, (v, i) => {
          res.push({value: v.id, text: v.name})
        })
        return res;
      },
      widgets(){
        return this.source.data
      }
    },
    mounted(){
      //if I want widgets before to select the dropdown
      this.load_widgets()
    },
    methods: {
      get_field: bbn.fn.get_field,
      load_widgets(){
        if ( this.id_project ){
          this.source.data = [];
            bbn.fn.post('internationalization/languages_tabs/dashboard', { id_project: this.id_project }, (d) => {
          if ( d.data.success ){
              this.source.data = d.data.data;
              this.source.configured_langs = d.data.configured_langs;
            }
          });
        }
      }
    },
    data(){
      return {
        id_project: null,

      }
    }
  }

})();