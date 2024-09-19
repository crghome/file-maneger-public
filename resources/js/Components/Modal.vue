<template>
    <Transition name="bounce">
        <div class="modal" v-if="open">
            <div class="modal-content">
                <div class="btn-close" @click="closeModal">X</div>
                <div class="modal-body">
                    <div class="type" v-if="type">
                        <i class="fas fa-check-double tw-text-green-400" v-if="type == 'success'"></i>
                        <i class="fas fa-flushed tw-text-red-400" v-if="type == 'error'"></i>
                        <i class="fas fa-info tw-text-blue-400" v-if="type == 'info'"></i>
                    </div>
                    <div class="label">{{ label }}</div>
                    <div class="data" v-html="message"></div>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script>
// import axios from 'axios';
export default {
    data() {
        return {
            open: false,
            type: '',
            label: '',
            message: '',
        }
    },
    methods: {
        closeModal(){
            this.open = false;
            this.type = '';
            this.label = '';
            this.message = '';
        },
        showModal(ev){
            if(ev.detail.open){
                this.type = ev.detail.type;
                this.label = ev.detail.label;
                this.message = ev.detail.message;
                this.open = ev.detail.open;
            }
        }
    },
    created () {
        document.addEventListener('modal-show', this.showModal);
    },
    unmounted () {
        document.removeEventListener('modal-show', this.showModal);
    },
}
</script>
