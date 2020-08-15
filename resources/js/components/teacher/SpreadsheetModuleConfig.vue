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

                    <div>
                        <button type="button" class="btn btn-sm btn-primary" @click="syncData()"><i class="fas fa-sync"></i> {{ $t('Copy values from specification') }}</button>
                    </div>
                    <hr>
                    <div>
                        <hot-table ref="hotTableSolutionComponent" :data="solutionData" :settings="$data._settings"></hot-table>
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

    export default {
        props: ["taskid"],
        data() {
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
                    stretchH: 'all',
                    width: '100%',
                    height: 200,
                    licenseKey: process.env.MIX_HANDSONTABLE_KEY
                },
            };
        },
        components: {
            HotTable
        },
        mounted() {
            console.log('TaskID:' + this.$props.taskid);

            this._hotInstanceSpecification = this.$refs.hotTableSpecificationComponent.hotInstance;
            this._hotInstanceSolution = this.$refs.hotTableSolutionComponent.hotInstance;

            var instSpecification = this._hotInstanceSpecification;
            var instSolution = this._hotInstanceSolution;

            var self = this;

            instSolution.addHook('afterChange', function(){

                var mydata = self._hotInstanceSolution.getData();

                for (var i = 0; i < mydata.length; i++){
                    for(var j = 0; j < mydata[i].length; j++){
                        if(mydata[i][j].toString().indexOf('=') > -1){
                            mydata[i][j] = self._hotInstanceSolution.getDataAtCell (j, i);
                        }
                    }
                }

                self.solutionDataFormulaEvaluated = mydata;
            });


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

                            if (data.specification == "" || data.solution == "") {
                                return;
                            }

                            self.col = data.specification.col;
                            self.row = data.specification.row;
                            self.programming = data.specification.programming;
                            self.dataVisualization = data.specification.dataVisualization;


                            setTimeout(function(){
                                self.specificationData = data.specification.data;
                                self.specificationCode = data.specification.code;

                                self.solutionData = data.solution.data;
                                self.solutionCode = data.solution.code;
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
                this.specificationData = Handsontable.helper.createEmptySpreadsheetData(newVal, this.col);
                this.solutionData = Handsontable.helper.createEmptySpreadsheetData(newVal, this.col);
            },
            col: function(newVal, oldVal) {
                this.specificationData = Handsontable.helper.createEmptySpreadsheetData(this.row, newVal);
                this.solutionData = Handsontable.helper.createEmptySpreadsheetData(this.row, newVal);
            }
        },
        methods: {
            resetSpreadsheet: function() {
                this.specificationData = Handsontable.helper.createEmptySpreadsheetData(this.row, this.col);
            },
            syncData() {
                this.solutionData = JSON.parse(JSON.stringify(this.specificationData));
            }
        }
    }
</script>

