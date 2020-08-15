<template>
    <button type="button" class="btn btn-warning mr-2" :title="$t('Help')" @click="showProgrammingInfo"><i class="fas fa-info"></i></button>
</template>

<script>

    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        methods: {
            showProgrammingInfo() {
                Swal.fire({
                    icon: 'question',
                    title: this.$t('Help'),
                    html:
                        this.$t('You can use all the features of the JavaScript language.') + "<hr>" +
                        "<table class='table table-bordered'><tr><th>" + this.$t('Description') + "</th><th>" + this.$t('Function') + "</th></tr>"+
                        "<tr><td>" + this.$t('Read data from cell') + "</td><td>getData('A1')</td></tr>" +
                        "<tr><td>" + this.$t('Write data to cell') + "</td><td>setData('A1', value)</td></tr>" +
                        "<tr><td>" + this.$t('Calculate the sum of given cells') + "</td><td>sum('A1', 'A2', ...)</td></tr>" +
                        "<tr><td>" + this.$t('Calculate the mean of given cells') + "</td><td>mean('A1', 'A2', ...)</td></tr>" +
                        "<tr><td>" + this.$t('Returns the value of the smallest number passed') + "</td><td>min('A1', 'A2', ...)</td></tr>" +
                        "<tr><td>" + this.$t('Returns the value of the largest number passed') + "</td><td>max('A1', 'A2', ...)</td></tr>" +
                        "</table>",
                });
            },
        }
    }
</script>
