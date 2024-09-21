<template>
    <vue-loader 
        :active="isGlobalWork" 
        :message="uploadMessage" 
    ></vue-loader>
    
    <div class="wrapperFilePreview">
        <div class="twc-container">
            <div class="filesRow" v-if="arrFiles.length">
                <div class="h2 label tw-mb-columns">Загруженные файлы</div>
                <div class="tw-flex tw-items-center tw-justify-between tw-gap-columns tw-mb-col tw-h-14">
                    <div class="tw-flex tw-items-center tw-gap-col"><input v-model="selectedFilesAll" class="checkbox" type="checkbox" @click="selectAll"> Выбрать все</div>
                    <Transition name="bounce">
                        <div class="tw-flex tw-gap-columns" v-if="selectedFiles.length > 0">
                            <button class="btn btn-danger" @click="deleteFiles">Удалить</button>
                            <button class="btn" @click="downloadFilesZip">СКАЧАТЬ</button>
                        </div>
                    </Transition>
                </div>
                <Transition name="bounce">
                <div class="twc-grid md:tw-grid-cols-2 tw-grid-cols-1">
                    <div class="previewFileList" v-for="item, index in arrFiles" :index="index">
                        <input v-model="selectedFiles" ref="fileCheckBox" class="checkbox" type="checkbox" :value="index">
                        <img :src="item.prev" :alt="item.name" v-if="item.type.match(/image\//)">
                        <img :src="`/img/images/audio.png`" :alt="item.name" v-if="item.type.match(/audio\//)">
                        <img :src="`/img/images/video.jpg`" :alt="item.name" v-if="item.type.match(/video\//)">
                        <div class="content miniText">
                            <strong>{{ item.date }}</strong>
                            <p>{{ item.name }}</p>
                            <p>{{ item.size[0]??'-' }} * {{ item.size[1]??'-' }}</p>
                            <p><a :href="item.src" download>СКАЧАТЬ {{ Math.round(item.filesize / 1000) / 1000 }} Mb</a></p>
                        </div>
                    </div>
                </div>
                </Transition>
                <Transition name="bounce">
                    <div class="tw-flex tw-gap-columns tw-mt-columns" v-if="selectedFiles.length > 0">
                        <button class="btn btn-danger" @click="deleteFiles">Удалить</button>
                        <button class="btn" @click="downloadFilesZip">СКАЧАТЬ</button>
                    </div>
                </Transition>
            </div>
            <div class="filesRow" v-if="!arrFiles.length">
                <div class="h2 label">Нет файлов</div>
            </div>
        </div>
    </div>
</template>
<script>
// import axios from 'axios';
// import Auth from '@/Components/Auth.vue';
export default {
    // components: {Auth},
    data() {
        return {
            token: null,
            sessionId: null,
            arrFiles: Array(),
            selectedFiles: Array(),
            selectedFilesAll: false,

            // loaderr
            isGlobalWork: false,
            uploadMessage: '',
        }
    },
    mounted(){
        this.token = document.querySelector('meta[name="csrf-token"]').content;
        this.sessionId = true; //sessionStorage.getItem("id");
        this.getFiles();
    },
    computed: {
        selectedAll: ()=>{ return this.arrFiles.length == this.selectedFiles.length; },
    },
    watch: {
        selectedFiles: {
            handler: function() {
                this.selectedFilesAll = (this.arrFiles.length == this.selectedFiles.length);
            },
            deep: true
        },
    },
    methods: {
        selectAll(){
            this.selectedFiles = [];
            if(!this.selectedFilesAll){
                this.selectedFilesAll = true;
                for(let ind in this.arrFiles){
                    this.selectedFiles.push(ind);
                }
            } else {
                this.selectedFilesAll = false;
            }
        },

        downloadFilesZip(){
            try {
                if(this.sessionId == null){ throw new Error('Иш масленица, доступ получи сначала'); }
                if(this.selectedFiles.length <= 0){ throw new Error("Не выбраны файлы"); }

                let _this = this;
                let postData = { _token: this.token, arrFileNames: [] };

                this.isGlobalWork = true;
                this.uploadMessage = 'Складываем файлы в пакет';

                this.selectedFiles.forEach((ind) => {
                    postData.arrFileNames.push(this.arrFiles[ind].name);
                });

                axios.post( '/api/get-files-zip', 
                    {data: postData},
                    { headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token }}
                ).then(function(res){
                    console.log(res.data);
                    if(res.data.status){
                        window.downloadHandler(res.data.data.zip, res.data.data.zip)
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
            } catch (e) {
                this.isGlobalWork = false;
                window.getAlert('error', 'Ошибка', e.message);
            }

        },

        deleteFiles(){
            try {
                if(this.sessionId == null){ throw new Error('Иш масленица, доступ получи сначала'); }
                if(this.selectedFiles.length <= 0){ throw new Error("Не выбраны файлы"); }

                let _this = this;
                let postData = { _token: this.token, arrFileNames: [] };

                this.isGlobalWork = true;
                this.uploadMessage = 'Складываем файлы в пакет';

                this.selectedFiles.forEach((ind) => {
                    postData.arrFileNames.push(this.arrFiles[ind].name);
                });

                axios.post( '/api/remove-files', 
                    {data: postData},
                    { headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token }}
                ).then(function(res){
                    console.log(res.data);
                    if(res.data.status){
                        _this.selectedFiles = [];
                        _this.getFiles()
                        window.getAlert('success', 'Удалено', res.data.data.countUnlink + ' файлов');
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
            } catch (e) {
                this.isGlobalWork = false;
                window.getAlert('error', 'Ошибка', e.message);
            }

        },

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
