Ext.define("PSI.UX.NumberOverride", {
  override: "Ext.grid.column.Number",

  defaultRenderer: function (value) {
    if (value >= 0) {
      return Ext.util.Format.number(value, this.format);
    } else {
      return "-"
        + Ext.util.Format.number(Math.abs(value),
          this.format);
    }
  }
});
