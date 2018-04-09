(() => {
  return {
    props:['source'],
    computed: {
      widgets(){
        var res = [];
        if (this.source.data){
          this.source.data.forEach( (v, i) => {
            var disabled =
            res.push({
              title: v.text,
              key: v.id,
              component : 'appui-languages-widget',
              id_project: this.id_project,
              buttonsRight: [{
                text: bbn._('Update widget data'),
                icon: 'fa fa-retweet',
                action: 'remake_cache'
              },{
                text: bbn._('Create and delete files of translation'),
                icon: 'fa fa-flag',
                action: 'config_locale_dir'
              },{
                text: bbn._('Open the table of strings of this path'),
                icon: 'fa fa-book',
                action: 'open_strings_table'
              }]
            })
          })
          return res;
        }

      },

      //the source of projects' dropdown
      dd_projects(){
        let res = [];
        $.map(this.source.projects, (v, i) => {
          res.push({value: v.id, text: v.name})
        })
        return res;
      },
    },
    mounted(){
      //if I want widgets before to select the dropdown
      //this.load_widgets()
    },
    methods: {
      //open the table of projects
      link_projects_table(){
        bbn.fn.link(this.source.root + 'page/home');
      },
      cfg_project_languages(){

        bbn.vue.closest(this, 'bbn-tab').popup().open({
          width: 600,
          height: 500,
          title: bbn._("Config translation languages for the project"),
          component: this.$options.components['appui-languages-form'],
          //send the configured langs for this id_project
          source: {
            data: {
              primary: this.source.primary
            },
            row: {
              configured_langs: this.source.configured_langs,
              id: this.id_project
            }
          }
        })
      },
      get_field: bbn.fn.get_field,
      load_widgets(){
        if ( this.id_project ){
          this.source.data = [];
            bbn.fn.post('internationalization/page/dashboard', { id_project: this.id_project }, (d) => {
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
            let form = bbn.vue.find(this, 'bbn-form'),
                idx =  $.inArray(obj.id, this.source.row.configured_langs);

            if ( idx > -1 ){
              bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs.splice(idx, 1);
              bbn.vue.closest(this, 'bbn-tab').getComponent().$forceUpdate();
            }
            else {
              bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs.push(obj.id)
              bbn.vue.closest(this, 'bbn-tab').getComponent().$forceUpdate();
            }
          }
        },
        props: ['source'],
      }
    }
  }

})();