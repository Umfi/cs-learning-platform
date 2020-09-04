<template>
    <button type="button" class="btn btn-warning mr-2" :title="$t('Help')" @click="showFormulaInfo"><i class="fas fa-info"></i></button>
</template>

<script>

    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        methods: {
            showFormulaInfo() {
                Swal.fire({
                    icon: 'question',
                    title: this.$t('Help'),
                    html:
                        "<table class='table table-bordered'><tr><th>" + this.$t('Description') + "</th><th>" + this.$t('Function') + "</th></tr>"+
                        "<tr><td>" + this.$t('Sum') + "</td><td>=SUMME(A1,A2,A3)<br>=SUMME(A1:A3)</td></tr>" +
                        "<tr><td>" + this.$t('Average') + "</td><td>=DURCHSCHNITT(A1,A2,A3)</td></tr>" +
                        "<tr><td>" + this.$t('Minimum Value') + "</td><td>=MINA(A1,A2,A3)<br>=MINA(A1:A3)</td></tr>" +
                        "<tr><td>" + this.$t('Maximum Value') + "</td><td>=MAXA(A1,A2,A3)<br>=MAXA(A1:A3)</td></tr>" +
                        "<tr><td>" + this.$t('Count') + "</td><td>=ZAEHLEN(A1,A2,A3)<br>=ZAEHLEN(A1:A3)</td></tr>" +
                        "<tr><td>" + this.$t('Value as number') + "</td><td>INT(A1)</td></tr>" +
                        "</table>" +
                        this.$t('More') + " <a href='https://formulajs.info/functions/' target='_blank'>" + this.$t('formulas') + "</a>"
                });
            },
        }
    }
</script>
