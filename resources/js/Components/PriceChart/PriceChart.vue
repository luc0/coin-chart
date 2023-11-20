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
import priceChartOptions from "@/Utils/Charts/PriceChartOptions";

let PALETTE = ['#f3a683', '#f7d794', '#778beb', '#e77f67', '#cf6a87', '#786fa6', '#f8a5c2', '#63cdda', '#ea8685']

export default defineComponent({
    components: { LineChart },
    props: {
        chartPrices: Array,
        chartDates: Array,
        error: String,
    },
    setup(props) {
        const chartData = computed(() => {
            const dates = props.chartDates.map((timestamp) => {
                return moment(timestamp).format("YYYY-MM-DDTHH:mm:ss");
            })

            let datasets = [];
            let datasetCount = -1

            if (props.chartPrices) {
                datasets = collect(props.chartPrices).map((prices, index) => {
                    datasetCount++

                    return {
                        label: index,
                        data: prices,
                        borderColor: PALETTE[datasetCount],
                        backgroundColor: PALETTE[datasetCount],
                        tension: 0.4,
                    }
                }).toArray()
            }

            return {
                labels: dates,
                datasets: datasets,
            }
        });

        const chartOptions = ref(priceChartOptions)

        return {
            chartOptions,
            chartData,
        };
    },
});
</script>
