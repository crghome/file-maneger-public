<template>
    <div class="wrapperHeadContent">
        <div class="twc-container">
            <h1 class="tw-font-bold">{{ title }}</h1>
        </div>
    </div>
</template>
<script>
// import axios from 'axios';
export default {
    props: {
        title: String
    },
    data() {
        return {
            // title: this.title,
        }
    },
}
</script>
