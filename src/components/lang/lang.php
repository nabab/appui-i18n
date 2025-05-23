<span :class="['appui-i18n-lang', 'bbn-nowrap', 'bbn-vmiddle', {'bbn-spadding': padded}]"
      style="display: inline-flex"
      :title="currentText">
  <img bbn-if="currentFlag"
       :src="currentFlag"
       style="height: 1rem; width: auto; object-fit: scale-down"
       :class="{'bbn-right-sspace': !onlyFlag && currentText}">
  <span bbn-if="currentText && !onlyFlag"
        bbn-text="currentText"/>
</span>