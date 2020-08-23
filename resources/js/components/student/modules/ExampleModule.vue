<template>
    <!-- TODO: Custom HTML here -->
</template>

<script>

    export default {
        props: ["taskid", "taskdata"],
        data() {
            return {
                example: null,
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _example: null,
            };
        },
        mounted() {
            var self = this;

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                var data = self.$props.taskdata;
                self.example = data.specification.example;
            });
        },
        methods: {
            exampleFunc() {

            },
            /**
             * Called before global store method
             */
            _preStore() {

            },
        }
    }
</script>

