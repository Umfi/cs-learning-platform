<template>
    <div class="container">
        <div class="row">
            <button type="button" class="btn btn-secondary mr-2" @click="resetSpreadsheet"><i class="fas fa-redo"></i> {{ $t('Reset table') }}</button>
            <spreadsheet-formula-info></spreadsheet-formula-info>

            <div class="align-self-lg-center ml-auto">
                <button type="button" class="btn btn-outline-dark btn-sm mr-2" @click="formatCell('bold')"><i class="fas fa-bold"></i></button>
                <button type="button" class="btn btn-outline-dark btn-sm mr-2" @click="formatCell('italic')"><i class="fas fa-italic"></i></button>
                <button type="button" class="btn btn-outline-dark btn-sm mr-2" @click="formatCell('underline')"><i class="fas fa-underline"></i></button>
                <button type="button" class="btn btn-outline-dark btn-sm mr-2" @click="formatCell('center')"><i class="fas fa-align-center"></i></button>
            </div>
        </div>

        <div class="row mt-2">
            <hot-table ref="hotTableSolutionComponent" :data="resultData" :settings="$data._settings"></hot-table>
        </div>

        <div v-if="programming">

            <div class="row">
                <button type="button" class="btn btn-secondary mr-2" @click="resetCode"><i class="fas fa-redo"></i> {{ $t('Reset code') }}</button>
                <button type="button" class="btn btn-success mr-2" @click="runCode"><i class="fas fa-play"></i> {{ $t('Run code') }}</button>
                <spreadsheet-code-info></spreadsheet-code-info>
            </div>

            <div class="row mt-3">
                <prism-editor v-model="resultCode" language="js" :lineNumbers="true"></prism-editor>
            </div>
        </div>

        <div v-show="dataVisualization">
            <spreadsheet-datavisualization ref="dataviscomponent" :taskid="taskid" :modalType="type"></spreadsheet-datavisualization>
        </div>

    </div>
</template>

