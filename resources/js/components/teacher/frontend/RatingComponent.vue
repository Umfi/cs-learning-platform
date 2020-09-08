<template>
    <div class="d-inline">
        <button type="button" class="border-dark btn btn-warning" :title="$t('Ratings')" @click="showRating()"><i class="far fa-star"></i></button>

        <!-- Course Rating Modal -->
        <div class="modal" :id="'courseRatingModal-' + course">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t("Course Rating") }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center">
                            <div :id="'carouselControls-' + course" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div v-if="loaded" v-for="(rating, key, index) in ratings" v-bind:key="index" :class="['carousel-item', (index === 0 ? 'active' : '')]">
                                        <rating-chart :chartdata="rating" class="border-0 card col"></rating-chart>
                                    </div>
                                    <div v-if="!loaded">
                                        <p>{{ $t("No ratings available.") }}</p>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" :href="'#carouselControls-' + course" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" :href="'#carouselControls-' + course" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>

                        <div class="row-cols-1" v-if="loaded">
                            <table :id="'ratingTable-' + course" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>{{ $t("Task") }}</th>
                                    <th>{{ $t("Topic") }}</th>
                                    <th>{{ $t("Student") }}</th>
                                    <th>{{ $t("Score") }}</th>
                                    <th>{{ $t("Max Score") }}</th>
                                    <th>{{ $t("Used Tips") }}</th>
                                    <th>{{ $t("Time Needed") }}</th>
                                    <th>{{ $t("Solve Attempts") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="rating in all">
                                    <td>{{ rating.task }}</td>
                                    <td>{{ rating.topic }}</td>
                                    <td>{{ rating.studentName }}</td>
                                    <td>{{ rating.score }}</td>
                                    <td>{{ rating.score_max }}</td>
                                    <td>{{ rating.used_tips }}</td>
                                    <td>{{ rating.required_time }}</td>
                                    <td>{{ rating.solve_attempts }}</td>
                                </tr>
                                </tbody>
                            </table>
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
                all: []
            };
        },
        async mounted() {
            var self = this;
            axios.get("/teacher/getAllRatingsFromCourse/" + this.$props.course)
                .then(response => {
                    if (response.data.ratings) {
                        self.ratings = response.data.ratings;
                        self.all = response.data.all;

                        self.loaded = response.data.ratings.length != 0;

                        $('.carousel').carousel({
                            interval: 2000
                        });



                    }
                }).catch(function (error) {
                    console.error(error);
                });


        },
        methods: {
            showRating() {
                $('#courseRatingModal-' + this.$props.course).modal('show');
                $('#ratingTable-' + this.$props.course).DataTable();
            }
        }
    }
</script>
