(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Array
      }
    },
    data(){
      return {
        formSource: {
          sourceLang: '',
          translationLang: ''
        }
      }
    },
    methods: {
      link(){
        bbn.fn.link(this.baseURL + 'glossary/' + this.formSource.sourceLang + '/' + this.formSource.translationLang);
      },
      cancel(){
        this.getPopup().close();
      },
    }
  }
})();