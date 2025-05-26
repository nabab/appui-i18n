(() => {
  return {
    data(){
      return {
        isOptionsProject: this.source.id === 'options',
        isTranslating: false,
        selectedLang: [],
        isLoading: false,
        toTranslate: [],
        currentIndex: 0
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
        if (this.currentIndex > 0) {
          this.currentIndex--;
        }
      },
      nextTranslation(){
        if (this.currentIndex < this.toTranslate.length - 1) {
          this.currentIndex++;
        }
      },
      saveTranslation(){
        if (this.currentTranslation) {
        }
      }
    }
  }
})();