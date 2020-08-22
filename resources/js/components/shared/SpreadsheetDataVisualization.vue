<template>
    <div class="mt-2">

        <div class="form-inline">
            <select class="form-control mr-2" v-model="type" @change="updateChart()">
                <option value="line">{{ $t('Line Chart') }}</option>
                <option value="bar">{{ $t('Bar Chart') }}</option>
                <option value="pie">{{ $t('Pie Chart') }}</option>
            </select>
            <button type="button" class="btn btn-sm form-control btn-success mr-2" @click="addData()">{{ $t('Add Data') }}</button>
            <button type="button" class="btn btn-sm form-control btn-success mr-2" @click="addDataset()">{{ $t('Add Dataset') }}</button>
            <button type="button" class="btn btn-sm form-control btn-danger mr-2" @click="resetChart()"><i class="fa-trash fas"></i> {{ $t('Reset') }}</button>
        </div>
         <hr>
        <canvas v-bind:id="'chart-' + taskid"></canvas>
    </div>
</template>

<script>

    import Chart from 'chart.js';
    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        props: ["taskid", "griddata"],
        data() {
            return {
                _ctx: null,
                _chart: null,
                type: "bar",
                data: {
                    labels: [],
                    datasets: []
                },
                _options: {
                    responsive: true,
                    lineTension: 1,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                padding: 25,
                            }
                        }]
                    }
                }
            }
        },
        async mounted() {
            this._ctx = document.getElementById("chart-" + this.$props.taskid);
            this._chart = new Chart(this._ctx, {
                type: this.type,
                data: this.data,
                options: this._options
            });
        },
        methods: {
            updateChart() {
                this._chart.destroy();
                this._chart = new Chart(this._ctx, {
                    type: this.type,
                    data: this.data,
                    options: this._options
                });
            },
            async addData() {
                var self = this;

                const { value: newLabel } = await Swal.fire({
                    title: self.$t('Add Data'),
                    text: self.$t('Select a field like A1. Or create multiple A1, B1, ...'),
                    input: 'text',
                    inputPlaceholder: 'A1',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return self.$t('You need to write something!');
                        }
                    }
                })

                if (newLabel) {

                    var parts = newLabel.split(",").map(function(item) {
                        return item.trim();
                    });

                    for (var index = 0; index < parts.length; ++index) {

                        var label = this._extractDataFromGrid(parts[index]);

                        if (label) {
                            this.data.labels.push(label);
                        }
                    }

                    this._chart.update();
                }
            },
            async addDataset() {
                var self = this;

                var dynamicColors = function() {
                    var r = Math.floor(Math.random() * 255);
                    var g = Math.floor(Math.random() * 255);
                    var b = Math.floor(Math.random() * 255);
                    return "rgb(" + r + "," + g + "," + b + ")";
                };

                const { value: formValues } = await Swal.fire({
                    title: self.$t('Add Dataset'),
                    html:
                        self.$t('Label') + ': <input id="swal-input1" class="swal2-input" placeholder="A1">' +
                        self.$t('Data') + ': <input id="swal-input2" class="swal2-input" placeholder="A2,A3,A4,A5,...">',
                    focusConfirm: false,
                    showCancelButton: true,
                    preConfirm: () => {
                        return [
                            document.getElementById('swal-input1').value,
                            document.getElementById('swal-input2').value
                        ]
                    }
                })

                if (formValues) {
                    let label = this._extractDataFromGrid(formValues[0]);

                    if (label) {
                        var newDataset = {
                            label: label,
                            backgroundColor: dynamicColors(),
                            data: [],
                            borderWidth: 3
                        };

                        var parts = formValues[1].split(",").map(function(item) {
                            return item.trim();
                        });

                        for (var index = 0; index < parts.length; ++index) {

                            var data = this._extractDataFromGrid(parts[index]);

                            if (data) {
                                newDataset.data.push(data);
                            }
                        }

                        this.data.datasets.push(newDataset);
                        this._chart.update();
                    }
                }
            },
            resetChart() {
                this.data = {
                    labels: [],
                    datasets: []
                };
                this.updateChart();
            },
            _extractDataFromGrid(text) {
                if (text === "") {
                    return false;
                }

                // is first char a letter
                if  (!text.charAt(0).match(/[a-z]/i)) {
                    return false;
                }

                var col = text.toLowerCase().charCodeAt(0) - 97;
                var _row = text.substring(1) - 1;

                // is rest of text a number
                if (Number.isInteger(_row)) {
                    var row = parseInt(_row);
                } else {
                    return false;
                }

                return this.$props.griddata[row][col];
            }
        }
    }
</script>
