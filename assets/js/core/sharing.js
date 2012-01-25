function facebookShare(u,t) {
	if (!t){
		t=document.title;
	}
	if (!u){
		u=location.href;
	}
	_url='http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t);
	_options="width=700,height=400,scrollbars=0,status=1,location=1";
	window.open(_url, 'facebookShare', _options);
	
	return false;
	}
	
	
function twitterShare(u,t){
	if (!t){
		t=document.title;
	}
	if (!u){
		u=location.href;
	}
	_url='https://twitter.com/intent/tweet?status='+ encodeURIComponent(t) + " " + encodeURIComponent(u);
	_options="width=700,height=400,scrollbars=0,status=1,location=1";
	window.open(_url, 'twitterShare', _options);
	
	return false;
}

