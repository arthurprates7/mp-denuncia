Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function getChartDataFretesCliente() {
    $.ajax({
        url: '/chart-fretes-cliente',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function (data) {
            renderChartFretesCliente(data);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

getChartDataFretesCliente();

function renderChartFretesCliente(data) {
    var ctx = document.getElementById("myBarChartFretesCliente");
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.nome),
            datasets: [{
                label: "Quantidade de Fretes",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: data.map(item => item.quantidade),
            }, {
                label: "Valor Total (R$)",
                backgroundColor: "#1cc88a",
                hoverBackgroundColor: "#17a673",
                borderColor: "#1cc88a",
                data: data.map(item => item.valor_total),
                yAxisID: 'y-axis-1'
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 10
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value;
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }, {
                    id: 'y-axis-1',
                    position: 'right',
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return 'R$ ' + number_format(value);
                        }
                    },
                    gridLines: {
                        display: false
                    }
                }],
            },
            legend: {
                display: true
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        if (tooltipItem.datasetIndex === 1) {
                            return datasetLabel + ': R$ ' + number_format(tooltipItem.yLabel);
                        } else {
                            return datasetLabel + ': ' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        }
    });
} 