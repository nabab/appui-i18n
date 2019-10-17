(() => {

  return {
    props:['source'],
    data(){
      return {
        id_project: null,
        primary: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary

      }
    },
    
    computed: {
      widgets(){
        let res = [],
          buttons;
        if (this.source.data){
          if (this.id_project !== 'options'){
            buttons = [{
              text: bbn._('Update widget data'),
              icon: 'nf nf-fa-retweet',
              action: 'remake_cache'
            },{
              text: bbn._('Setup languages'),
              icon: 'nf nf-fa-flag',
              action: 'generate'
            },{
              text: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }, {
              text: bbn._('Delete locale folder'),
              icon: 'nf nf-fa-trash',
              action: 'delete_locale_folder',
            }]
          }
          else {
            buttons = [{
              text: bbn._('Update widget data'),
              icon: 'nf nf-fa-retweet',
              action: 'remake_cache'
            },{
              text: bbn._('Find new options or translations for this category'),
              icon: 'icon-database',
              action: 'find_options'
            },{
              text: bbn._('Open the table of strings of this path'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }]
          }
          this.source.data.forEach( (v, i) => {
            if ( v.id ){
              res.push({
                title: v.text,
                key: v.id,
                component : 'appui-i18n-widget',
                id_project: this.id_project,
                buttonsRight: buttons
              })
            }
          })
          return res;
        }

      },
    },
    mounted(){
      this.id_project = this.closest('bbn-tabnav').currentURL.split('/')[1]
      this.closest('bbn-tabnav').$parent.configured_langs = this.source.configured_langs;
    },
  }

})();
