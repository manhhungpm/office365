<template>
    <div ref="chart" class="highcharts"></div>
</template>

<script>
    import Highcharts from 'highcharts'
    import cloneDeep from 'lodash/cloneDeep'

    export default {
        name: "TheHighcharts",
        props: {
            title: {
                type: String,
                default: ''
            },
            categories: {
                type: Array,
                default: () => undefined
            },
            colors: {
                type: Array,
                default: () => ['#5867dd', '#f4516c', '#34bfa3', '#ffb822',
                    '#2f7ed8', '#0d233a', '#8bbc21', '#36a3f7', '#910000', '#1aadce',
                    '#492970', '#f28f43', '#716aca', '#77a1e5', '#c42525', '#a6c96a']
            },
            series: {
                type: Array,
                default: () => []
            },

            chartType: {
                type: String,
                default: 'pie'
            },

            chartHeight: {
                type: [String,Number],
                default: '500'
            },
            tooltipFormat: {
                type: String,
                default: ''
            },
            plotOptions: {
                type: Object,
                default: () => {
                }
            }
        },
        data() {
            return {
                chart: null
            }
        },
        mounted() {
            this.initChart()
        },
        methods: {
            initChart() {
                let series = cloneDeep(this.series)

                let options = {
                    credits: {
                        enabled: false
                    },
                    chart: {
                        backgroundColor: 'transparent',
                        type: this.chartType,
                        height: this.chartHeight
                    },
                    lang: {
                        drillUpText: 'Trở lại',
                        downloadCSV: 'Download định dạng CSV',
                        downloadJPEG: 'Download định dạng JPEG',
                        downloadPDF: 'Download định dạng PDF',
                        downloadPNG: 'Download định dạng PNG',
                        downloadSVG: 'Download định dạng SVG',
                        printChart: 'In biểu đồ',
                    },
                    colors: this.colors,
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        labels: {
                            enabled: false
                        }
                    },
                    yAxis: {
                        title: {
                            enabled: false
                        }
                    },

                    plotOptions: {
                        series: {
                            pointStart: 0,
                            cursor: 'pointer',
                            events: {
                            },
                            borderWidth: 0
                        },
                        areaspline: {
                            marker: {
                                enabled: false
                            }
                        }
                    },
                    legend: {

                    },
                    series: this.series,
                }

                Object.assign(options.plotOptions, this.plotOptions)

                this.chart = Highcharts.chart(this.$refs.chart, options);
            },
            exportChart(filename) {
                this.chart.exportChartLocal({
                    filename: filename,
                    type:'image/png',
                    sourceWidth: 1500,
                    sourceHeight: 500,
                    scale: 1,
                })
            }
        },
        watch: {
            series: {
                handler(val) {
                    let series = cloneDeep(val)
                    this.chart.update({
                        series: series
                    })
                },
                deep: true,
            },
            categories: {
                handler(val) {
                    this.chart.update({
                        xAxis: {
                            categories: val
                        }
                    })
                },
                deep: true,
            }
        }
    }
</script>
