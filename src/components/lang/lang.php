<span :class="['appui-i18n-lang', 'bbn-nowrap', 'bbn-vmiddle', {'bbn-spadding': padded}]"
      style="display: inline-flex">
  <img v-if="currentFlag"
       :src="currentFlag"
       style="height: 1rem; width: auto; object-fit: scale-down"
       class="bbn-right-sspace">
  <span v-if="currentText"
        v-text="currentText"/>
</span>