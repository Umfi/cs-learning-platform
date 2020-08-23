<template>
    <div class="container">
        <div class="row">
            <button type="button" class="btn btn-secondary mr-2" @click="resetSpreadsheet"><i class="fas fa-redo"></i> {{ $t('Reset table') }}</button>
            <spreadsheet-formula-info></spreadsheet-formula-info>
        </div>

        <div class="row mt-3">
            <hot-table ref="hotTableSpecificationComponent" :data="resultData" :settings="$data._settings"></hot-table>
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
            <spreadsheet-datavisualization ref="dataviscomponent" :taskid="taskid" :griddata="resultDataFormulaEvaluated"></spreadsheet-datavisualization>
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

    export default {
        props: ["taskid", "taskdata"],
        data() {
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
                    width: '100%',
                    stretchH: 'all',
                    height: 200,
                    licenseKey: process.env.MIX_HANDSONTABLE_KEY
                },
            };
        },
        components: {
            HotTable,
            PrismEditor
        },
        mounted() {

            this._hotInstanceSpecification = this.$refs.hotTableSpecificationComponent.hotInstance;
            var instSpecification = this._hotInstanceSpecification;

            this.dataVisualizationData = this.$refs.dataviscomponent.data;
            this.dataVisualizationType = this.$refs.dataviscomponent.type;

            var self = this;

            instSpecification.addHook('afterChange', function(){

                var mydata = self._hotInstanceSpecification.getData();

                for (var i = 0; i < mydata.length; i++){
                    for(var j = 0; j < mydata[i].length; j++){
                        if(mydata[i][j].toString().indexOf('=') > -1){
                            mydata[i][j] = self._hotInstanceSpecification.getDataAtCell (j, i);
                        }
                    }
                }

                self.resultDataFormulaEvaluated = mydata;
            })


            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                var data = self.$props.taskdata;
                self.programming = data.specification.programming;
                self.dataVisualization = data.specification.dataVisualization;
                self.specificationData = JSON.parse(JSON.stringify(data.specification.data));
                self.specificationCode = '' + data.specification.code;
                self.resultData = JSON.parse(JSON.stringify(data.specification.data));
                self.resultCode = '' + data.specification.code;

                window.table = instSpecification;
                window.table.updateSettings({ readOnly: self.programming});
            });

            var stepperEl = document.querySelector('#bs-stepper-' + self.$props.taskid);
            stepperEl.addEventListener('shown.bs-stepper', function (event) {
                setTimeout(function(){
                    instSpecification.render();
                }, 500);
            });


        },
        methods: {
            resetSpreadsheet: function() {
                this.resultData = JSON.parse(JSON.stringify(this.specificationData));
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
            /**
             * Called before global store method
             */
            _preStore() {
                this.dataVisualizationData = this.$refs.dataviscomponent.data;
                this.dataVisualizationType = this.$refs.dataviscomponent.type;
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
            }
        }
    }
</script>

