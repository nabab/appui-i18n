<div style="min-height: 500px">
  <bbn-table :source="source.path"
  >
    <bbns-column field="text"
                label="Path"
                flabel="<?= _('All different paths of this project') ?>"
                ref="paths_table"
                width="80%"
    ></bbns-column>

    <bbns-column field="language"
                label="<?= _('Language') ?>"
                flabel="<?= _('Original language of the path') ?>"
                width="10%"
    ></bbns-column>

    <bbns-column field="code"
                label=" "
                cls="bbn-c"
                :buttons="[{
                  notext: true,
                  action: find_new_strings,
                  icon: 'zmdi zmdi-flash',
                  title: '<?= _('Check for new strings in this path') ?>',
                  text: '<?= _('Check for new strings in this path') ?>',
                  },{
                  notext: true,
                  action: open_strings_table,
                  icon: 'nf nf-fa-book',
                  text: '<?= _('Go to the table of strings of this path') ?>',
                  title: '<?= _('Go to the table of strings of this path') ?>'
           	  	 }]"
    ></bbns-column>

  </bbn-table>
</div>