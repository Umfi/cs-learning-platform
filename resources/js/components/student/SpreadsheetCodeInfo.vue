<template>
    <button type="button" class="btn btn-warning mr-2" title="Help" @click="showProgrammingInfo"><i class="fas fa-info"></i></button>
</template>

<script>

    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        methods: {
            showProgrammingInfo() {
                Swal.fire({
                    icon: 'question',
                    title: 'Help',
                    html:
                        "You can use all the features of the JavaScript language. <hr>" +
                        "<table class='table table-bordered'><tr><th>Description</th><th>Function</th></tr>"+
                        "<tr><td>Read data from cell</td><td>getData('A', 1)</td></tr>" +
                        "<tr><td>Write data to cell</td><td>setData('A', 1, value)</td></tr>" +
                        "</table>",
                });
            },
        }
    }
</script>
