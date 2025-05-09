(() => {
  return {
    name: 'appui-i18n-dashboard',
    props: {
      source: {
        type: Object
      }
    },
    data(){
      const idProject = this.source.projects?.length ? bbn.fn.getField(this.source.projects, 'id', 'name', bbn.env.appName) : '';
      return {
        isLoading: false,
        idProject,
        primary: this.source.primary,
        optionsRoot: appui.plugins['appui-option'] + '/',
        root: appui.plugins['appui-i18n'] + '/'
      }
    },
    computed: {
      isOptionsProject(){
        return this.idProject === 'options';
      },
      currentProject(){
        return !!this.idProject ? bbn.fn.getRow(this.source.projects, 'id', this.idProject) : {};
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
        if (this.source.data){
          let buttons = [{
            label: bbn._('Update widget data'),
            icon: 'nf nf-fa-retweet',
            action: 'remake_cache'
          }];
          if (!this.isOptionsProject) {
            buttons.push({
              label: bbn._('Setup languages'),
              icon: 'nf nf-fa-flag',
              action: 'generate'
            }, {
              label: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }, {
              label: bbn._('Delete locale folder'),
              icon: 'nf nf-fa-trash',
              action: 'delete_locale_folder',
            });
          }
          else {
            buttons.push({
              label: bbn._('Create translation files'),
              icon: 'nf nf-md-file_replace_outline',
              action: 'generateFiles',
            }, {
              label: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            });
          }
          if (bbn.fn.isArray(this.source.data)
            && this.source.data.length
          ) {
            bbn.fn.each(this.source.data, v => {
              if (v.id || v.code) {
                res.push({
                  label: v.title + (this.isOptionsProject ? ` (${v.code})` : ''),
                  key: v.id || v.code,
                  component : 'appui-i18n-widget',
                  source: v,
                  buttonsRight: buttons,
                  componentOptions: {
                    id_project: this.idProject,
                  }
                });
              }
            });
          }
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
      isMobile: bbn.fn.isMobile,
      openUsersActivity(){
        bbn.fn.link(this.root + 'page/history/');
      },
      openUserActivity(){
        bbn.fn.link(this.root + 'page/user_history');
      },
      openGlossary(){
        //open a component popup to select source language and translation language for the table glossary
          var tab = this.closest('bbn-container').getComponent();
          this.getPopup({
            scrollable: false,
            width: 400,
            height: 250,
            source: {
              source_lang: false,
              translation_lang: false,
              primary: tab.primary,
              dd_source_lang: tab.dd_source_lang,
              dd_translation_lang: tab.dd_translation_lang,
            },
            component: tab.$options.components.cfg_translations_form,
            label: bbn._('Config your translation tab')
          })

        },

      openProjectLanguagesCfg(){
        this.getPopup({
          //width: 600,
          //height: 300,
          label: bbn._("Config translation languages for the project"),
          scrollable: true,
          component: this.$options.components.languagesForm,
          componentOptions: {
            source: {
              langs: this.source.configured_langs,
              idProject: this.idProject
            },
            currentLanguage: this.language,
            primariesLanguages: this.source.primary
          }
        })
      },
      getField: bbn.fn.getField,
      loadProject(){
        this.source.data.splice(0);
        this.source.configured_langs.splice(0);
        this.post(this.root + 'data/dashboard', {idProject: this.idProject}, d => {
          if (d.success) {
            this.source.data.push(...d.paths);
            this.source.configured_langs.push(...d.langs);
          }
        });
      }
    },
    created(){
      appui.register('appui-i18n-dashboard', this);
    },
    beforeDestroy(){
      appui.unregister('appui-i18n-dashboard');
    },
    watch : {
      idProject(val){
        if (val){
          this.language = bbn.fn.getField(this.source.projects, 'lang', 'id', val)
        }
      }
    },
    components: {
      'cfg_translations_form': {
        template: `<bbn-form :source="source.row"
                  @submit="link"
                  :prefilled="true"
                  @cancel="cancel"
                  :scrollable="false">
          <div class="bbn-grid-fields bbn-flex-fill bbn-padding bbn-c">
            <span>
              <?= _('Select source language') ?>:
            </span>
            <div>
              <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.source_lang"></bbn-dropdown>
            </div>

            <span>
              <?= _('Select a language for the translation') ?>:
            </span>
            <div>
              <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.translation_lang"></bbn-dropdown>
            </div>
          </div>
        </bbn-form>`,
        props:['source'],
        methods: {
          link(){
            bbn.fn.link(this.root + 'page/glossary/' + this.source.source_lang + '/' + this.source.translation_lang);
          },
          cancel(){
            this.getPopup().close();
          },
        }
      },
      languagesForm: {
        props: {
          source: {
            type: Object
          },
          currentLanguage: {
            type: String
          },
          primariesLanguages: {
            type: Array
          }
        },
        template: `
        <div class="bbn-w-100">
          <bbn-form :scrollable="false"
                    :source="source"
                    ref="form"
                    :action="root + 'actions/languages_form'"
                    confirm-leave="` + bbn._('Are you sure you want to exit without saving changes?') + `"
                    :prefilled="true"
                    @success="success">
            <div bbn-if="primariesLanguages?.length"
                 class="bbn-padding bbn-grid bbn-grid-gap"
                 style="grid-template-columns: repeat(3, 1fr)">
              <div bbn-for="l in primariesLanguages"
                  class="bbn-spadding bbn-radius bbn-alt-background">
                <bbn-checkbox :id="l.id"
                              :checked="source.langs.includes(l.id)"
                              @change="toggleLang"
                              :label="l.text"
                              component="appui-i18n-lang"
                              :component-options="{code: l.code}"
                              :disabled="l.code === currentLanguage"/>
              </div>
            </div>
            <h2 bbn-else>` + bbn._('No primary languages found') + `</h2>
          </bbn-form>
        </div>`,
        data(){
          return {
            root: appui.plugins['appui-i18n'] + '/'
          }
        },
        methods:{
          success(d){
            if (d.success) {
              appui.success(bbn._('Languages successfully updated'))
            }
            else {
              appui.error();
            }
          },
          toggleLang(val, obj){
            let idx = this.source.langs.indexOf(obj.id);
            if (idx > -1) {
              this.source.langs.splice(idx, 1);
            }
            else {
              this.source.langs.push(obj.id)
            }
          }
        },

      }
    }
  }

})();
