export default {
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
}
