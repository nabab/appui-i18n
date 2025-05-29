(() => {
  return {
    data(){
      const secondaryBg = window.getComputedStyle(document.body).getPropertyValue('--secondary-background');
      return {
        isOptionsProject: this.source.id === 'options',
        isTranslating: false,
        selectedLang: [],
        isLoading: false,
        isLoadingSuggestions: false,
        isSuggestionsActive: !!this.source.isSuggestionsActive,
        toTranslate: [],
        toGenerate: false,
        isGenerating: false,
        currentIndex: 0,
        formButtons: [{
          label: bbn._('Previous'),
          icon: 'nf nf-md-page_previous',
          iconPosition: 'left',
          action: this.prevTranslation
        }, {
          label: bbn._('Skip'),
          icon: 'nf nf-md-page_next',
          action: this.nextTranslation,
          cls: 'bbn-tertiary',
        }, {
          label: bbn._('Save'),
          icon: 'nf nf-fa-save',
          iconPosition: 'right',
          action: this.saveTranslation,
          preset: 'submit'
        }],
        bgColors: [
          secondaryBg,
          secondaryBg + 'de',
          secondaryBg + 'c2',
          secondaryBg + 'a1'
        ]
      }
    },
    computed: {
      localeDirs(){
        return this.source?.locale_dirs || [];
      },
      data(){
        if (this.source?.language && this.source?.result) {
          let result = bbn.fn.clone(this.source.result, true);
          bbn.fn.iterate(result, (r, l) => {
            r.class = '';
            r.class_db = '';
            if (r.num_translations >= 0) {
              r.val = !r.num ? 0 : r.num_translations/r.num * 100
              /** the css class for progress bar */
              if ((r.val >= 0) && (r.val <= 30)) {
                r.class = 'low'
              }
              else if ((r.val > 30) && (r.val <= 70)) {
                r.class = 'medium'
              }
              else if ((r.val > 70) && (r.val <= 100)) {
                r.class = 'high'
              }
            }

          });

          return result;
        }

        return [];
      },
      configuredLangs(){
        return this.source?.languages || [];
      },
      currentTranslation(){
        return this.toTranslate[this.currentIndex] || false;
      }
    },
    methods: {
      normalize(val){
        return val && (val > 0) ? parseFloat(val.toFixed(2)) : 0;
      },
      toggleLang(lang){
        if (this.selectedLang.includes(lang)) {
          this.selectedLang = this.selectedLang.filter(l => l !== lang);
        }
        else {
          this.selectedLang.push(lang);
        }
      },
      startTranslation(){
        const url = this.root + 'data/translate';
        const data = {
          project: this.source.project,
          path: this.source.id,
          langs: this.selectedLang
        };
        if (this.isLoading) {
          bbn.fn.abort(this.isLoading);
        }

        this.isTranslating = true;
        this.isLoading = bbn.fn.getRequestId(url, data);
        this.post(this.root + 'data/translate', data, d => {
          if (d.success) {
            if (d.expressions?.length) {
              this.toTranslate = d.expressions;
              this.currentIndex = 0;
              this.$nextTick(() => {
                this.textareaFocus();
              });
            }
            else {
              this.isTranslating = false;
              this.alert(bbn._('There are no expressions to translate'));
            }
          }
          else {
            appui.error();
          }

          this.isLoading = false;
        });
      },
      stopTranslation(){
        if (this.isLoading) {
          bbn.fn.abort(this.isLoading);
          if (!this.toGenerate) {
            this.isLoading = false;
          }
        }

        if (this.isLoadingSuggestions) {
          bbn.fn.abort(this.isLoadingSuggestions);
          this.isLoadingSuggestions = false;
        }

        if (this.toGenerate) {
          this.isLoading = true;
          this.isGenerating = true;
          this.post(this.root + 'actions/generate', {
            id_project: this.source.project,
            id_option: this.source.id,
            languages: [this.source.language].concat(this.selectedLang)
          }, d => {
            if (d.success){
              if (d.widget?.result) {
                this.source.result = d.widget.result;
              }

              this.isGenerating = false;
              this.isLoading = false;
              this.isTranslating = false;
              this.toTranslate = [];
              this.currentIndex = 0;
              this.selectedLang = [];
              appui.success();
            }
            else {
              appui.error();
            }
          });
        }
        else {
          this.isTranslating = false;
          this.toTranslate = [];
          this.currentIndex = 0;
          this.selectedLang = [];
        }

      },
      prevTranslation(){
        if (this.isTranslating
          && (this.currentIndex > 0)
        ) {
          this.getRef('form').cancel();
          this.$nextTick(() => {
            this.currentIndex--;
            //this.loadSuggestions();
            this.textareaFocus();
          });
        }
      },
      nextTranslation(){
        if (this.isTranslating
          && (this.currentIndex < (this.toTranslate.length - 1))
        ) {
          this.getRef('form').cancel();
          this.$nextTick(() => {
            this.currentIndex++;
            //this.loadSuggestions();
            this.textareaFocus();
          });
        }
      },
      saveTranslation(){
        if (this.isTranslating
          && this.currentTranslation?.expression?.length
        ) {
          const translations = {};
          bbn.fn.each(this.selectedLang, l => {
            if (this.currentTranslation[l]?.translation?.length) {
              translations[l] = this.currentTranslation[l].translation;
            }
          });
          if (Object.keys(translations).length) {
            this.post(this.root + 'actions/translate', {
              project: this.source.project,
              path: this.source.id,
              expression: this.currentTranslation.expression,
              translations
            }, d => {
              if (d.success){
                this.toGenerate = true;
                appui.success();
                if (this.currentIndex < (this.toTranslate.length - 1)) {
                  this.currentIndex++;
                  this.textareaFocus();
                }
              }
              else {
                appui.error();
              }
            })
          }
        }
      },
      setSuggest(lang, suggest){
        if (this.isTranslating
          && this.currentTranslation
        ) {
          this.currentTranslation[lang].translation = suggest;
        }
      },
      loadSuggestions(current){
        if (this.isSuggestionsActive
          && this.isTranslating
          && this.toTranslate?.length
          && this.currentTranslation
          && this.selectedLang?.length
        ) {
          let expressions = [];
          if (current) {
            expressions.push(this.currentTranslation.expression);
          }
          else {
            let i = 0;
            expressions = bbn.fn.map(
              bbn.fn.filter(
                this.toTranslate.slice().splice(this.currentIndex > 1 ? (this.currentIndex - 2) : 0),
                e => {
                  if (i === 4) {
                    return false;
                  }

                  let res = false;
                  bbn.fn.each(this.selectedLang, l => {
                    if (!e[l].suggestions?.length) {
                      res = true;
                      i++;
                      return false;
                    }
                  });
                  return res;
                }
              ),
              e => e.expression
            );
          }
          if (expressions.length) {
            if (this.isLoadingSuggestions) {
              bbn.fn.abort(this.isLoadingSuggestions);
              this.isLoadingSuggestions = false;
            }

            const url = this.root + 'data/translate';
            const data = {
              project: this.source.project,
              path: this.source.id,
              langs: this.selectedLang,
              expressions
            };
            this.isLoadingSuggestions = bbn.fn.getRequestId(url, data);
            this.post(url, data, d => {
              if (d.success) {
                if (d.expressions?.length) {
                  bbn.fn.each(d.expressions, e => {
                    const exp = bbn.fn.getRow(this.toTranslate, 'expression', e.expression);
                    if (exp) {
                      bbn.fn.each(this.selectedLang, l => {
                        exp[l].suggestions = e[l].suggestions || [];
                      });
                    }
                  });
                }
              }

              this.isLoading = false;
            });
          }
        }
      },
      textareaFocus(){
        this.getRef('textarea')?.getRef('element')?.focus();
      }
    },
    watch: {
      currentTranslation(newVal){
        this.$nextTick(() => {
          const form = this.getRef('form');
          if (form?.init && form?.reinit) {
            form.reinit();
            form.init();
          }

          if (this.isSuggestionsActive) {
            let toLoadSuggestions = false;
            bbn.fn.each(this.selectedLang, l => {
              if (!newVal[l]?.suggestions?.length) {
                toLoadSuggestions = true;
                return false;
              }
            });
            if (toLoadSuggestions) {
              this.loadSuggestions(true);
            }
          }
        });
      }
    }
  }
})();