<script>
    import { Doughnut } from 'vue-chartjs'

    export default {
        extends: Doughnut,
        props: {
            chartdata: {
                type: Object,
                default: null,
                required: true
            }
        },
        data () {
            return {
                options: {
                    legend: {
                        display: false
                    },
                    responsive: false,
                    maintainAspectRatio: true,
                    title: {
                        display: true,
                        text: ''
                    }
                }
            }
        },
        mounted () {

            var fixedLabels = [];
            var fixedData = [];
            var totalParticipants = 0;
            var participants = 0;
            var unratedParticipants = 0;

            for (var data in this.chartdata) {
                var tmp = this.chartdata[data];

                fixedData.push(tmp.length);
                fixedLabels.push(this.$t('Score') + " (" + data + ")");

                totalParticipants = tmp[0].participantsCount;
                participants += tmp.length;

                // Set title of chart
                this.options.title.text = tmp[0].topic + " - " + tmp[0].task;
            }

            // Add all the unrated students to the chart
            unratedParticipants = totalParticipants - participants;
            if (unratedParticipants > 0) {
                fixedData.push(unratedParticipants);
                fixedLabels.push(this.$t('Unfinished'));
            }


            this.renderChart({
                labels: fixedLabels,
                datasets: [{
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        data: fixedData
                    }]}, this.options);
        }
    }
</script>
