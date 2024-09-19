<template>
    <div class="wrapperAlertBlock" v-if="arrMessages.length">
        <div class="btn-close" @click="clearArrParent">X</div>
        <div class="icon"><i class="fas fa-flushed"></i></div>
        <div class="text">
            <p v-for="item, index in arrMessages" :index="index"><strong v-if="item.status">{{ item.status }}: </strong>{{ item.message??'---' }}</p>
        </div>
    </div>
</template>
<script>
// import axios from 'axios';
export default {
    props: {
        messages: Array
    },
    data() {
        return {
            arrMessages: this.messages,
        }
    },
    watch: {
        '$props.messages': {
            handler: function(messages) {
                this.arrMessages = messages;
            },
            deep: true
        }
    },
    methods: {
        clearArrParent(){
            this.$emit('clear-messages', {});
        }
    }
}
</script>
