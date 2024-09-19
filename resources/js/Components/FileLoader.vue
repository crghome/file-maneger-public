<template>
    <vue-loader 
        :active="isGlobalWork" 
        :message="uploadMessage" 
        :percentage="uploadPercentage"
        :message-second="uploadMessageSecond" 
        :percentage-second="uploadPercentageSecond" 
    ></vue-loader>
    
    <div class="wrapperFileLoader">
        <div class="twc-container">
            <div class="twc-grid md:tw-grid-cols-2">
                <div class="prevFile">
                    <!-- <img class="uploadImage" id="uploadImage" :src="image.blob" v-if="image.blob != null && image.blob != ''"> -->
                    <div class="loadBack">
                        <div class="back">
                            <img :src="`/img/icons/upload.svg`" alt="">
                            <div class="text" v-if="files == null">Выбрать файлы</div>
                            <div class="text" v-if="files != null">Добавить файлы</div>
                        </div>
                    </div>
                    <input class="inputFile" type="file" name="files" accept="image/*, video/*" multiple @change="onFileChange" />
                </div>
                <div class="infoFiles">
                    <div class="infoRow tw-grid tw-grid-cols-[200px_1fr]" v-if="files">
                        <div class="label">Выбрано файлов</div>
                        <div class="value">{{ files.length }}</div>
                        <div class="label">Размер файлов</div>
                        <div class="value">~ {{ filesSize }} Mb</div>
                    </div>
                </div>
            </div>
            <vue-alert-block :messages="arrErrors" @clearMessages="clearMessages(event)"></vue-alert-block>
            <div class="fileSelect" v-if="files">
                <div class="h2 label">Выбранные файлы</div>
                <div class="twc-grid xxl:tw-grid-cols-10 xl:tw-grid-cols-8 lg:tw-grid-cols-6 md:tw-grid-cols-4 xs:tw-grid-cols-3 tw-grid-cols-2">
                    <div class="previewFileImage" v-for="item, index in files" :index="index">
                        <div class="btn-close" @click="unsetFile(index)">X</div>
                        <img :src="getFilePreview(item)" :alt="item.name">
                        <div class="content miniText"><strong>{{ item.name }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="afterLoad" v-if="files">
                <button class="btn btn-success" @click="acceptLoadFiles">Загрузить</button>
            </div>
            <div class="fileSelect" v-if="filesLoaded.length">
                <div class="h2 label">Загруженные файлы</div>
                <div class="twc-grid xl:tw-grid-cols-12 lg:tw-grid-cols-10 md:tw-grid-cols-8 xs:tw-grid-cols-4 tw-grid-cols-3">
                    <div class="previewFileImage" v-for="item, index in filesLoaded" :index="index">
                        <img :src="item.src" :alt="item.file">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
// import loader from '@/Components/Loader.vue';
export default {
    // components: { ImageCropperJs, VueSelectHtml, BeforeAfterSlider },
    data() {
        return {
            token: null,
            files: null,
            filesLoaded: Array(),
            sizeFiles: null,

            arrErrors: Array(),
            // arrErrors: Array({status: 200, message: 'Nu ok'}, {message: 'Not status'}, {status: 500}),

            isGlobalWork: false,
            uploadMessage: '',
            uploadPercentage: 0,
            uploadMessageSecond: '',
            uploadPercentageSecond: 0,
        }
    },
    mounted(){
        // this.token = $('meta[name="csrf-token"]').attr('content');
        this.token = document.querySelector('meta[name="csrf-token"]').content;
    },
    computed: {
        filesSize: function(){
            let s = 0;
            if(this.files != null && this.files.length > 0){
                for(let i = 0; i < this.files.length; i++){
                    s += this.files[i].size;
                }
                this.sizeFiles = s > 0 ? s : null;
                s = Math.round(s / 1000) / 1000;
            }
            return s;
        },
        runLoader: function(){ return this.isGlobalWork; }
    },
    methods: {
        getModal(){
            // window.getAlert('success', 'Вызов', 'Ну сообщение');
            this.isGlobalWork = true;
            console.log('isGlobalWork: '+this.isGlobalWork)
        },
        clearMessages(event){
            console.log('clearMessages')
            this.arrErrors = [];
            console.log(this.arrErrors)
        },

        onFileChange(event) {
            if(this.files != null){
                let dt = new DataTransfer(this.files);
                for(let i = 0; i < this.files.length; i++) dt.items.add(this.files[i]);
                let fAdd = event.target.files;
                for(let i = 0; i < fAdd.length; i++) dt.items.add(fAdd[i]);
                this.files = dt.files;
                document.querySelector('input[name="files"]').files = dt.files;
            } else {
                this.files = event.target.files;
            }
            console.log(typeof this.files);
            console.log(this.files);
        },
        getFilePreview(file) {
            return URL.createObjectURL(file);
        },
        unsetFile(index){
            let dt = new DataTransfer();
            for (let i = 0; i < this.files.length; i++){
                const file = this.files[i];
                if(index !== i) dt.items.add(file);
            }
            this.files = dt.files;
            document.querySelector('input[name="files"]').files = dt.files;
            if(this.files.length <= 0) this.files = null;
            console.log(this.files);
        },

        async acceptLoadFiles(){
            try {
                if(this.files == null && this.files.length <= 0) throw new Error('Не выбраны файлы');

                let _this = this;
                let filesCopy = [...this.files];;

                this.isGlobalWork = true;
                this.uploadMessage = 'Загружаем файлы';
                this.uploadPercentage = 2;
                console.log('start load');

                for(let i = (filesCopy.length - 1); i >= 0; i--){
                    this.uploadMessageSecond = filesCopy[i].name;
                    this.uploadPercentageSecond = 0;
                    console.log('i: '+i);
                    console.log('file: '+filesCopy[i].name);

                    let formData = new FormData();
                    formData.append('file', filesCopy[i]);
                    formData.append('_token', this.token);
                    
                    await axios.post( '/api/upload-file', 
                        formData,
                        { 
                            headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': this.token },
                            onUploadProgress: function(progressEvent){
                                this.uploadPercentageSecond = parseInt(Math.round(( progressEvent.loaded / progressEvent.total) * 100));
                            }.bind(this)
                        }
                    ).then(function(res){
                        console.log(res.data);
                        _this.filesLoaded.push(res.data.data);
                        _this.unsetFile(i);
                    }).catch(function(error){
                        console.log(error);
                        let response = error.response;
                        let status = response.status ? response.status : 0;
                        let message = response.statusText ? response.statusText : error.message;
                        message = message + ' [' + response.data.message + ']';
                        _this.arrErrors.push({status: status, message: 'Не загрузили "'+filesCopy[i].name+'" - '+message});
                        // return false;
                    }).then(() => {
                        _this.uploadPercentageSecond = 0;
                        console.log('file loaded');
                    });

                    this.uploadPercentage += (100 / filesCopy.length);
                }
                console.log('endfor');


                this.uploadPercentage = 0;
                this.uploadMessage = '';
                this.uploadPercentageSecond = 0;
                this.uploadMessageSecond = '';
                this.isGlobalWork = false;
                window.getAlert('success', 'Файлы загружены');
            } catch (e) {
                this.uploadPercentage = 0;
                this.uploadMessage = '';
                this.uploadPercentageSecond = 0;
                this.uploadMessageSecond = '';
                this.isGlobalWork = false;
                window.getAlert('error', 'Ошибка', e.message);
                // e.stopPropagation();
            } finally {
                console.log('FIN');
            }
        },

        exceptionsHandler(error){
            let response = error.response;
            let status = response.status ? response.status : 0;
            switch (status){
                case 413:
                    window.getAlert('error', 'Ошибка', 'Слишком долгая загрузка файла, проверьте интернет и размер файла')
                    break;
                default:
                    response.statusText
                        ? window.getAlert('error', 'Ошибка ' + status, response.statusText)
                        : window.getAlert('error', 'Ошибка ' + status, error);
                    break;
            }
        }
    },
}
</script>
