<template>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" v-bind:id="'specification-tab-' + taskid" data-toggle="tab" v-bind:href="'#specification-' + taskid" role="tab" aria-controls="specification" aria-selected="true">{{ $t('Specification') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" v-bind:id="'solution-tab-' + taskid" data-toggle="tab" v-bind:href="'#solution-' + taskid" role="tab" aria-controls="solution" aria-selected="false">{{ $t('Solution') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" v-bind:id="'tips-tab-' + taskid" data-toggle="tab" v-bind:href="'#tips-' + taskid" role="tab" aria-controls="tips" aria-selected="false">{{ $t('Tips') }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" v-bind:id="'specification-' + taskid" role="tabpanel" aria-labelledby="specification-tab">
                <div class="mt-1">

                    <div>
                        <div class="form-group">
                            <label for="col">{{ $t('Columns') }}</label>
                            <input v-model="col" type="number" min="1" max="20" class="form-control" id="col">
                        </div>
                        <div class="form-group">
                            <label for="row">{{ $t('Rows') }}</label>
                            <input v-model="row" type="number" min="1" max="50" class="form-control" id="row">
                        </div>
                        <div class="form-check">
                            <input v-model="programming" type="checkbox" class="form-check-input" id="programming">
                            <label class="form-check-label" for="programming">{{ $t('Programming Task') }}</label>
                            <i class="fa-info-circle fas ml-1" data-toggle="tooltip" :title="$t('Student can only edit spreadsheet by code.')"></i>
                        </div>
                        <div class="form-check">
                            <input v-model="dataVisualization" type="checkbox" class="form-check-input" id="datavis">
                            <label class="form-check-label" for="datavis">{{ $t('Data Visualization Task') }}</label>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <button type="button" class="btn btn-secondary mr-2" @click="resetSpreadsheet"><i class="fas fa-redo"></i> {{ $t('Reset table') }}</button>
                        <spreadsheet-formula-info></spreadsheet-formula-info>
                    </div>

                    <div>
                        <hot-table ref="hotTableSpecificationComponent" :data="specificationData" :settings="$data._settings"></hot-table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'solution-' + taskid" role="tabpanel" aria-labelledby="solution-tab">
                <div class="mt-1">
                    <div class="mt-2">
                        <span>{{ $t('Check solution for content or formula used:') }}</span><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" v-model="solutionCmpType" value="content" >
                            {{ $t('Content') }}
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" v-model="solutionCmpType" value="formula">
                            {{ $t('Formula') }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-primary mr-2" @click="syncData()"><i class="fas fa-sync"></i> {{ $t('Copy values from specification') }}</button>
                    </div>
                    <hr>
                    <div>
                        <hot-table ref="hotTableSolutionComponent" :data="solutionData" :settings="$data._settings_solution"></hot-table>
                    </div>
                    <div v-show="dataVisualization">
                        <spreadsheet-datavisualization ref="dataviscomponent" :taskid="taskid"></spreadsheet-datavisualization>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'tips-' + taskid" role="tabpanel" aria-labelledby="tips-tab">
                <div class="mt-1">
                    <tip-list ref="tips" :taskid="taskid"></tip-list>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import { HotTable } from '@handsontable/vue';
    import Handsontable from 'handsontable';
    import formulaMapping from "../../../../configs/formulaMapping";

    export default {
        props: ["taskid"],
        data() {
            var self = this;

            return {
                row: 5,
                col: 4,
                programming: false,
                dataVisualization: false,
                specificationData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                specificationCode: '',
                solutionData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                solutionCode: '',
                solutionDataFormulaEvaluated: null,
                dataVisualizationData: null,
                dataVisualizationType: null,
                solutionCmpType: "content",
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _hotInstanceSpecification: null,
                _hotInstanceSolution: null,
                _settings: {
                    rowHeaders: true,
                    colHeaders: true,
                    formulas: true,
                    outsideClickDeselects: false,
                    selectionMode: 'multiple',
                    stretchH: 'all',
                    width: '100%',
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
                                                    self._setCellType('text', self._hotInstanceSpecification);
                                                  }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:numeric',
                                            name: self.$t('Numeric'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('numeric', self._hotInstanceSpecification);
                                                  }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:currency',
                                            name: self.$t('Currency'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('currency', self._hotInstanceSpecification);
                                                 }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:percent',
                                            name: self.$t('Percent'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('percent', self._hotInstanceSpecification);
                                                 }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:date',
                                            name: self.$t('Date'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('date', self._hotInstanceSpecification);
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
                _settings_solution: {
                    rowHeaders: true,
                    colHeaders: true,
                    formulas: true,
                    outsideClickDeselects: false,
                    selectionMode: 'multiple',
                    stretchH: 'all',
                    width: '100%',
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
                                                    self._setCellType('text', self._hotInstanceSolution);
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:numeric',
                                            name: self.$t('Numeric'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('numeric', self._hotInstanceSolution);
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:currency',
                                            name: self.$t('Currency'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('currency', self._hotInstanceSolution);
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:percent',
                                            name: self.$t('Percent'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('percent', self._hotInstanceSolution);
                                                }, 0);
                                            }
                                        },
                                        {
                                            // Key must be in the form "parent_key:child_key"
                                            key: 'set_column_type:date',
                                            name: self.$t('Date'),
                                            callback: function(key, selection, clickEvent) {
                                                setTimeout(function() {
                                                    self._setCellType('date', self._hotInstanceSolution);
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
        },
        mounted() {
            this._hotInstanceSpecification = this.$refs.hotTableSpecificationComponent.hotInstance;
            this._hotInstanceSolution = this.$refs.hotTableSolutionComponent.hotInstance;

            var instSpecification = this._hotInstanceSpecification;
            var instSolution = this._hotInstanceSolution;

            var self = this;

            // Add custom functions - mapping german, english function name
            var formulaInstSpecification = instSpecification.getPlugin('formulas');
            for (let [englishFunctionName, germanFunctionName] of formulaMapping.entries()) {
                formulaInstSpecification.sheet.parser.setFunction(germanFunctionName, function(params) {
                    let newFormular = englishFunctionName + "(" + params.join(',') + ")";
                    let res = formulaInstSpecification.sheet.parser.parse(newFormular);
                    return res.result;
                });
            }
            var formulaInstSolution = instSolution.getPlugin('formulas');
            for (let [englishFunctionName, germanFunctionName] of formulaMapping.entries()) {
                formulaInstSolution.sheet.parser.setFunction(germanFunctionName, function(params) {
                    let newFormular = englishFunctionName + "(" + params.join(',') + ")";
                    let res = formulaInstSolution.sheet.parser.parse(newFormular);
                    return res.result;
                });
            }

            // store data of evaluated grid (formula -> value)
            instSolution.addHook('afterChange', function(){

                var mydata = self._hotInstanceSolution.getData();

                for (var i = 0; i < mydata.length; i++){
                    for(var j = 0; j < mydata[i].length; j++){
                        if (mydata[i][j] !== null) {
                            if (mydata[i][j].toString().indexOf('=') > -1) {
                                mydata[i][j] = self._hotInstanceSolution.getDataAtCell(j, i);
                            }
                        } else {
                            mydata[i][j] = "";
                        }
                    }
                }

                self.solutionDataFormulaEvaluated = mydata;
            });


            this.dataVisualizationData = this.$refs.dataviscomponent.data;
            this.dataVisualizationType = this.$refs.dataviscomponent.type;


            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                setTimeout(function(){
                    instSpecification.render();
                    instSolution.render();
                }, 200);

                $('[data-toggle="tooltip"]').tooltip();

                axios.get("/teacher/getTask/" + self.$props.taskid)
                    .then(response => {
                        if (response.data.task) {
                            var data = {
                                specification: response.data.task.specification,
                                solution: response.data.task.solution
                            };

                            if  (
                                typeof data.specification === "undefined" ||
                                typeof data.specification === "undefined" ||
                                data.specification == "" ||
                                data.solution == ""
                            ) {
                                return;
                            }

                            self.col = data.specification.col;
                            self.row = data.specification.row;
                            self.programming = data.specification.programming;
                            self.dataVisualization = data.specification.dataVisualization;

                            if ((data.solution.solutionCmpType === "content")
                                || (data.solution.solutionCmpType === "formula")) {
                                self.solutionCmpType = data.solution.solutionCmpType;
                            }

                            setTimeout(function(){
                                self._hotInstanceSpecification.loadData(data.specification.data);
                                self.specificationData = self._hotInstanceSpecification.getData();

                                self.specificationCode = data.specification.code;

                                self._hotInstanceSolution.loadData(data.solution.data);
                                self.solutionData = self._hotInstanceSolution.getData();

                                self.solutionCode = data.solution.code;

                                if (self.dataVisualization) {

                                    self.$refs.dataviscomponent.data = data.solution.dataVisualizationData;
                                    self.$refs.dataviscomponent.type = data.solution.dataVisualizationType;
                                    self.$refs.dataviscomponent.updateChart();
                                }

                                self.dataVisualizationData = self.$refs.dataviscomponent.data;
                                self.dataVisualizationType = self.$refs.dataviscomponent.type;


                            }, 500);


                        }
                    }).catch(function (error) {
                    console.error(error);
                });
            });

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
                setTimeout(function(){
                    instSpecification.render();
                    instSolution.render();
                }, 200);
            });
        },
        watch: {
            row: function(newVal, oldVal) {
                var curRows = this._hotInstanceSpecification.countRows();

                if(newVal > curRows){
                    this._hotInstanceSpecification.alter('insert_row',curRows ,newVal - curRows, 's',true);
                    this._hotInstanceSolution.alter('insert_row',curRows ,newVal - curRows, 's', true);
                }
                else if (newVal < curRows){
                    this._hotInstanceSpecification.alter('remove_row', curRows-1,curRows - newVal, '');
                    this._hotInstanceSolution.alter('remove_row', curRows-1,curRows - newVal, '');
                }

            },
            col: function(newVal, oldVal) {
                var curCols = this._hotInstanceSpecification.countCols();

                if(newVal > curCols){
                    this._hotInstanceSpecification.alter('insert_col',curCols ,newVal - curCols);
                    this._hotInstanceSolution.alter('insert_col',curCols ,newVal - curCols);
                }
                else if (newVal < curCols){
                    this._hotInstanceSpecification.alter('remove_col', curCols-1,curCols - newVal );
                    this._hotInstanceSolution.alter('remove_col', curCols-1,curCols - newVal );
                }

            }
        },
        methods: {
            resetSpreadsheet: function() {
                this._hotInstanceSpecification.clear();
            },
            syncData() {
                var spec = this._hotInstanceSpecification.getData();
                this._hotInstanceSolution.loadData(spec);
            },
            /**
             * Called before global store method
             */
            _preStore() {
                this.specificationData = this._hotInstanceSpecification.getData();
                this.solutionData = this._hotInstanceSolution.getData();
                this.dataVisualizationData = this.$refs.dataviscomponent.data;
                this.dataVisualizationType = this.$refs.dataviscomponent.type;
            },
            _setCellType(type, instance) {
                var selection = instance.getSelected();

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
                                instance.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "date",
                                    dateFormat: 'DD.MM.YYYY'
                                });
                            } else if (type === "currency") {
                                instance.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: '0,0.00 $',
                                        culture: 'de-DE',
                                    },
                                });
                            } else if (type === "percent") {
                                instance.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: {
                                            output: "percent",
                                            mantissa: 2
                                        },
                                    },
                                });
                            } else if (type === "numeric") {
                                instance.setCellMetaObject(rowIndex, columnIndex, {
                                    type: "numeric",
                                    numericFormat: {
                                        pattern: {
                                            thousandSeparated: true
                                        },
                                    },
                                });
                            } else {
                                instance.setCellMetaObject(rowIndex, columnIndex, {
                                    type: type
                                });
                            }
                        }
                    }

                    instance.render();

                }
            },
        }
    }
</script>

