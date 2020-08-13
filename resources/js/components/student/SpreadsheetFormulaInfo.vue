<template>
    <button type="button" class="btn btn-warning mr-2" title="Help" @click="showFormulaInfo"><i class="fas fa-info"></i></button>
</template>

<script>

    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        methods: {
            showFormulaInfo() {
                Swal.fire({
                    icon: 'question',
                    title: 'Help',
                    html:
                        "<table class='table table-bordered'><tr><th>Description</th><th>Function</th></tr>"+
                        "<tr><td>Sum</td><td>=SUM(A1,A2,A3)<br>=SUM(A1:A3)</td></tr>" +
                        "<tr><td>Average</td><td>=AVERAGE(A1,A2,A3)</td></tr>" +
                        "<tr><td>Min Value</td><td>=MINA(A1,A2,A3)<br>=MINA(A1:A3)</td></tr>" +
                        "<tr><td>Max Value</td><td>=MAXA(A1,A2,A3)<br>=MAXA(A1:A3)</td></tr>" +
                        "<tr><td>Count</td><td>=COUNTA(A1,A2,A3)<br>=COUNTA(A1:A3)</td></tr>" +
                        "<tr><td>Value as number</td><td>INT(A1)</td></tr>" +
                        "</table>" +
                        "More <a href='https://formulajs.info/functions/' target='_blank'>formulas</a>"
                });
            },
        }
    }
</script>
