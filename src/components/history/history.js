(() => {
  return {
    props: {
      source: {
        type: [Array, String],
        required: true
      },
      user: {
        type: Boolean,
        default: false
      }
    },
    data(){
      return {
        flagUrl: 'https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/'
      };
    },
    methods: {
      renderUser(row){
        return row.user ? appui.getUserName(row.user) : '';
      },
      renderStatus(row){
        let st = '';
        let icon = '';
        let color = '';
        let title = '';
        if (row.opr === 'DELETE') {
          icon = 'nf nf-fa-times';
          color = 'bbn-red';
          title = bbn._('Expression deleted from database');
        }
        else if ((row.original_lang === row.translation_lang)
          && (row.expression === row.original_exp)
        ) {
          icon = 'nf nf-fa-check';
          color = 'bbn-green';
          title = bbn._('Expressions are identical');
        }
        else if ((row.original_lang === row.translation_lang)
          && (row.expression !== row.original_exp)
        ) {
          icon = 'nf nf-fa-exclamation_triangle';
          color = 'bbn-orange';
          title = bbn._('Expression changed in its original language');
        }
        else if ((row.original_lang !== row.translation_lang)
          && (row.expression !== row.original_exp)
        ) {
          icon = 'nf nf-oct-smiley';
          color = 'bbn-green';
          title = bbn._('Expression translated');
        }
        else if ((row.original_lang !== row.translation_lang)
          && (row.expression === row.original_exp)
        ) {
          icon = 'nf nf-oct-smiley';
          color = 'bbn-green';
          title = bbn._('Translated! Expression is the same in the two languages');
        }

        if (title) {
          return `<i class="${icon} ${color} bbn-xl" title="${title}"></i>`;
        }
        return st;
      },
      renderFlag(row, col){
        let code = row[col.field === 'lang' ? 'translation_lang' : 'original_lang'];
        if (code === 'en') {
          code = 'gb';
        }

        return `<img style="height: 1rem; width: auto; object-fit: scale-down" src="${this.flagUrl}${code}.svg" alt="${code.toUpperCase()} Flag">`;
      },
    },
  }
})();
