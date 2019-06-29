var randomScalingFactor = function() {
            return Math.round(Math.random() * 100);
        };
var config_m_d = {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        adsforwp_localize_data.mobile,
                        adsforwp_localize_data.desktop,
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
                        adsforwp_localize_data.mobile,
                        adsforwp_localize_data.amp,
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
    
} 