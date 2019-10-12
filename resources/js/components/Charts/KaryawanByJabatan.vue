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
            dataByJabatan(){
            axios.get('/hrd-karyawan/by-jabatan').then((response) => {
                    var ctx = document.getElementById(this.id).getContext('2d');
                    new Chart(ctx, {
                        type: this.type ? this.type : 'bar',
                        options: {
                            scales: { },
                            legend: {
                                display: true,
                                position: 'left',
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        },
                        data: {
                            labels:response.data.gridColumns,
                            datasets: [{
                                label: this.title,
                                data: response.data.gridData,
                                fill: this.fill,
                                backgroundColor: [this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex(),this.getRandomColorHex()],
                                borderColor: this.borderColor,
                                borderWidth: this.borderWidth ? this.borderWidth : 1
                            }]
                        }
                    });
                });
            },
        },
        created: function () {
        },
        mounted() {
            this.dataByJabatan(); 
        }
    }
</script>