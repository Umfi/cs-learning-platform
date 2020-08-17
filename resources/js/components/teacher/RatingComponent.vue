<template>
    <div class="d-inline">
        <button type="button" class="border-dark btn btn-warning" :title="$t('Ratings')" @click="showRating()"><i class="far fa-star"></i></button>

        <!-- Course Rating Modal -->
        <div class="modal" :id="'courseRatingModal-' + course">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t("Course Rating") }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row" style="max-height: 600px; overflow-y: auto;">
                            <div v-if="loaded" v-for="rating in ratings" class="col-12">
                                <rating-chart :chartdata="rating" class="border-0 card"></rating-chart>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import RatingChart from "./RatingChart";

    export default {
        props: ["course"],
        components: {
            RatingChart
        },
        data() {
            return {
                loaded: false,
                ratings: [],
            };
        },
        async mounted() {
            var self = this;
            axios.get("/teacher/getAllRatingsFromCourse/" + this.$props.course)
                .then(response => {
                    if (response.data.ratings) {
                        self.ratings = response.data.ratings;
                        self.loaded = true;
                    }
                }).catch(function (error) {
                    console.error(error);
                });
        },
        methods: {
            showRating() {
                $('#courseRatingModal-' + this.$props.course).modal('show');
            }
        }
    }
</script>
