var randomScalingFactor = function() {
            return Math.round(Math.random() * 100);
        };
var config_m_d = {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        piechart.mobile,
                        piechart.desktop,
                    ],
                    backgroundColor: [
                        'blue',
                        'orange',
                    ],
                    label: 'Mobile vs Desktop'
                }],
                labels: [
                    'Mobile',
                    'Desktop',
                ]
            },
            options: {
                responsive: true
            }
        };

var config_m_a = {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        piechart.mobile,
                        piechart.amp,
                    ],
                    backgroundColor: [
                        'blue',
                        'orange',
                    ],
                    label: 'Mobile vs Non AMp'
                }],
                labels: [
                    'Non AMp',
                    'Amp',
                ]
            },
            options: {
                responsive: true
            }
        };

window.onload = function() { 


    var ctx = document.getElementById('chart-stats').getContext('2d');
    window.myPie = new Chart(ctx, config_m_d);

    var ctx = document.getElementById('chart-amp-mobile').getContext('2d');
    window.myPie = new Chart(ctx, config_m_a);

/*
     $("#chart-stats").CanvasJSChart({ 
        title: { 
            text: "Mobile vs Desktop",
            fontSize: 24
        }, 
        axisY: { 
            title: "Products in %" 
        }, 
        legend :{ 
            verticalAlign: "center", 
            horizontalAlign: "right" 
        }, 
        chartArea: {
          left: 18,
          top: 0,
          width: 250,
          height: 250 
        },
        width: 280,
        height: 280,
        data: [ 
        { 
            type: "pie", 
            showInLegend: true, 
            toolTipContent: "{label} <br/> {y} %", 
            indexLabel: "{y} %", 
            dataPoints: [ 
                { label: "Mobile",  y: piechart.mobile, legendText: "mobile"}, 
                { label: "Desktop",    y: piechart.desktop, legendText: "Desktop"  },
            ] 
        } 
        ] 
    }); */
} 