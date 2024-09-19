<template>
<div class="wrapperLoader wrapperLoaderFixed" v-if="active" style="z-index: 10000;">
    <div class="">
        <div class="loader"></div>
        <div class="uploadMessage" v-if="message">{{ message }}</div>
        <div class="uploadPercentDiv" v-if="percentage > 0"><div class="value" :style="{'width': percentage+'%'}"></div></div>
        <div class="uploadMessage" v-if="messageSecond">{{ messageSecond }}</div>
        <div class="uploadPercentDiv" v-if="percentageSecond > 0"><div class="value" :style="{'width': percentageSecond+'%'}"></div></div>
    </div>
</div>
</template>
<script>
// import axios from 'axios';
export default {
    props: {
        active: Boolean,
        message: String,
        percentage: Number,
        messageSecond: String,
        percentageSecond: Number
    },
    data() {
        return {
            // active: this.active,
            // message: this.message,
            // percentage: this.percentage,
            // messageSecond: this.messageSecond,
            // percentageSecond: this.percentageSecond,
        }
    },
}
</script>
