(() => {
  return {
    props: {
      source: {
        type: Object,
        default(){
          return {}
        }
      },
      code: {
        type: String
      },
      text: {
        type: String
      },
      flag: {
        type: Boolean,
        default: true
      },
      onlyFlag: {
        type: Boolean,
        default: false
      }
    },
    data(){
      return {
        mainPage: appui.getRegistered('appui-i18n'),
        githubFlagsUrl: 'https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/',
        padded: false
      }
    },
    computed: {
      currentCode(){
        return this.code || this.source.code;
      },
      currentFlag(){
        if (!this.currentCode || !this.flag) {
          return '';
        }
        let code = this.currentCode;
        if (code === 'en') {
          code = 'gb';
        }
        return this.githubFlagsUrl + code + '.svg';
      },
      currentText(){
        if (this.text && this.text.length) {
          return this.text;
        }
        if (this.source.text && this.source.text.length) {
          return this.source.text;
        }
        if (this.mainPage
          && this.mainPage.source
          && this.mainPage.source.primary
          && this.code
        ) {
          return bbn.fn.getField(this.mainPage.source.primary, 'text', 'code', this.code);
        }
        return '';
      }
    },
    created(){
      if (this.closest('bbn-dropdown')) {
        this.padded = true;
      }
    }
  }
})();