<template>
    <vue-loader 
        :active="isGlobalWork" 
        :message="uploadMessage" 
        :percentage="uploadPercentage"
        :message-second="uploadMessageSecond" 
        :percentage-second="uploadPercentageSecond" 
    ></vue-loader>
    
    <div class="wrapperFilePreview">
        <div class="twc-container">
            <div class="filesRow" v-if="arrFiles.length">
                <div class="h2 label tw-mb-6">Загруженные файлы</div>
                <div class="twc-grid md:tw-grid-cols-2 tw-grid-cols-1">
                    <div class="previewFileList" v-for="item, index in arrFiles" :index="index">
                        <img :src="item.prev" :alt="item.name" v-if="item.type.match(/image\//)">
                        <img :src="`/img/images/audio.png`" :alt="item.name" v-if="item.type.match(/audio\//)">
                        <img :src="`/img/images/video.jpg`" :alt="item.name" v-if="item.type.match(/video\//)">
                        <div class="content miniText"><strong>{{ item.name }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="filesRow" v-if="!arrFiles.length">
                <div class="h2 label">Нет файлов</div>
            </div>
        </div>
    </div>
</template>
<script>
// import axios from 'axios';
export default {
    data() {
        return {
            token: null,
            arrFiles: Array(),

            // loaderr
            isGlobalWork: false,
            uploadMessage: '',
        }
    },
    mounted(){
        this.token = document.querySelector('meta[name="csrf-token"]').content;
        this.getFiles();
    },
    methods: {
        getFiles(){
            let _this = this;
            let postData = { _token: this.token };
            this.isGlobalWork = true;
            this.uploadMessage = 'Читаем директорию';

            axios.post( '/api/get-files', 
                {data: postData},
                { headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token }}
            ).then(function(res){
                console.log(res.data);
                if(res.data.status){
                    // _this.palette.selected = res.data.data.palettes[0].value;
                    // if(typeof res.data.data.palettes[0].name != "undefined") _this.palette.name = res.data.data.palettes[0].name;
                    _this.arrFiles = res.data.data.files;
                } else {
                    window.getAlert('error', 'Ошибка', res.data.message);
                }
            }).catch(function(error){
                console.log(error);
                window.exceptionsHandler(error);
            }).then(function(){
                _this.isGlobalWork = false;
                _this.uploadMessage = '';
            });
        }
    }
}
</script>
