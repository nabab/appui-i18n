(() => {
  return {
    data(){
      const secondaryBg = window.getComputedStyle(document.body).getPropertyValue('--secondary-background');
      return {
        isOptionsProject: this.source.id === 'options',
        isTranslating: false,
        selectedLang: [],
        isLoading: false,
        toTranslate: [],
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
        this.isLoading = true;
        this.isTranslating = true;
        this.post(this.root + 'data/translate', {
          project: this.source.project,
          path: this.source.id,
          langs: this.selectedLang
        }, d => {
          if (d.success) {
            this.toTranslate = d.expressions;
          }
          else {
            appui.error();
          }

          this.isLoading = false;
        });
      },
      stopTranslation(){
        this.isTranslating = false;
        this.toTranslate = [];
        this.currentIndex = 0;
      },
      prevTranslation(){
        if (this.isTranslating
          && (this.currentIndex > 0)
        ) {
          this.getRef('form').cancel();
          this.$nextTick(() => {
            this.currentIndex--;
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
            this.getSuggestions();
          });
        }
      },
      saveTranslation(){
        if (this.isTranslating
          && this.currentTranslation
        ) {
          const translations = {};
          bbn.fn.each(this.selectedLang, l => {
            translations[l] = this.currentTranslation[l].translation;
          });
          if (bbn.fn.filter(translations, t => t.length).length) {
            this.post(this.root + 'actions/translate', {
              project: this.source.project,
              path: this.source.id,
              expression: this.currentTranslation.expression,
              translations
            }, d => {
  
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
      getSuggestions(){
        if (this.isTranslating) {
          
        }
      }
    },
    watch: {
      currentTranslation(){
        this.$nextTick(() => {
          const form = this.getRef('form');
          if (form) {
            form.init();
            bbn.fn.log('AAAAAAAAAA')
          }
        });
      }
    }
  }
})();