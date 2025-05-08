<span :class="['appui-i18n-lang', 'bbn-nowrap', 'bbn-vmiddle', {'bbn-spadding': padded}]"
      style="display: inline-flex">
  <img bbn-if="currentFlag"
       :src="currentFlag"
       style="height: 1rem; width: auto; object-fit: scale-down"
       class="bbn-right-sspace">
  <span bbn-if="currentText"
        bbn-text="currentText"/>
</span>