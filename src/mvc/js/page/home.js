(() => {
  return {
    props:['source'],
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    },
    methods: {
      //render for the third column of the projects table
      render_lang_name(row){
        return bbn.fn.getField(this.source.primary, 'text', 'code', row.lang);
      },
      //render for the first column of the projects table
      render_projects(row){
        return '<a href="' + this.root + 'components/languages/glossaries">'+ row.name +'</a>'
      },
      //render for the second column of the projects table
      render_langs(row){
        var st = '';
        if ( ( row.langs !== null ) && ( row.langs.length ) ){
          bbn.fn.each( row.langs, (v, i) => {
            st += bbn.fn.getField(this.source.primary, 'text', 'id', v) + '<br>'});
          return st;
        }
        else {
          st = 'Configure a language for this project';
        }
      },
      //cfg language for the project
      cfg_languages(row, col, idx){
        bbn.fn.each( this.source.primary, (z, a) => {
          //if( $.inArray(z.id, row.langs) ){
            //da vedere 
          if( row.langs.indexOf(z.id) !== 0 ){
            this.active = 1
          }
        });
        //edit projects' table using the form $options.components['appui-languages-form'] declared in the html of
        // the projects table
        return this.$refs.table1.edit(row,  {height: 600, width:500, label: bbn._("Config languages for the project")}, idx);
      },
    },
    components: {
      'paths-table':{
        //expand the rows of projects table using the template contained in the folder templates
        template:'#paths-table',
        props:['source'],
        data(){
          return{
            path: this.source.path,
            id_project: this.source.id,
            id_option: false,
            root: appui.plugins['appui-i18n'] + '/'
          };
        },
        methods: {
          open_strings_table(row){
            //open the table of strings of this path combining new strings found in the files with strings present in db
            //send arguments[0] (id_option of the path) to 'page/expressions/'
            this.id_option = row.id_option;
            //page/expressions/ will return the cached_model in its data, if a
            // cached_model doesn't exist for this id_option it will be created
            bbn.fn.link(this.root + 'page/expressions/' + row.id_option);
          },
          find_new_strings(row){
            //check in the path for new strings
            this.post(this.root + 'actions/find_strings', row, (d) => {
              //d.done is the number of new strings inserted in the db
              //the empty string passed to bbn.fn.alert is for the title of the alert
              if( d.done > 0 ){
                appui.alert(d.done + ' ' + bbn._("strings successfully updated for") + ' ' + row.text + '<i class="nf nf-oct-smiley"></i>!', ' ');
              }
              else {
                appui.alert(bbn._('There are no new strings to update') + ' <i class="nf nf-fa-smile" aria-hidden="true"></i>', ' ');
              }
            });
          },
        },

      },
      //the component form to configure languages in which translate the project
      'languages-form': {
        template: '#languages-form',
        data(){
          return {
            root: appui.plugins['appui-i18n'] + '/'
          };
        },
        methods:{
          //inArray: $.inArray,
          change_checked_langs(val, obj){
            let form = this.find('bbn-form'),
            //idx =  $.inArray(obj.id, this.source.row.langs);
            idx = this.source.row.langs.indexOf(obj.id);
            bbn.fn.log('++++++++++++', obj.id, idx)
            if ( idx > -1 ){
              this.source.row.langs.splice(idx, 1);

            }
            else {
              this.source.row.langs.push(obj.id);
              //this.closest('bbns-container').getComponent().$forceUpdate();
            }
          }
        },
        props: ['source'],
      }
    }
  }

})();
