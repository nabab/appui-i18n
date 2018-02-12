
(() => {
  return {
    props:['source'],
    created(){
      this.run_script = this.$options.components['appui-paths-table'].methods['run_script'];
      this.open_strings_table = this.$options.components['appui-paths-table'].methods['open_roots_table'];
      //this.path_code = this.$options.components['appui-paths-table'].methods['path_code'];

      //methods of toolbar component
      this.open_user_history = this.$options.components['toolbar'].methods['open_user_history '];
      this.open_complete_history = this.$options.components['toolbar'].methods['open_complete_history'];
      this.open_statistic_list = this.$options.components['toolbar'].methods['open_statistic_list'];
    },
    methods: {
      render_lang_name(row){
        return bbn.fn.get_field(this.source.primary, 'code', row.lang, 'text');
      },
      render_projects(row){
        return '<a href="internationalization/components/languages/glossaries">'+ row.name +'</a>'
      },
      render_langs(row){
        var st = '';
        if ( ( row.langs !== null ) && ( row.langs.length ) ){
          $.each( row.langs, (i, v) => {
            st += bbn.fn.get_field(this.source.primary, 'id', v, 'text') + '<br>'});

          return st;
        }
        else {
          st = 'Configure a language for this project';
        }
      },
      //cfg language for the project
      cfg_languages(row){
        $.each( this.source.primary, (a, z) => {
          if( $.inArray(z.id, row.langs) ){
            this.active = 1
          }
        });
        return this.$refs.table1.edit(row, bbn._("Config languages for the project"));
      },
    },
    components: {
      //the toolbar of the table
      'toolbar': {
        template:'#toolbar',
        props:['source'],
        data(){
          return{
            lang:  false,
            langs: bbn.vue.closest(this, 'bbn-table').$parent.source['langs_in_db'],
            primary: bbn.vue.closest(this, 'bbn-table').$parent.source['primary'],
          }
        },
        computed: {
          //the source of dropdown
          dd_source(){
            let res = [];
            $.each(this.langs, (i, v) => {
              res.push({value:v, text: bbn.fn.get_field(this.primary, 'code', v, 'text') })
            })
            return res;
          },
        },
        methods: {
          open_user_history(){
            bbn.fn.link('internationalization/languages/user_history');
          },
          open_complete_history(){
            bbn.fn.link('internationalization/languages/complete_history');
          },
          open_statistic_list(){
            bbn.fn.link('internationalization/languages/statistic_tab');
          },
        },
        watch: {
          lang(val){
            if (val){
              //link to open glossary table of each language in db after the selection of dropdown in the toolbar
              bbn.fn.link('internationalization/languages/glossary_tab/' + val
              );
            }
          }
        }
      },
      'appui-paths-table':{
        //expander
        template:'#appui-paths-table',
        props:['source'],
        data(){
          return{
            path: this.source.path,
            id_project: this.source.id,
            id_option: false
          }
        },
        methods: {
          open_strings_table(row){
            //the table's source will take id_option as arguments[0]
            this.id_option = row.id_option;
            bbn.fn.link('internationalization/languages/strings_tab/' + row.id_option );
          },
          run_script(row){
            bbn.fn.post('internationalization/find_strings', row, (d) => {
              if( d.done > 0 ){
                bbn.fn.alert(d['todo'].length + ' strings successfully updated for '+ row.text + '<i class="fa' +
                  ' fa-smile-o"></i>!');
              }
              else {
                bbn.fn.alert('There are no new strings to update <i class="fa fa-frown-o" aria-hidden="true"></i>');
              }
            });
          },
        },

      },

      'appui-languages-form': {
        template: '#appui-languages-form',
        methods:{
          inArray: $.inArray,
          change_checked_langs(val, obj){
            let form = bbn.vue.find(this, 'bbn-form'),
              checkboxes = bbn.vue.findAll(this, 'bbn-checkbox'),
              idx =  $.inArray(obj.id, this.source.row.langs);
            if( idx > -1 ){
              this.source.row.langs.splice(idx, 1)
            }
            else{
              this.source.row.langs.push(obj.id)
            }
          }
        },
        props: ['source'],
      }
    }
  }

})();