<script>

    import { HotTable } from '@handsontable/vue';
    import Handsontable from 'handsontable';
    import "prismjs";
    import "prismjs/themes/prism.css";
    import PrismEditor from 'vue-prism-editor'
    import "vue-prism-editor/dist/VuePrismEditor.css";
    import Swal from 'sweetalert2/src/sweetalert2.js'
    import formulaMapping from "../../../configs/formulaMapping";

    export default {
        props: ["taskid", "taskdata", "type"],
        data() {
            var self = this;

            return {
                programming: false,
                dataVisualization: false,
                specificationData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                specificationCode: '',
                resultData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                resultCode: '',
                resultDataFormulaEvaluated: null,
                dataVisualizationData: null,
                dataVisualizationType: null,
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _hotInstanceSpecification: null,
                _settings: {
                    rowHeaders: true,
                    colHeaders: true,
                    formulas: true,
                    outsideClickDeselects: false,
                    selectionMode: 'multiple',
                    width: '100%',
                    stretchH: 'all',
                    height: 200,
                    contextMenu: {
                        items: {
                            "set_column_type": {
                                name: self.$t('Change cell format type'),
                                submenu: {
                                    items: [
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:text',
                                            name: self.$t('Text'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('text');
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:numeric',
                                            name: self.$t('Numeric'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('numeric');
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:currency',
                                            name: self.$t('Currency'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('currency');
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:percent',
                                            name: self.$t('Percent'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('percent');
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:date',
                                            name: self.$t('Date'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('date');
                                                }, 0);
                                            }
                                        },
                                    ]
                                }
                            },
                        }
                    },
                    licenseKey: process.env.MIX_HANDSONTABLE_KEY
                },
            };
        },
        components: {
            HotTable,
            PrismEditor
        },
        mounted() {

            this._hotInstanceSpecification = this.$refs.hotTableSolutionComponent.hotInstance;
            var instSpecification = this._hotInstanceSpecification;


            // Add custom functions - mapping german, english function name
            var formulaInst = instSpecification.getPlugin('formulas');
            for (let [englishFunctionName, germanFunctionName] of formulaMapping.entries()) {
                formulaInst.sheet.parser.setFunction(germanFunctionName, function(params) {
                    let newFormular = englishFunctionName + "(" + params.join(',') + ")";
                    let res = formulaInst.sheet.parser.parse(newFormular);
                    return res.result;
                });
            }

            this.dataVisualizationData = this.$refs.dataviscomponent.data;
            this.dataVisualizationType = this.$refs.dataviscomponent.type;
        },
        methods: {
            resetSpreadsheet: function() {
                this.resultData = JSON.parse(JSON.stringify(this.specificationData));
                this._hotInstanceSpecification.loadData(this.resultData);
            },
            resetCode() {
                this.resultCode = '' + this.specificationCode;
            },
            runCode() {

                this.resetSpreadsheet();

                var self = this;

                setTimeout(function(){
                    var parsedCode = self._preparseCode(self.resultCode);

                    try {
                        eval(parsedCode);
                    } catch (e) {
                        if (e instanceof SyntaxError) {

                            Swal.fire({
                                icon: 'warning',
                                title: self.$t('Syntax Error'),
                                text: e.message,
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: self.$t('Error'),
                                text: e.message,
                            });
                        }
                    }
                }, 500);


            },
            formatCell(style) {
                var self = this;

                self.hasProp = function(element, prop, value) {
                    return element[prop] && !!element[prop].match(new RegExp('(\\s|^)' + value + '(\\s|$)'));
                }

                self.addCellMeta = function(row, column, property, newProperty) {
                    var cellMeta = this._hotInstanceSpecification.getCellMeta(row, column);

                    if (!self.hasProp(cellMeta, property, newProperty)) {
                        this._hotInstanceSpecification.setCellMeta(row, column, property, (cellMeta[property] || '') + ' ' + newProperty);
                    }
                };

                self.removeCellMeta = function(row, column, property, propertyToRemove) {
                    var cellMeta = this._hotInstanceSpecification.getCellMeta(row, column);
                    var newClass = self.removeFromString(cellMeta[property], propertyToRemove);

                    this._hotInstanceSpecification.setCellMeta(row, column, property, newClass);
                };

                self.toggleCellMeta = function(row, column, property, newProperty) {
                    var cellMeta = this._hotInstanceSpecification.getCellMeta(row, column);

                    if (!self.hasProp(cellMeta, property, newProperty)) {
                        this._hotInstanceSpecification.setCellMeta(row, column, property, (cellMeta[property] || '') + ' ' + newProperty);
                    } else {
                        self.removeCellMeta(row, column, property, newProperty);
                    }
                };

                self.removeFromString = function(currentString, toRemove) {
                    if (currentString) {
                        var reg = new RegExp('(\\s|^)' + toRemove + '(\\s|$)');
                        currentString = currentString.replace(reg, '');
                    }
                    return currentString || '';
                };


                var selection = this._hotInstanceSpecification.getSelected();

                for (var i = 0; i < selection.length; i += 1) {

                    var item = selection[i];
                    var startRow = Math.min(item[0], item[2]);
                    var endRow = Math.max(item[0], item[2]);
                    var startCol = Math.min(item[1], item[3]);
                    var endCol = Math.max(item[1], item[3]);

                    if (startRow < 0 ) {
                        startRow = 0;
                    }
                    if (startCol < 0 ) {
                        startCol = 0;
                    }

                    for (var rowIndex = startRow; rowIndex <= endRow; rowIndex += 1) {
                        for (var columnIndex = startCol; columnIndex <= endCol; columnIndex += 1) {
                            self.toggleCellMeta(rowIndex, columnIndex, 'className', 'spreadsheetmodule-cell-' + style);
                        }
                    }

                }

                this._hotInstanceSpecification.render();
            },
            /**
             * Called before global store method
             */
            _preStore() {
                this.dataVisualizationData = this.$refs.dataviscomponent.data;
                this.dataVisualizationType = this.$refs.dataviscomponent.type;
                this.resultData = this._hotInstanceSpecification.getSourceData();
                this.resultDataFormulaEvaluated = this._hotInstanceSpecification.getData();
            },
            _onOpen() {

                var data = this.$props.taskdata;

                if (this.$props.type === "assignment") {

                    this.programming = data.specification.programming;
                    this.dataVisualization = data.specification.dataVisualization;

                    this._hotInstanceSpecification.loadData(data.specification.data);
                    this.specificationData = this._hotInstanceSpecification.getSourceData();
                    this.specificationCode = '' + data.specification.code;

                    this.resultData = this._hotInstanceSpecification.getSourceData();
                    this.resultCode = '' + data.specification.code;

                    var instSpecification = this._hotInstanceSpecification;
                    window.table = instSpecification;
                    window.table.updateSettings({readOnly: this.programming});

                    var stepperEl = document.querySelector('#bs-stepper-' + this.$props.taskid);
                    stepperEl.addEventListener('shown.bs-stepper', function (event) {
                        setTimeout(function(){
                            instSpecification.render();
                        }, 500);
                    });
                } else if (this.$props.type === "solution") {

                    this.programming = data.specification.programming;
                    this.dataVisualization = data.specification.dataVisualization;

                    this._hotInstanceSpecification.loadData(data.solution.resultData);
                    this.specificationData = this._hotInstanceSpecification.getSourceData();
                    this.specificationCode = '' + data.specification.code;

                    this.resultData = this._hotInstanceSpecification.getSourceData();
                    this.resultCode = '' + data.solution.resultCode;

                    if (this.dataVisualization) {
                        this.$refs.dataviscomponent.data = data.solution.dataVisualizationData;
                        this.$refs.dataviscomponent.type = data.solution.dataVisualizationType;
                        this.$refs.dataviscomponent.updateChart();
                    }

                    var instSpecification = this._hotInstanceSpecification;

                    window.table = instSpecification;
                    window.table.updateSettings({ readOnly: this.programming});
                    instSpecification.render();
                }
            },
            _preparseCode(code) {
                var tmp = '' + code;

                // REPLACE - getData
                tmp = tmp.replace(/getData(\s)*\(/gi, 'self._getData(');

                // REPLACE - setData
                tmp = tmp.replace(/setData(\s)*\(/gi, 'self._setData(');

                // REPLACE - sum
                tmp = tmp.replace(/sum(\s)*\(/gi, 'self._sum(');

                // REPLACE - mean
                tmp = tmp.replace(/mean(\s)*\(/gi, 'self._mean(');

                // REPLACE - max
                tmp = tmp.replace(/max(\s)*\(/gi, 'self._max(');

                // REPLACE - min
                tmp = tmp.replace(/min(\s)*\(/gi, 'self._min(');

                return tmp;
            },
            _convertLetterToNumber(str) {
                return str.toLowerCase().charCodeAt(0) - 97;
            },
            _getData(cell) {

                if  (typeof cell === "undefined") {
                    throw new Error("Wrong usage of getData. Parameter required.");
                }

                if  (!cell.charAt(0).match(/[a-z]/i)) {
                    throw new Error("Wrong usage of getData. No valid parameter. Need something like A1.");
                }

                var col = cell.toLowerCase().charCodeAt(0) - 97;
                var _row = cell.substring(1) - 1;

                if (Number.isInteger(_row)) {
                    var row = parseInt(_row);
                } else {
                    throw new Error("Wrong usage of getData. No valid parameter. Need something like A1.");
                }

                var returnValue = window.table.getDataAtCell(row, col);

                if (typeof returnValue === "undefined" || returnValue === "") {
                    return "";
                }

                if(!isNaN(returnValue)){
                    if (Number.isInteger(returnValue)) {
                        return parseInt(returnValue);
                    } else {
                        return parseFloat(returnValue);
                    }
                }

                return  returnValue;
            },
            _setData(cell, value) {

                if  (typeof cell === "undefined" || typeof value === "undefined") {
                    throw new Error("Wrong usage of setData. Parameters required.");
                }

                if  (!cell.charAt(0).match(/[a-z]/i)) {
                    throw new Error("Wrong usage of setData. No valid parameter. Need something like A1.");
                }

                var col = cell.toLowerCase().charCodeAt(0) - 97;
                var _row = cell.substring(1) - 1;

                if (Number.isInteger(_row)) {
                    var row = parseInt(_row);
                } else {
                    throw new Error("Wrong usage of setData. No valid parameter. Need something like A1.");
                }

                window.table.setDataAtCell(row, col, value);
            },
            _sum(...args) {
                let sum = 0;
                for (let arg of args) sum += this._getData(arg);
                return sum;
            },
            _mean(...args) {
                let sum = 0;
                for (let arg of args) sum += this._getData(arg);
                return sum/args.length;
            },
            _min(...args) {
                var data = [];
                for (let arg of args) {
                    data.push(this._getData(arg));
                }
                return Math.min(...data);
            },
            _max(...args) {
                var data = [];
                for (let arg of args) {
                    data.push(this._getData(arg));
                }
                return Math.max(...data);
            },
            _setCellType(type) {
                var selection = this._hotInstanceSpecification.getSelected();

                for (var i = 0; i < selection.length; i += 1) {

                    var item = selection[i];
                    var startRow = Math.min(item[0], item[2]);
                    var endRow = Math.max(item[0], item[2]);
                    var startCol = Math.min(item[1], item[3]);
                    var endCol = Math.max(item[1], item[3]);

                    if (startRow < 0 ) {
                        startRow = 0;
                    }
                    if (startCol < 0 ) {
                        startCol = 0;
                    }

                    for (var rowIndex = startRow; rowIndex <= endRow; rowIndex += 1) {
                        for (var columnIndex = startCol; columnIndex <= endCol; columnIndex += 1) {

                            if (type === "date") {
                                this._hotInstanceSpecification.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "date",
                                    dateFormat: 'DD.MM.YYYY'
                                });
                            } else if (type === "currency") {
                                this._hotInstanceSpecification.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: '0,0.00 $',
                                        culture: 'de-DE',
                                    },
                                });
                            } else if (type === "percent") {
                                this._hotInstanceSpecification.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: {
                                            output: "percent",
                                            mantissa: 2
                                        },
                                    },
                                });
                            } else if (type === "numeric") {
                                this._hotInstanceSpecification.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: {
                                            thousandSeparated: true
                                        },
                                    },
                                });
                            } else {
                                this._hotInstanceSpecification.setCellMetaObject(rowIndex, columnIndex, {
                                    type: type
                                });
                            }
                        }
                    }

                    this._hotInstanceSpecification.render();

                }
            },
        }
    }
</script>

