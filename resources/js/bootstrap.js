import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.getAlert = function (type, label, message){
    let ev = new CustomEvent("modal-show", {
        detail: {
            open: true,
            type: type,
            label: label,
            message: message,
        },
        bubbles: true,
        cancelable: false,
        composed: true,
    });
    document.dispatchEvent(ev);
}
