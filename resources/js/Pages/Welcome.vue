<template>
    <div class="container">
        <div class="head">
            <!-- <a href="/coin" class="nav-button w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-300 text-base font-medium text-black hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                Coin List
            </a> -->
            {{ grouped }}
            <div class="coin-filter">
                <div class="selected-coins flex">
                    <div v-for="(coinItem, key) in selectedCoins">
                            <img @click="removeCoin(coinItem)" class="coin h-10 w-10 rounded-full" 
                                :style="'right: -' + (20 * key) + 'px; z-index:' + (100 - key)" :src="coinItem.iconUrl" alt="">
                    </div>
                </div>

                <Search :class="'select-coin'" :list="filterableCoinsList" @add-coin="addCoin"/>
                Agrupar <input type="checkbox" :checked="grouped" @click="changeGrouped()">
            </div>
        </div>
    
        <div class="chart-filters">
            <div class="filter-range">
                <FilterRange v-for="rangeItem in filterRangeList" :value="rangeItem" :currentValue="filterRange" @changeRange="changeRange($event)" />
            </div>
        </div>

        <LineChart :chartData="chartData" class="chart" :options="chartOptions" />

        <p v-if="error" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            {{ error }}
        </p>
    </div>
</template>

<script>
    import { defineComponent, ref, computed, reactive, toRefs } from 'vue';
    import { LineChart } from 'vue-chart-3';
    import FilterRange from '../Components/Filters/FilterRange.vue';
    import FilterCoin from '../Components/Filters/FilterCoin.vue';
    import Search from '../Components/Filters/Search.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { usePage } from '@inertiajs/inertia-vue3'
    import { months } from '../utils'
    import moment from 'moment';
    import 'chartjs-adapter-moment';
    import collect from 'collect.js';

    let PALETTE = ['#f3a683', '#f7d794', '#778beb', '#e77f67', '#cf6a87', '#786fa6', '#f8a5c2', '#63cdda', '#ea8685']

    export default defineComponent({
        components: { LineChart, FilterRange, FilterCoin, Search },
        props: {
            chartPrices: Array,
            chartDates: Array,
            error: String,
            coinsList: Array, 
            filterRangeList: Array,
            grouped: Boolean
        },
        setup(props) {
            const state = reactive({
                filterRange: '3m',
                selectedCoins: usePage().props.value.coinsSelected,
                grouped: props.grouped
            });

            const filterableCoinsList = computed(() => {
                return props.coinsList.filter((coin) => {
                    let isPresent = false
                    state.selectedCoins.forEach((current) => {
                        if(current.symbol == coin.symbol) isPresent = true
                    });
                    return !isPresent
                })
            })
            
            const chartData = computed(() => {
                const dates = props.chartDates.map((timestamp) => {
                    return moment.unix(timestamp)
                })

                let datasets = [];
                let datasetCount = -1

                if (props.chartPrices) {
                    datasets = collect(props.chartPrices).map((prices, index) => {
                        datasetCount++
                        console.log(datasetCount)
                        return {
                            label: index,
                            data: prices,
                            borderColor: PALETTE[datasetCount],
                            backgroundColor: PALETTE[datasetCount],
                            tension: 0.4
                        }
                        
                    }).toArray()
                }
                
                return {
                    labels: dates,
                    datasets: datasets,
                }
            });

            const chartOptions = ref({
                responsive: true,
                elements: {
                    point:{
                        radius: 0
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        gridLines: {
                            display:false
                        },
                        time: {
                            minUnit: 'minute',
                            stepSize: 10,
                        }
                    },
                    y: {
                        ticks: {
                            callback: function(value, index, values) {
                                return value + '%';
                            }
                        }
                    }
                }
            })

            function changeRange(range) {
                state.filterRange = range;
                this.updateChart();
            }

            function addCoin(coin) 
            {
                state.selectedCoins.push(coin);
                updateChart();
            }

            function removeCoin(removeCoin) 
            {
                state.selectedCoins = state.selectedCoins.filter((coin) => (
                    coin.symbol != removeCoin.symbol
                ));
                updateChart();
            }

            function changeGrouped()
            {
                state.grouped = !state.grouped
                updateChart()
            }
            
            function updateChart() {
                Inertia.post('/', {
                    'coins': state.selectedCoins, 
                    'range': state.filterRange,
                    'grouped': state.grouped
                })
            }

            Object.map = function (obj, fn, ctx) {
                return Object.keys(obj).reduce((a, b) => {
                    a[b] = fn.call(ctx || null, b, obj[b]);
                    return a;
                }, {});
            };

            return { chartOptions, chartData, changeRange, changeGrouped, updateChart, ...toRefs(state), addCoin, removeCoin, filterableCoinsList };
        },
    });
</script>

<style>
    body {
        color: #333;    
    }

    .container {
        margin: 0 auto;
        padding: 0 20px;
    }

    .head {
        display: flex;
        padding: 20px 0 0 50px;
        flex-direction: row-reverse;
    }

    .chart {
        height: 400px;
        margin: 0 auto;
    }

    @media (max-width: 800px) {
        .chart {
            height: 200px;
        }
    }

    .chart-filters {
        display: flex;
        justify-content: flex-end;
        height: 90px;
        align-items: center;
    }

    .coin {
        background: white;
        border: 1px solid #ddd;
        padding: 2px;
        position: relative;
        animation-name: moveDown;
        animation-duration: 0.3s;
    }

    .coin:hover {
        cursor: pointer;
        margin-top: -5px;
        animation-name: moveUp;
        animation-duration: 0.3s;
    }

    @keyframes moveUp {
        from {margin-top: 0px;}
        to {margin-top: -5px;}
    }

    @keyframes moveDown {
        from {margin-top: -5px;}
        to {margin-top: 0px;}
    }

    .nav-button {
        margin-right: 80px;
        background: #f3f3f3;
        border: 1px solid #ddd;
        line-height: 24px;
        color: #333;
    }

    .coin-filter {
        display: flex;
        justify-content: space-between;
    }

    .selected-coins {
        width: 145px;
        margin-right: 15px;
        flex-direction: row-reverse;
    }

    .selected-coins .coin-item .remove-coin {
        display: none;
        background: #d85757;
        color: white;
        border-radius: 50px;
        padding: 0 7px;
        position: absolute;
        z-index: 9999;
        left: -3px;
        top: 20px;
        font-size: 10px;
    }

    .selected-coins .coin-item:hover .remove-coin {
        display: block;
    }
    
</style>