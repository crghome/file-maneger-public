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

window.exceptionsHandler = function(error){
    let response = error.response??{};
    let status = response.status??0;
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

