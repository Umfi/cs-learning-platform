<template>
    <div>
        <div class="form-group">
            <label>{{ $t('Add new tip:') }}</label>
            <div class="form-inline">
                <input type="text" class="col-11 form-control" v-model="newTip" v-on:keypress.enter="addTip()">
                <button type="button" class="btn btn-primary ml-2" @click="addTip()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <hr>
        <ul class="list-group">
            <li class="list-group-item" v-for="(tip, ind) in tips" v-bind:key="ind">
                <span>{{tip}}</span>
                <button type="button" class="btn btn-danger btn-sm float-right" @click="removeTip(tip)"><i class="fa fa-trash"></i></button>
            </li>
        </ul>
    </div>
</template>

<script>

    export default {
        props: ["taskid"],
        data() {
            return {
                tips: [],
                newTip: ''
            };
        },
        async mounted() {
            var self = this;
            axios.get("/teacher/getTask/" + this.$props.taskid)
                .then(response => {
                    if (response.data.task) {

                        if (typeof response.data.task.tips === "undefined") {
                            return;
                        }

                        self.tips = response.data.task.tips;
                    }
                }).catch(function (error) {
                    console.error(error);
             });
        },
        methods: {
            addTip() {
                if (this.tips === "")
                    this.tips = [];

                if (this.newTip.trim() !== '')
                    this.tips.push(this.newTip);
                this.newTip = '';
            },
            removeTip(tip) {
                this.tips = this.tips.filter(e => e !== tip)
            }
        }
    }
</script>
