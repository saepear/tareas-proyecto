(function () {
    let chartInstances = [];

    const THEME_COLORS = {
        light: {
            pending: '#e8b830',
            inProgress: '#4a8fe0',
            completed: '#00c896',
            gridLine: '#d0d5e0',
            text: '#3d4468'
        },
        dark: {
            pending: '#f0c850',
            inProgress: '#6aafe8',
            completed: '#2ed8a0',
            gridLine: '#383b44',
            text: '#e8eaf0'
        }
    };

    const colorMap = {
        'pending': 'pending',
        'in-progress': 'inProgress',
        'completed': 'completed'
    };

    function getColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return isDark ? THEME_COLORS.dark : THEME_COLORS.light;
    }

    function assignColors(data, keys) {
        const colors = getColors();
        return data.map(function (d) {
            const cpy = structuredClone(d);
            const key = colorMap[d.colorKey];
            cpy.color = colors[key] || colors.pending;
            return cpy;
        });
    }

    function assignSeriesColors(series) {
        const colors = getColors();
        return series.map(function (s) {
            const key = colorMap[s.colorKey];
            s.color = colors[key] || colors.pending;
            return s;
        });
    }

    globalThis.initCharts = function (data) {
        const colors = getColors();

        chartInstances[0] = Highcharts.chart('chart-donut', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent'
            },
            title: { text: null },
            credits: { enabled: false },
            tooltip: {
                pointFormat: '<b>{point.y}</b> tareas ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '55%',
                    borderWidth: 0,
                    dataLabels: [{
                        format: '{point.name}',
                        connectorColor: colors.text,
                        style: { color: colors.text, textOutline: 'none' }
                    }, {
                        format: '{point.percentage:.0f}%',
                        distance: -30,
                        style: { fontSize: '0.9em', textOutline: 'none', color: '#ffffff' }
                    }]
                }
            },
            series: [{
                name: 'Tareas',
                data: assignColors(data.donut)
            }]
        });

        chartInstances[1] = Highcharts.chart('chart-weekly', {
            chart: {
                type: 'column',
                backgroundColor: 'transparent'
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                categories: data.weekly.categories,
                labels: { style: { color: colors.text } },
                lineColor: colors.gridLine,
                tickColor: colors.gridLine
            },
            yAxis: {
                min: 0,
                title: { text: null },
                gridLineColor: colors.gridLine,
                labels: { style: { color: colors.text } }
            },
            legend: {
                itemStyle: { color: colors.text },
                symbolRadius: 3
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br/>',
                pointFormat: '{series.name}: <b>{point.y}</b> tareas'
            },
            plotOptions: {
                column: {
                    borderRadius: 4,
                    borderWidth: 0
                }
            },
            series: assignSeriesColors(data.weekly.series)
        });

        chartInstances[2] = Highcharts.chart('chart-monthly', {
            chart: {
                type: 'bar',
                backgroundColor: 'transparent'
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                categories: data.monthly.categories,
                labels: { style: { color: colors.text } },
                lineColor: colors.gridLine,
                tickColor: colors.gridLine
            },
            yAxis: {
                min: 0,
                title: { text: null },
                gridLineColor: colors.gridLine,
                labels: { style: { color: colors.text } }
            },
            legend: {
                itemStyle: { color: colors.text },
                symbolRadius: 3,
                reversed: true
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br/>',
                pointFormat: '{series.name}: <b>{point.y}</b> tareas'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderWidth: 0
                }
            },
            series: assignSeriesColors(data.monthly.series)
        });
    };

    globalThis.updateChartTheme = function () {
        chartInstances.forEach(function (chart) {
            if (chart) chart.destroy();
        });
        chartInstances = [];
        if (globalThis.CHART_DATA) {
            initCharts(globalThis.CHART_DATA);
        }
    };

    if (globalThis.CHART_DATA && globalThis.CHART_DATA.donut && globalThis.CHART_DATA.donut.length > 0) {
        initCharts(globalThis.CHART_DATA);
    }
})();
