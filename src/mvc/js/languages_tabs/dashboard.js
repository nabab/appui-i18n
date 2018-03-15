(() => {
  return {
    props:['source'],
    computed: {
      langs(){
        let res = [];
        if ( this.source.configured_langs ){
          Object.values(this.source.configured_langs).forEach( (v, i) => {
            res.push(v.id)
          });
        }
        return res;

      },
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
      cfg_project_languages(){

        bbn.vue.closest(this, 'bbn-tab').popup().open({
          width: 600,
          height: 500,
          title: bbn._("Config translation languages for the project"),
          component: this.$options.components['appui-languages-form'],
          //send the configured langs for this id_project
          source: { data: { primary: this.source.primary }, row: { langs: this.langs, id: this.id_project } }
        })
      },
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
    },
    components: {
      'appui-languages-form': {
        template: '#appui-languages-form',
        methods:{
          inArray: $.inArray,
          change_checked_langs(val, obj){
            bbn.fn.log(JSON.stringify(bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs))
            let form = bbn.vue.find(this, 'bbn-form'),
                idx =  $.inArray(obj.id, this.source.row.langs);

            if ( idx > -1 ){
              bbn.vue.closest(this, 'bbn-tab').getComponent().langs.splice(idx, 1);
              bbn.vue.closest(this, 'bbn-tab').getComponent().$forceUpdate();
            }
            else {
              bbn.vue.closest(this, 'bbn-tab').getComponent().langs.push(obj.id)
              bbn.vue.closest(this, 'bbn-tab').getComponent().$forceUpdate();
            }
          }
        },
        props: ['source'],
      }
    }
  }

})();