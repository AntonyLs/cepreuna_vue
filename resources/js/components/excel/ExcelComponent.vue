<template>
    <button
        @click="exportExcel"
    >
        <slot></slot>
    </button>
</template>

<script>
    import * as XLSX from 'xlsx/xlsx';
    // window.$ = window.jQuery = require('jquery');
    export default {
        name: "vue-excel-xlsx",
        props: {
            columns: {
                type: Array,
                default: []
            },
            data: {
                type: Array,
                default: []
            },
            filename: {
                type: String,
                default: 'excel'
            },
            sheetname: {
                type: String,
                default: 'SheetName'
            }
        },
        methods: {
            exportExcel() {
                let createXLSLFormatObj = [];
                let newXlsHeader = [];
                let vm = this;
                if (vm.columns.length === 0){
                    console.log("Add columns!");
                    return;
                }
                if (vm.data.length === 0){
                    console.log("Add data!");
                    return;
                }
                $.each(vm.columns, function(index, value) {
                    newXlsHeader.push(value.label);
                });
                createXLSLFormatObj.push(newXlsHeader);
                $.each(vm.data, function(index, value) {
                    let innerRowData = [];
                    $.each(vm.columns, function(index, val) {
                        let stringField = [];
                            stringField = val.field.split(".");
                        switch (stringField.length) {
                            case 1:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[val.field]));
                                }else {
                                    innerRowData.push(value[val.field]);
                                }
                                break;
                            case 2:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]]));
                                }else {
                                    innerRowData.push(value[stringField[0]][stringField[1]]);
                                }
                                break;
                            case 3:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]][stringField[2]]));
                                }else {
                                    innerRowData.push(value[stringField[0]][stringField[1]][stringField[2]]);
                                }
                                break;
                            case 4:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]]));
                                }else {
                                    innerRowData.push(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]]);
                                }
                                break;
                            case 5:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]][stringField[4]]));
                                }else {
                                    innerRowData.push(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]][stringField[4]]);
                                }
                                break;
                            case 6:
                                if (val.dataFormat && typeof val.dataFormat === 'function') {
                                    innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]][stringField[4]][stringField[5]]));
                                }else {
                                    innerRowData.push(value[stringField[0]][stringField[1]][stringField[2]][stringField[3]][stringField[4]][stringField[5]]);
                                }
                                break;
                            default:
                                break;
                        }
                        // if(stringField.length==1){
                        //     if (val.dataFormat && typeof val.dataFormat === 'function') {
                        //         innerRowData.push(val.dataFormat(value[val.field]));
                        //     }else {
                        //         innerRowData.push(value[val.field]);
                        //     }
                        // }else{
                        //     if (val.dataFormat && typeof val.dataFormat === 'function') {
                        //         innerRowData.push(val.dataFormat(value[stringField[0]][stringField[1]]));
                        //     }else {
                        //         innerRowData.push(value[stringField[0]][stringField[1]]);
                        //     }
                        //     // innerRowData.push(value[stringField[0]][stringField[1]]);
                        // }
                    });
                    createXLSLFormatObj.push(innerRowData);
                });
                let filename = vm.filename + ".xlsx";
                let ws_name = vm.sheetname;
                let wb = XLSX.utils.book_new(),
                    ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
                XLSX.utils.book_append_sheet(wb, ws, ws_name);
                XLSX.writeFile(wb, filename);
            }
        }
    }
</script>
