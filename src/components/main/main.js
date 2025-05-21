(() => {
  bbn.cp.mixins.i18n = {
    data() {
      return {
        _mainPage: null,
        root: appui.plugins['appui-i18n'] + '/',
        baseURL: this.closest('bbn-router').fullBaseURL,
        isMobile: bbn.fn.isMobile(),
        isTablet: bbn.fn.isTabletDevice(),
        isDev: bbn.env.isDev,
      };
    },
    computed: {
      mainPage() {
        if (!this._mainPage) {
          this.updateInternalMainPage();

        }

        return this._mainPage;
      }
    },
    methods: {
      fdate: bbn.fn.fdate,
      fdatetime: bbn.fn.fdatetime,
      getRow: bbn.fn.getRow,
      getField: bbn.fn.getField,
      updateInternalMainPage() {
        this._mainPage = this.closest('appui-i18n-main');
      }
    }
  };
  let idx = bbn.fn.search(bbn.cp.knownPrefixes, 'prefix', 'appui-i18n-');
  if (idx > -1) {
    bbn.cp.knownPrefixes.splice(idx, 1);
  }

  bbn.cp.addUrlAsPrefix(
    'appui-i18n-',
    appui.plugins['appui-component'] + '/',
    [bbn.cp.mixins.basic, bbn.cp.mixins.i18n]
  );

  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      project: {
        type: String
      }
    },
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/',
        baseURL: this.closest('bbn-router').fullBaseURL,
        isLoading: true,
        data: {}
      }
    },
    methods: {
      loadData(){
        this.isLoading = true;
        bbn.fn.post(this.root + 'data/main', {
          project: this.project
        }, d => {
          if (d.success && d.data) {
            this.data = d.data;
          }

          this.isLoading = false;
        }, () => {
          appui.error();
        });
      }
    },
    created(){
      appui.register('appui-i18n' + (this.project ? '-' + this.project : ''), this);
    },
    mounted(){
      this.loadData();
    },
    beforeDestroy(){
      appui.unregister('appui-i18n' + (this.project ? '-' + this.project : ''));
    }
  }
})();