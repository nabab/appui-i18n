(() => {
  return {
    data(){
      return{}
    },  
    methods: {
      open_untranslated(){
        bbn.fn.link(bbn.vue.closest(this, 'bbn-tabnav').baseURL + 'untranslated');
      },
      edit_translation(){
        alert('edit row')
      }
    },
  }
})();
