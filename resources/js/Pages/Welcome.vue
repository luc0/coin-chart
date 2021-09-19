<template>
    <a href="/" class="nav-button w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-300 text-base font-medium text-black hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
        Coin List
    </a>

    <LineChart :chartData="chartData" class="chart"/>

    <div class="chart-filters">
        <div class="filter-group">
            <FilterRange :value="'24h'" :currentValue="state.currentRange" @changeRange="changeRange($event)" :side="'l'" />
            <FilterRange :value="'7d'" :currentValue="state.currentRange"  @changeRange="changeRange($event)" />
            <FilterRange :value="'1y'" :currentValue="state.currentRange" @changeRange="changeRange($event)" :side="'r'"/>
        </div>
        <div class="filter-group">
            <FilterCoin :value="'BTC'" :currentValue="state.currentCoin" @changeCoin="changeCoin($event)" :side="'l'" />
            <FilterCoin :value="'ETH'" :currentValue="state.currentCoin"  @changeCoin="changeCoin($event)" />
            <FilterCoin :value="'ADA'" :currentValue="state.currentCoin" @changeCoin="changeCoin($event)" :side="'r'"/>
        </div>
    </div>

    <p v-if="error" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
        {{ error }}
    </p>
</template>

<script>
    import { defineComponent, ref, computed, reactive } from 'vue';
    import { LineChart } from 'vue-chart-3';
    import FilterRange from '../Components/Filters/FilterRange.vue';
    import FilterCoin from '../Components/Filters/FilterCoin.vue';
    import { Inertia } from '@inertiajs/inertia';

    export default defineComponent({
        components: { LineChart, FilterRange, FilterCoin },
        props: {
            prices: Array,
            time: Array,
            error: String,
            currentCoin: String
        },
        setup(props) {
            const state = reactive({
                currentCoin: props.currentCoin,
                currentRange: '1y',
            });

            const chartData = computed(() => ({
                labels: props.time,
                datasets: [
                    {
                        label: state.currentCoin,
                        data: props.prices,
                        borderColor: '#74b9ff',
                        backgroundColor: '#74b9ff',
                        tension: 0.4
                    },
                ],
            }));

            function changeRange(range) {
                state.currentRange = range;
                this.updateChart();
            }

            function changeCoin(coin) {
                state.currentCoin = coin;
                this.updateChart();
            }
            
            function updateChart() {
                Inertia.post('/coin/' + state.currentCoin, {'range': state.currentRange})
            }

            return { chartData, changeRange, changeCoin, updateChart, state };
        },
    });
</script>

<style>
    .nav-button {
        margin: 20px;
    }

    .chart {
        width: 80%;
        margin: 100px auto;
    }

    .chart-filters {
        height: 90px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
    }
    
</style>