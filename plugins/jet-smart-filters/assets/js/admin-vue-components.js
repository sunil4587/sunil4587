!function(e){var t={};function n(r){if(t[r])return t[r].exports;var a=t[r]={i:r,l:!1,exports:{}};return e[r].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)n.d(r,a,function(t){return e[t]}.bind(null,a));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=37)}({37:function(e,t,n){"use strict";(function(e){var t={data:function(){return{pageOptions:window.jetSmartFiltersSettingsConfig.settingsData,preparedOptions:{},savingStatus:!1,ajaxSaveHandler:null}},watch:{pageOptions:{handler:function(e){var t={};for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n].value);this.preparedOptions=t,this.saveOptions()},deep:!0}},methods:{saveOptions:function(){var t=this;t.savingStatus=!0,t.ajaxSaveHandler=e.ajax({type:"POST",url:window.jetSmartFiltersSettingsConfig.settingsApiUrl,dataType:"json",data:t.preparedOptions,beforeSend:function(e,n){null!==t.ajaxSaveHandler&&t.ajaxSaveHandler.abort()},success:function(e,n,r){t.savingStatus=!1,"success"===e.status&&t.$CXNotice.add({message:e.message,type:"success",duration:3e3}),"error"===e.status&&t.$CXNotice.add({message:e.message,type:"error",duration:3e3})}})}}};Vue.component("jet-smart-filters-general-settings",{template:"#jet-dashboard-jet-smart-filters-general-settings",mixins:[t]}),Vue.component("jet-smart-filters-indexer-settings",{template:"#jet-dashboard-jet-smart-filters-indexer-settings",mixins:[t]}),Vue.component("jet-smart-filters-url-structure-settings",{template:"#jet-dashboard-jet-smart-filters-url-structure-settings",mixins:[t]})}).call(this,n(6))},6:function(e,t){e.exports=jQuery}});