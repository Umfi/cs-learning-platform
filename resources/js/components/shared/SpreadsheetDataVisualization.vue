<template>
    <div class="mt-2">

        <div class="form-inline">
            <select class="form-control mr-2" v-model="type" @change="updateChart()">
                <option value="line">{{ $t('Line Chart') }}</option>
                <option value="bar">{{ $t('Bar Chart') }}</option>
                <option value="pie">{{ $t('Pie Chart') }}</option>
            </select>
            <button type="button" class="btn btn-sm form-control btn-success mr-2" @click="addDataset()">{{ $t('Add Dataset') }}</button>
            <button type="button" class="btn btn-sm form-control btn-danger mr-2" @click="resetChart()"><i class="fa-trash fas"></i> {{ $t('Reset') }}</button>
            <button type="button" :title="$t('Help')" class="btn btn-warning mr-2" @click="showHelp()"><i class="fas fa-info"></i></button>
        </div>
         <hr>
        <canvas v-bind:id="'chart-' + modalType + '-' + taskid"></canvas>
    </div>
</template>

<script>

    import Chart from 'chart.js';
    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        props: ["taskid", "modalType"],
        data() {
            return {
                _ctx: null,
                _chart: null,
                type: "bar",
                data: {
                    labels: [],
                    datasets: []
                },
                _hot: null,
                _lab: 1,
                _options: {
                    responsive: true,
                    lineTension: 1,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            }
        },
        async mounted() {

            this.$data._hot = this.$parent.$refs.hotTableSolutionComponent.hotInstance;

            this.$data._ctx = document.getElementById('chart-' + this.$props.modalType + '-' + this.$props.taskid);
            this.$data._chart = new Chart(this.$data._ctx, {
                type: this.type,
                data: this.data,
                options: this.$data._options
            });
        },
        methods: {
            updateChart() {
                this.$data._chart.destroy();
                this.$data._chart = new Chart(this.$data._ctx, {
                    type: this.type,
                    data: this.data,
                    options: this.$data._options
                });
            },
            addDataset() {
                var self = this;

                var dynamicColors = function() {
                    var r = Math.floor(Math.random() * 255);
                    var g = Math.floor(Math.random() * 255);
                    var b = Math.floor(Math.random() * 255);
                    return "rgb(" + r + "," + g + "," + b + ")";
                };

                var selection = this.$data._hot.getSelected();

                if  (typeof selection === "undefined") {

                    Swal.fire({
                        icon: 'error',
                        title: self.$t('Error'),
                        text: self.$t('No data in table selected.')
                    });

                    return;
                }

                for (var i = 0; i < selection.length; i += 1) {

                    var item = selection[i];
                    var startRow = Math.min(item[0], item[2]);
                    var endRow = Math.max(item[0], item[2]);
                    var startCol = Math.min(item[1], item[3]);
                    var endCol = Math.max(item[1], item[3]);

                    if (startRow < 0) {
                        startRow = 0;
                    }
                    if (startCol < 0) {
                        startCol = 0;
                    }

                    if ((startCol == endCol) && (startRow == endRow)) {
                        Swal.fire({
                            icon: 'error',
                            title: self.$t('Error'),
                            text: self.$t('Not enough data selected.')
                        });
                        return;
                    }

                    var label = this.$data._hot.getDataAtCell(startRow, startCol);
                    var newDataset = {
                        label: label,
                        backgroundColor: dynamicColors(),
                        data: [],
                        borderWidth: 3
                    };

                    for (var rowIndex = startRow+1; rowIndex <= endRow; rowIndex += 1) {
                        for (var columnIndex = startCol; columnIndex <= endCol; columnIndex += 1) {
                            var val = this.$data._hot.getDataAtCell(rowIndex, columnIndex);
                            newDataset.data.push(val);
                        }
                    }

                    this.data.labels.push(this.$data._lab);
                    this.data.datasets.push(newDataset);
                    this.$data._chart.update();
                    this.$data._lab += 1;
                }
            },
            resetChart() {
                this.data = {
                    labels: [],
                    datasets: []
                };
                this.$data._lab = 1;
                this.updateChart();
            },
            showHelp() {
                Swal.fire({
                    icon: 'question',
                    title: this.$t('Help'),
                    html:
                        "<p>" + this.$t('To add a record, you must select the data in the table. The value in the cell that was selected first will be used as the label.') + "</p>"
                });
            }
        }
    }
</script>
