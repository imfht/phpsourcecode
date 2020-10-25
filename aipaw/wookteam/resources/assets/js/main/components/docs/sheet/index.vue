<template>
    <div ref="xspreadsheet" class="xspreadsheet" :class="{'xspreadsheet-readonly':readOnly}"></div>
</template>

<style lang="scss">
    .xspreadsheet-readonly {
        .x-spreadsheet-menu {
            > li:first-child {
              > div.x-spreadsheet-icon {
                  display: none;
              }
            }
        }
    }
</style>
<style lang="scss" scoped>
    .xspreadsheet {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
    }
</style>
<script>
    import Spreadsheet from 'x-data-spreadsheet';
    import zhCN from 'x-data-spreadsheet/dist/locale/zh-cn';
    import XLSX from 'xlsx';

    export default {
        name: "Sheet",
        props: {
            value: {
                type: [Object, Array],
                default: function () {
                    return {}
                }
            },
            readOnly: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                sheet: null,
                clientHeight: 0,
                clientWidth: 0,

                bakValue: '',
            }
        },
        mounted() {
            Spreadsheet.locale('zh-cn', zhCN);
            //
            let options = {
                view: {
                    height: () => {
                        try {
                            return this.clientHeight = this.$refs.xspreadsheet.clientHeight;
                        }catch (e) {
                            return this.clientHeight;
                        }
                    },
                    width: () => {
                        try {
                            return this.clientWidth = this.$refs.xspreadsheet.clientWidth;
                        }catch (e) {
                            return this.clientWidth;
                        }
                    },
                },
            };
            if (this.readOnly) {
                options.mode = 'read'
                options.showToolbar = false
                options.showContextmenu = false;
            }
            this.bakValue = JSON.stringify(this.value);
            this.sheet = new Spreadsheet(this.$refs.xspreadsheet, options).loadData(this.value).change(data => {
                if (!this.readOnly) {
                    this.bakValue = JSON.stringify(this.sheet.getData());
                    this.$emit('input', this.sheet.getData());
                }
            });
            //
            this.sheet.validate()
        },
        watch: {
            value: {
                handler(value) {
                    if (this.bakValue == JSON.stringify(value)) {
                        return;
                    }
                    this.bakValue = JSON.stringify(value);
                    this.sheet.loadData(value);
                },
                deep: true
            }
        },
        methods: {
            exportExcel(name, bookType){
                var new_wb = this.xtos(this.sheet.getData());
                XLSX.writeFile(new_wb, name + "." + (bookType == 'xlml' ? 'xls' : bookType), {
                    bookType: bookType || "xlsx"
                });
            },

            xtos(sdata) {
                var out = XLSX.utils.book_new();
                sdata.forEach(function(xws) {
                    var aoa = [[]];
                    var rowobj = xws.rows;
                    for(var ri = 0; ri < rowobj.len; ++ri) {
                        var row = rowobj[ri];
                        if(!row) continue;
                        aoa[ri] = [];
                        Object.keys(row.cells).forEach(function(k) {
                            var idx = +k;
                            if(isNaN(idx)) return;
                            aoa[ri][idx] = row.cells[k].text;
                        });
                    }
                    var ws = XLSX.utils.aoa_to_sheet(aoa);
                    XLSX.utils.book_append_sheet(out, ws, xws.name);
                });
                return out;
            },
        }
    }
</script>
