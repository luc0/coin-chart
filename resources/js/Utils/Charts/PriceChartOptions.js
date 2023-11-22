import moment from "moment";

const PriceChartOptions = (filterRange) => {
    return {
        responsive: true,
        elements: {
            point: {
                radius: 0
            }
        },
        scales: {
            x: {
                type: 'time',
                gridLines: {
                    display: false
                },
                time: {
                    // unit: 'day',
                    // tooltipFormat: 'MMM DD',
                    minUnit: 'minute',
                    // stepSize: 10,
                    displayFormats: {
                        hour: 'YYYY/MM/DD HH:mm:ss',
                        day: 'YYYY/MM/DD',
                        month: 'YYYY/MM/DD',
                        year: 'YYYY/MM/DD',
                    },
                },
                ticks: {
                    callback: function (value, index, values) {
                        // Muestra el nombre del mes cuando hay un cambio de mes
                        const currentMoment = moment(value);
                        const previousMoment = index > 0 ? moment(values[index - 1].value) : null;

                        if (previousMoment && currentMoment.year() !== previousMoment.year()) {
                            return currentMoment.format('YYYY');
                        } else {
                            if (previousMoment && currentMoment.month() !== previousMoment.month()) {
                                return currentMoment.format('MMMM');
                            } else {

                                if (previousMoment && currentMoment.day() !== previousMoment.day()) {
                                    return currentMoment.format('ddd DD');
                                } else {
                                    return currentMoment.format('HH a');

                                }
                            }
                        }
                    },
                },
            },
            y: {
                ticks: {
                    callback: function (value, index, values) {
                        return value + '%';
                    }
                }
            }
        }
    }
}

export default PriceChartOptions
