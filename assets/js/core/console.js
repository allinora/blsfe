// Do not throw errors on console stuff in js
if(!window.console){
  window.console = {
    log: function(){
      return;
    },
    write: function(){
      return;
    },
    info: function(){
      return;
    },
    warn: function(){
      return;
    },
    error: function(){
      return;
    }
  }
}
