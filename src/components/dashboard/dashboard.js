(() => {
  let mixins = [bbn.cp.mixins.i18n, {
    data() {
      return {
        _dashboard: null,
      };
    },
    computed: {
      dashboard(){
        if (!this._dashboard) {
          this.updateInternalDashboard();

        }

        return this._dashboard;
      },
      idProject(){
        return this.dashboard?.idProject;
      },
      projectName(){
        return this.dashboard?.currentProject?.name || '';
      },
      projectCode(){
        return this.dashboard?.currentProject?.code || '';
      },
      primary(){
        return this.dashboard?.primary;
      },
      configuredLangs(){
        return this.dashboard?.configuredLangs;
      },
      isOptionsProject(){
        return this.dashboard?.isOptionsProject;
      }
    },
    methods: {
      updateInternalDashboard() {
        this._dashboard = this.closest('bbn-container').find('appui-i18n-dashboard');
      }
    }
  }];
  bbn.cp.addUrlAsPrefix(
    'appui-i18n-dashboard-',
    appui.plugins['appui-component'] + '/',
    mixins
  );

  return {
    props: {
      source: {
        type: Object
      }
    },
    data(){
      const idProject = this.source?.id_project || (this.source.projects?.length ? bbn.fn.getField(this.source.projects, 'id', 'name', bbn.env.appName) : '');
      return {
        isLoading: true,
        idProject,
        primary: this.source.primary,
        optionsRoot: appui.plugins['appui-option'] + '/',
        configuredLangs: [],
        data: []
      }
    },
    computed: {
      isOptionsProject(){
        return this.idProject === 'options';
      },
      currentProject(){
        return this.idProject && this.source?.projects?.length ?
          bbn.fn.getRow(this.source.projects, 'id', this.idProject) :
          {};
      },
      currentProjectLanguage: {
        get(){
          return this.currentProject?.lang || '';
        },
        set(val){
          if (val
            && this.idProject
            && this.currentProject
            && (val !== this.currentProject.lang)
          ) {
            const current = this.currentProject.lang;
            this.currentProject.lang = val;
            this.isLoading = true;
            this.post(this.root + 'actions/project_lang', {
              id_project: this.idProject,
              language: val
            }, d => {
              if (d.success) {
                appui.success(bbn._('Project source language successfully updated'))
              }
              else {
                this.currentProject.lang = current;
                appui.error(bbn._('Project source language not updated'));
              }

              this.isLoading = false;
            }, () => {
              this.isLoading = false;
            });
          }
        }
      },
      languageText(){
        if (this.primary) {
          return bbn.fn.getField(this.primary, 'text', 'code', this.language);
        }
        return null;
      },
      widgets(){
        let res = [];
        if (this.data?.length){
          let buttons = [{
            label: bbn._('Update widget data'),
            icon: 'nf nf-fa-retweet',
            action: 'remakeCache'
          }, (this.isOptionsProject ? {
            label: bbn._('Create translation files'),
            icon: 'nf nf-md-file_replace_outline',
            action: 'generateFiles',
          } : {
            label: bbn._('Setup languages'),
            icon: 'nf nf-fa-flag',
            action: 'generate'
          }), {
            label: bbn._('Open the table of strings'),
            icon: 'nf nf-fa-book',
            action: 'openStringsTable',
          }, {
            label: bbn._('Open the translations form'),
            icon: 'nf nf-md-translate',
            action: 'openTranslationsForm',
          }];
          if (!this.isOptionsProject) {
            buttons.push({
              label: bbn._('Delete locale folder'),
              icon: 'nf nf-fa-trash',
              action: 'deleteLocaleFolder',
            });
          }

          bbn.fn.each(this.data, v => {
            if (v.id || v.code) {
              res.push({
                label: v.title + (this.isOptionsProject ? ` (${v.code})` : ''),
                key: v.id || v.code,
                component : 'appui-i18n-dashboard-widget',
                buttonsRight: buttons,
                options: {
                  idProject: this.source?.id_project,
                  source: v
                }
              });
            }
          });

          return res;
        }

      },
      //source to choose the translation language using the popup
      dd_translation_lang(){
        let res = [];
        bbn.fn.each(this.primary, (v, i) => {
          res.push({text: v.text, value: v.code })
        })
        return res;
      }
    },
    methods: {
      openUsersActivity(){
        bbn.fn.link(this.baseURL + 'history/');
      },
      openUserActivity(){
        bbn.fn.link(this.baseURL + 'user_history');
      },
      openGlossary(){
        this.getPopup({
          scrollable: false,
          source: this.primary,
          component: 'appui-i18n-dashboard-form-glossary',
          label: bbn._('Config your translation tab')
        })
      },
      openProjectLanguagesCfg(){
        this.getPopup({
          //width: 600,
          //height: 300,
          label: bbn._("Config translation languages for the project"),
          scrollable: true,
          component: 'appui-i18n-dashboard-form-languages',
          componentOptions: {
            source: {
              langs: this.configuredLangs,
              idProject: this.idProject
            },
            currentLanguage: this.language,
            primariesLanguages: this.source.primary
          }
        })
      },
      getField: bbn.fn.getField,
      loadProject(){
        this.isLoading = true;
        this.data.splice(0);
        this.configuredLangs.splice(0);
        this.post(this.root + 'data/dashboard', {idProject: this.idProject}, d => {
          if (d.success) {
            this.data.push(...d.paths);
            this.configuredLangs.push(...d.langs);
          }

          this.$nextTick(() => {
            this.isLoading = false;
          });
        }, () => {
          this.$nextTick(() => {
            this.isLoading = false;
          });
        });
      }
    },
    created(){
      appui.register('appui-i18n-dashboard' + (this.source?.id_project ? `-${this.source.id_project}` : ''), this);
    },
    mounted(){
      if (this.idProject){
        this.loadProject();
      }
    },
    beforeDestroy(){
      appui.unregister('appui-i18n-dashboard' + (this.source?.id_project ? `-${this.source.id_project}` : ''));
    },
    watch : {
      idProject(val){
        if (val){
          this.language = bbn.fn.getField(this.source.projects, 'lang', 'id', val)
        }
      }
    }
  }
})();
