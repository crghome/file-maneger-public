<template>
    <vue-loader 
        :active="isGlobalWork" 
        :message="uploadMessage" 
    ></vue-loader>
    <div class="wrapperLogin">
        <div class="tw-container tw-flex tw-items-center tw-justify-center tw-h-full">
            <div class="formBlock">
                <div class="name h2 tw-mb-columns tw-text-center">Вход</div>
                <vue-alert-block :messages="arrErrors" @clearMessages="function(event){this.arrErrors = []}"></vue-alert-block>
                <div class="form">
                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" v-model="login" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" v-model="password" required>
                    </div>
                    <div class="form-group">
                        <span></span>
                        <button class="btn" @click="submit">Войти</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            token: '',
            login: 'dnage',
            password: 'password',
            arrErrors: [],

            isGlobalWork: false,
            uploadMessage: '',
        }
    },
    mounted(){
        this.token = document.querySelector('meta[name="csrf-token"]').content;
        // if(typeof sessionStorage.getItem("id") == "undefined" || sessionStorage.getItem("id") == null){
        //     window.getAlert('error', 'Да фиг вам', 'Не нашли сессию')
        // }
    },
    methods: {
        submit() {
            try {
                if(this.login == '' || this.password == '') throw new Error('Не все поля заполнены');

                let _this = this;

                this.isGlobalWork = true;
                this.uploadMessage = 'Обрабатываем';
                let formData = new FormData();
                formData.append('_token', this.token);
                formData.append('login', this.login);
                formData.append('password', this.password);
                    
                axios.post( '/login/confirm', 
                    formData,
                    { 
                        headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': this.token },
                    }
                ).then(function(res){
                    console.log(res.data);
                    if(res.data.status){
                        window.location.href = '/resource';
                    } else {
                        window.getAlert('error', 'Ошибка', res.data.message);
                    }
                }).catch(function(error){
                    console.log(error);
                    window.exceptionsHandler(error);
                });

                this.uploadMessage = '';
                this.isGlobalWork = false;
            } catch (e) {
                this.uploadMessage = '';
                this.isGlobalWork = false;
                window.getAlert('error', 'Ошибка', e.message);
                // e.stopPropagation();
            } finally {
                console.log('FIN');
            }
        }
    }
}
</script>