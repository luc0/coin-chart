<template>
    <LineChart :chartData="chartData" class="chart" :options="chartOptions" />
</template>

<script>
import "../../../css/charts.scss";
import { defineComponent, ref, computed } from 'vue';
import { LineChart } from 'vue-chart-3';
import moment from 'moment';
import 'chartjs-adapter-moment';
import collect from 'collect.js';
import CommitsChartOptions from "@/Utils/Charts/CommitsChartOptions";

let PALETTE = ['#f3a683', '#f7d794', '#778beb', '#e77f67', '#cf6a87', '#786fa6', '#f8a5c2', '#63cdda', '#ea8685']

export default defineComponent({
    components: { LineChart },
    props: {
        chartGithubCommits: Array,
        chartGithubCommitsDates: Array,
        error: String,
    },
    setup(props) {
        const chartData = computed(() => {
            if (!props.chartGithubCommits) {
                return
            }

            // TODO: we use index 0: use only the dates of the first crypto this is ugly fix. We would need to have dates of ALL days, and return days of no commits for each crypto
            const dates = props.chartGithubCommitsDates[0]?.map((timestamp) => {
                return moment(timestamp)
            })
            console.log('props.chartGithubCommitsDates[0]', props.chartGithubCommitsDates[0])

            let datasets = [];
            let datasetCount = -1

            if (props.chartGithubCommits) {
                datasets = collect(props.chartGithubCommits).map((commitCount, index) => {
                    datasetCount++

                    return {
                        label: index,
                        data: commitCount,
                        borderColor: PALETTE[datasetCount],
                        backgroundColor: PALETTE[datasetCount],
                        tension: 0.1,
                    }
                }).toArray()
            }

            console.log('dates', dates, datasets)
            return {
                labels: dates,
                datasets: datasets,
            }
        });

        const chartOptions = ref(CommitsChartOptions)

        Object.map = function (obj, fn, ctx) {
            return Object.keys(obj).reduce((a, b) => {
                a[b] = fn.call(ctx || null, b, obj[b]);
                return a;
            }, {});
        };

        return {
            chartOptions,
            chartData,
        };
    },
});
</script>
