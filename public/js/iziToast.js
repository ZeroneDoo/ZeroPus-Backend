const showAlert = (message, type) => {
    if(type == 'success'){
        iziToast.success({
            title: 'Success',
            message: message,
            position: 'topRight'
        });
    }else if(type == 'error'){
        iziToast.error({
            title: 'Failed',
            message: message,
            position: 'topRight',
        });
    }else if(type == 'info'){
        iziToast.info({
            title: 'Info',
            message: message,
            position: 'topRight'
        });
    }
}