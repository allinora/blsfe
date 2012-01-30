function switchLanguage(lang_code){
    var hash = document.location.hash;
        var pathname=self.location.pathname;
        var query=self.location.search;
    if (pathname.match(/^\/\w\w\//)){
        //alert (new_uri);
        new_uri="/" + lang_code + pathname.substr(3);
    } else {
        new_uri="/" + lang_code + pathname;
    }


    if (query) {
        new_uri+=query;
    }
    if (hash) {
        new_uri+=hash;
    }
    //alert(new_uri);
    self.location=new_uri;
}

