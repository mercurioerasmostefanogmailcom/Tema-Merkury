(function(){
  'use strict';
  var root=document.documentElement, body=document.body;
  var modeClass=(body.className.match(/merkury-mode-(light|dark|system)/)||[])[1]||'system';
  var saved=null, key='merkury-color-preference';
  try{saved=localStorage.getItem(key)}catch(e){}
  var media=window.matchMedia?window.matchMedia('(prefers-color-scheme: dark)'):null;
  function effective(){
    if(saved==='light'||saved==='dark') return saved;
    if(modeClass==='light'||modeClass==='dark') return modeClass;
    return media&&media.matches?'dark':'light';
  }
  function apply(){
    var value=effective();
    root.setAttribute('data-merkury-mode',value);
    if(body) body.setAttribute('data-merkury-mode',value);
    document.querySelectorAll('.merkury-mode-toggle').forEach(function(button){
      button.setAttribute('aria-pressed',value==='dark'?'true':'false');
      button.innerHTML=value==='dark'?'☀ <span>Modalità chiara</span>':'☾ <span>Modalità scura</span>';
    });
  }
  function bind(){
    apply();
    document.querySelectorAll('.merkury-mode-toggle').forEach(function(button){
      button.addEventListener('click',function(){
        saved=effective()==='dark'?'light':'dark';
        try{localStorage.setItem(key,saved)}catch(e){}
        apply();
      });
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',bind); else bind();
  if(media){
    var change=function(){if(!saved&&modeClass==='system')apply()};
    if(media.addEventListener) media.addEventListener('change',change); else if(media.addListener) media.addListener(change);
  }
})();
