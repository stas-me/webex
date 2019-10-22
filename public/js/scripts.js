function ConfirmDelete(url, text){
    if( confirm(text) ){
        window.location.href = url;
    }
}