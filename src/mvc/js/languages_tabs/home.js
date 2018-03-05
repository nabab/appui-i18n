(() => {
  return {
    props:['source'],
    methods: {
      //render for the third column of the projects table
      render_lang_name(row){
        return bbn.fn.get_field(this.source.primary, 'code', row.lang, 'text');
      },
      //render for the first column of the projects table
      render_projects(row){
        return '<a href="internationalization/components/languages/glossaries">'+ row.name +'</a>'
      },
      //render for the second column of the projects table
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
      cfg_languages(row, col, idx){
        $.each( this.source.primary, (a, z) => {
          if( $.inArray(z.id, row.langs) ){
            this.active = 1
          }
        });
        //edit projects' table using the form $options.components['appui-languages-form'] declared in the html of
        // the projects table
        return this.$refs.table1.edit(row,  {height: 600, width:500, title: bbn._("Config languages for the project")}, idx);
      },
    },
    components: {
      'appui-paths-table':{
        //expand the rows of projects table using the template contained in the folder templates
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
            //open the table of strings of this path combining new strings found in the files with strings present in db
            //send arguments[0] (id_option of the path) to 'internationalization/languages_tabs/path_translations/'
            this.id_option = row.id_option;
            //internationalization/languages_tabs/path_translations/ will return the cached_model in its data, if a
            // cached_model doesn't exist for this id_option it will be created
            bbn.fn.link('internationalization/languages_tabs/path_translations/' + row.id_option);
          },
          find_new_strings(row){
            //check in the path for new strings
            bbn.fn.post('internationalization/actions/find_strings', row, (d) => {
              //d.done is the number of new strings inserted in the db
              //the empty string passed to bbn.fn.alert is for the title of the alert
              if( d.done > 0 ){
                bbn.fn.alert( d.done + ' strings successfully updated for '+ row.text + '<i class="fa' +
                  ' fa-smile-o"></i>!', ' ');
              }
              else {
                bbn.fn.alert('There are no new strings to update <i class="fa fa-smile-o" aria-hidden="true"></i>', ' ');
              }
            });
          },
        },

      },
      //the component form to configure languages in which translate the project
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