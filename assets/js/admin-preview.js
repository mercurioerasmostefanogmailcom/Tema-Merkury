(function(){
  'use strict';
  var frame=document.getElementById('mk-live-frame');
  function settings(){
    var values={};
    document.querySelectorAll('[name^="settings["]').forEach(function(el){
      var match=el.name.match(/^settings\[([^\]]+)\]$/);
      if(!match) return;
      if(el.type==='checkbox') values[match[1]]=el.checked;
      else if(el.type==='radio'){if(el.checked) values[match[1]]=el.value;}
      else values[match[1]]=el.value;
    });
    var image=document.querySelector('[name="settings[hero_image]"]');
    values.hero_image_url=image?image.value:'';
    return values;
  }
  function send(){if(frame&&frame.contentWindow) frame.contentWindow.postMessage({type:'merkury-live-preview',settings:settings()},window.location.origin);}
  document.querySelectorAll('[name^="settings["]').forEach(function(el){el.addEventListener('input',send);el.addEventListener('change',send);});
  var overlay=document.getElementById('mk-overlay'), output=document.getElementById('mk-overlay-out');
  if(overlay&&output){var update=function(){output.textContent=overlay.value+'%';};overlay.addEventListener('input',update);update();}
  if(frame){
    frame.addEventListener('load',send);
    window.addEventListener('message',function(event){if(event.origin===window.location.origin&&event.data&&event.data.type==='merkury-live-preview-ready') send();});
    document.getElementById('mk-refresh')?.addEventListener('click',function(){frame.src=frame.src;});
    document.querySelectorAll('[data-width]').forEach(function(button){button.addEventListener('click',function(){frame.style.width=button.dataset.width;});});
  }
  send();
})();
