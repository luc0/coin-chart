<template>
    <LineChart :chartData="chartData"/>
    {{ currentRange }}
    {{ currentCoin }}
    <div>
        <a @click="changeRange('24h')" 
            :class="state.currentRange === '24h' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
            24h
        </a>
        <a @click="changeRange('7d')" 
            :class="state.currentRange === '7d' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="z-10 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#" >
            7d
        </a>
        <a @click="changeRange('1y')" 
            :class="state.currentRange === '1y' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
            1y
        </a>
    </div>

    <div>
        <a @click="changeCoin('BTC')" 
            :class="state.currentCoin === 'BTC' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
            Bitcoin
        </a>
        <a @click="changeCoin('ETH')" 
            :class="state.currentCoin === 'ETH' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="z-10 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#" >
            Ethereum
        </a>
        <a @click="changeCoin('ADA')" 
            :class="state.currentCoin === 'ADA' ? 'bg-indigo-50 border-indigo-500' : null" 
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
            Cardano
        </a>
    </div>

    <p v-if="error" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
        {{ error }}
    </p>
</template>

<script>
    import { defineComponent, ref, computed, reactive } from 'vue';
    import { LineChart } from 'vue-chart-3';
    import { Inertia } from '@inertiajs/inertia';

    export default defineComponent({
        components: { LineChart },
        props: {
            prices: Array,
            time: Array,
            error: String,
        },
        setup(props) {
            const state = reactive({
                currentCoin: 'BTC',
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
                Inertia.post('/', {'range': state.currentRange, 'coin': state.currentCoin})
            }

            return { chartData, changeRange, changeCoin, updateChart, state };
        },
    });
</script>
