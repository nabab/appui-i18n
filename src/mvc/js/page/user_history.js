(() => {
  return {
    props: {
      source: {
        type: Object
      }
    },
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      };
    }
  }
})();
