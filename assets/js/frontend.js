(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{11:function(e,t,n){"use strict";n.r(t);var r=n(1),o={name:"App"},s=n(0),i=Object(s.a)(o,(function(){var e=this.$createElement,t=this._self._c||e;return t("div",{attrs:{id:"vue-frontend-app"}},[t("h2",[this._v("Frontend App")]),this._v(" "),t("router-link",{attrs:{to:"/"}},[this._v("Home")]),this._v(" "),t("router-link",{attrs:{to:"/profile"}},[this._v("Profile")]),this._v(" "),t("router-view")],1)}),[],!1,null,null,null).exports,a=n(2),l={name:"Home",data:()=>({msg:"Welcome to Your Vue.js Frontend App"})},p=Object(s.a)(l,(function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"hello"},[t("span",[this._v(this._s(this.msg))])])}),[],!1,null,"50a8de1d",null).exports,u={name:"Profile",data:()=>({})},c=Object(s.a)(u,(function(){var e=this.$createElement;return(this._self._c||e)("div",{staticClass:"profile"},[this._v("\n  The Profile Page\n")])}),[],!1,null,"4beb872e",null).exports;r.a.use(a.a);var h=new a.a({routes:[{path:"/",name:"Home",component:p},{path:"/profile",name:"Profile",component:c}]});r.a.config.productionTip=!1,new r.a({el:"#vue-frontend-app",router:h,render:e=>e(i)})}},[[11,0,1]]]);