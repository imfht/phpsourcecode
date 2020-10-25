/**
 * CellEditing扩展
 */
Ext.define("PSI.UX.CellEditing", {
  extend: "Ext.grid.plugin.CellEditing",

  onSpecialKey: function (ed, field, e) {
    var sm;

    if (e.getKey() === e.TAB || e.getKey() == e.ENTER) {
      e.stopEvent();

      if (ed) {
        ed.onEditorTab(e);
      }

      sm = ed.up('tablepanel').getSelectionModel();
      if (sm.onEditorTab) {
        return sm.onEditorTab(ed.editingPlugin, e);
      }
    }
  }
});
