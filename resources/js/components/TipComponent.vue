<template>
    <div>
        <div class="form-group">
            <label>Add new tip:</label>
            <input type="text" class="form-control" v-model="newTip" v-on:keypress.enter="addTip()">
        </div>
        <hr>
        <ul class="list-group">
            <li class="list-group-item" v-for="(tip, ind) in storedTips" v-bind:key="ind">
                <span>{{tip}}</span>
                <button type="button" class="btn btn-danger btn-sm float-right" @click="removeTip(tip)"><i class="fa fa-trash"></i></button>
            </li>
        </ul>
    </div>
</template>

<script>

    export default {
        props: ["tips"],
        data() {
            return {
                storedTips: [],
                newTip: ''
            };
        },
        mounted() {
            this.storedTips = this.$props.tips;
        },
        methods: {
            addTip() {
                if (this.newTip.trim() != '')
                    this.storedTips.push(this.newTip);
                this.newTip = '';
            },
            removeTip(tip) {
                this.storedTips = this.storedTips.filter(e => e !== tip)
            }
        }
    }
</script>
