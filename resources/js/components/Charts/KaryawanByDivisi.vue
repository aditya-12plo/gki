<template>
    <div>
        <canvas :width="width" :height="height" :id="id"></canvas>
    </div>
</template>

<script> 
    export default {
        props: ['id','width','height','type','title', 'fill', 'borderColor', 'borderWidth'],
        data () {
            return { 
                labels:[] ,
                datas:[] ,
            }
        },
        methods: {  
            getRandomColorHex () {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
            },
             getDataset(comeData,total) { 
                var data = []
                for(var i=0; i < total; i++) {
                    var dd = { 
                        label: comeData[i].divisi, 
                        fill: 'rgba(220,220,220,0.2)',
                        backgroundColor: [this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex()],
                        borderColor: [this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex()],
                        borderWidth: 1,
                        data: [comeData[i].total] 
                    };
                    data.push(dd)
                }
                return data; 
            },
            dataByDivisi(){
            axios.get('/hrd-karyawan/by-divisi').then((response) => {
                    var data = response.data;
                    var exportData = this.getDataset(data,data.length);
                    var ctx = document.getElementById(this.id).getContext('2d');
                    new Chart(ctx, {
                        type: this.type ? this.type : 'bar',
                        options: {
                            scales: { 
                                yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,           
                                    stepSize:10,
                                },
                                gridLines: {
                                    display: true
                                }
                                }],
                                xAxes: [ {
                                gridLines: {
                                    display: false
                                }
                                }]
                            },
                            legend: {
                                display: true,
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        },
                        data: {
                            labels: ['Divisi'],
                            datasets: exportData
                        }
                    });
                });
            },
        },
        created: function () {
        },
        mounted() {
            this.dataByDivisi(); 
        }
    }
</script>