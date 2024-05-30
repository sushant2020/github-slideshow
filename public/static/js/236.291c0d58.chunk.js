"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[236],{96236:(e,t,r)=>{r.r(t),r.d(t,{default:()=>d});var n=r(424),a=r(99442),o=r(47419),c=r(11645),s=r(44864),l=r(65043),i=r(30460),f=r(70579);let u="";const d=(0,a.y)((function(e){const[t,r]=(0,l.useState)([]),[a,d]=(0,l.useState)(!0),[p,m]=(0,l.useState)(null);return(0,l.useEffect)((()=>{(async()=>{const e="".concat(s.u.MAIN_ENDPOINT,"/graphql");try{const t=await fetch(e,{method:"POST",mode:"cors",cache:"no-cache",headers:{"Content-type":"application/json",Authorization:"Bearer ".concat(u)},body:JSON.stringify({query:"\n        query {\n          getSupplierdata {\n            code\n            name\n          }\n        }\n      "})}),n=await t.json();t.ok?r(n.data.getSupplierdata):m(n.errors||"Error fetching data")}catch(p){console.log(p,"Error fetching data"),m("Error fetching data")}finally{d(!1)}})()}),[]),u=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,f.jsx)(f.Fragment,{children:(0,f.jsx)(o.A,{children:(0,f.jsxs)(c.A,{span:20,children:[(0,f.jsxs)("ol",{className:"breadcrumb breadcrumb-dot fs-6 fw-bold",style:{marginTop:"0.1%",marginBottom:"10px"},children:[(0,f.jsx)(n.N_,{to:"/home",className:"pe-1 cursor-pointer",children:(0,f.jsx)("li",{className:"breadcrumb-item pe-1 pt-1",children:"Home"})}),"/ \xa0",(0,f.jsx)("li",{className:"breadcrumb-item pe-3",children:"Watch List"})]}),(0,f.jsxs)("div",{className:"row",children:[(0,f.jsx)(i.A,{loading:a,active:!0}),t.map((e=>(0,f.jsx)("div",{className:"col-3 mt-1 mb-1 p-4 ",children:(0,f.jsxs)("div",{className:"card p-4 justify-content-center",style:{minHeight:"100px"},children:[e.code,"-",e.name]})})))]})]})})})}))},11645:(e,t,r)=>{r.d(t,{A:()=>n});const n=r(30227).A},95150:(e,t,r)=>{r.d(t,{A:()=>n});const n=(0,r(65043).createContext)({})},30227:(e,t,r)=>{r.d(t,{A:()=>p});var n=r(64467),a=r(58168),o=r(82284),c=r(98139),s=r.n(c),l=r(65043),i=r(35296),f=r(95150),u=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var a=0;for(n=Object.getOwnPropertySymbols(e);a<n.length;a++)t.indexOf(n[a])<0&&Object.prototype.propertyIsEnumerable.call(e,n[a])&&(r[n[a]]=e[n[a]])}return r};var d=["xs","sm","md","lg","xl","xxl"];const p=l.forwardRef((function(e,t){var r=l.useContext(i.QO),c=r.getPrefixCls,p=r.direction,m=l.useContext(f.A),A=m.gutter,y=m.wrap,h=m.supportFlexGap,v=e.prefixCls,g=e.span,x=e.order,b=e.offset,j=e.push,O=e.pull,w=e.className,N=e.children,S=e.flex,E=e.style,C=u(e,["prefixCls","span","order","offset","push","pull","className","children","flex","style"]),P=c("col",v),k={};d.forEach((function(t){var r={},c=e[t];"number"===typeof c?r.span=c:"object"===(0,o.A)(c)&&(r=c||{}),delete C[t],k=(0,a.A)((0,a.A)({},k),(0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)({},"".concat(P,"-").concat(t,"-").concat(r.span),void 0!==r.span),"".concat(P,"-").concat(t,"-order-").concat(r.order),r.order||0===r.order),"".concat(P,"-").concat(t,"-offset-").concat(r.offset),r.offset||0===r.offset),"".concat(P,"-").concat(t,"-push-").concat(r.push),r.push||0===r.push),"".concat(P,"-").concat(t,"-pull-").concat(r.pull),r.pull||0===r.pull),"".concat(P,"-rtl"),"rtl"===p))}));var T=s()(P,(0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)({},"".concat(P,"-").concat(g),void 0!==g),"".concat(P,"-order-").concat(x),x),"".concat(P,"-offset-").concat(b),b),"".concat(P,"-push-").concat(j),j),"".concat(P,"-pull-").concat(O),O),w,k),I={};if(A&&A[0]>0){var R=A[0]/2;I.paddingLeft=R,I.paddingRight=R}if(A&&A[1]>0&&!h){var B=A[1]/2;I.paddingTop=B,I.paddingBottom=B}return S&&(I.flex=function(e){return"number"===typeof e?"".concat(e," ").concat(e," auto"):/^\d+(\.\d+)?(px|em|rem|%)$/.test(e)?"0 0 ".concat(e):e}(S),!1!==y||I.minWidth||(I.minWidth=0)),l.createElement("div",(0,a.A)({},C,{style:(0,a.A)((0,a.A)({},I),E),className:T,ref:t}),N)}))},28821:(e,t,r)=>{r.d(t,{A:()=>h});var n=r(58168),a=r(64467),o=r(82284),c=r(5544),s=r(98139),l=r.n(s),i=r(65043),f=r(35296),u=r(46058),d=r(19304),p=r(29592),m=r(95150),A=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var a=0;for(n=Object.getOwnPropertySymbols(e);a<n.length;a++)t.indexOf(n[a])<0&&Object.prototype.propertyIsEnumerable.call(e,n[a])&&(r[n[a]]=e[n[a]])}return r};(0,p.P)("top","middle","bottom","stretch"),(0,p.P)("start","end","center","space-around","space-between","space-evenly");function y(e,t){var r=i.useState("string"===typeof e?e:""),n=(0,c.A)(r,2),a=n[0],s=n[1];return i.useEffect((function(){!function(){if("string"===typeof e&&s(e),"object"===(0,o.A)(e))for(var r=0;r<d.ye.length;r++){var n=d.ye[r];if(t[n]){var a=e[n];if(void 0!==a)return void s(a)}}}()}),[JSON.stringify(e),t]),a}const h=i.forwardRef((function(e,t){var r=e.prefixCls,s=e.justify,p=e.align,h=e.className,v=e.style,g=e.children,x=e.gutter,b=void 0===x?0:x,j=e.wrap,O=A(e,["prefixCls","justify","align","className","style","children","gutter","wrap"]),w=i.useContext(f.QO),N=w.getPrefixCls,S=w.direction,E=i.useState({xs:!0,sm:!0,md:!0,lg:!0,xl:!0,xxl:!0}),C=(0,c.A)(E,2),P=C[0],k=C[1],T=i.useState({xs:!1,sm:!1,md:!1,lg:!1,xl:!1,xxl:!1}),I=(0,c.A)(T,2),R=I[0],B=I[1],q=y(p,R),F=y(s,R),G=(0,u.A)(),J=i.useRef(b);i.useEffect((function(){var e=d.Ay.subscribe((function(e){B(e);var t=J.current||0;(!Array.isArray(t)&&"object"===(0,o.A)(t)||Array.isArray(t)&&("object"===(0,o.A)(t[0])||"object"===(0,o.A)(t[1])))&&k(e)}));return function(){return d.Ay.unsubscribe(e)}}),[]);var L=N("row",r),W=function(){var e=[void 0,void 0];return(Array.isArray(b)?b:[b,void 0]).forEach((function(t,r){if("object"===(0,o.A)(t))for(var n=0;n<d.ye.length;n++){var a=d.ye[n];if(P[a]&&void 0!==t[a]){e[r]=t[a];break}}else e[r]=t})),e}(),D=l()(L,(0,a.A)((0,a.A)((0,a.A)((0,a.A)({},"".concat(L,"-no-wrap"),!1===j),"".concat(L,"-").concat(F),F),"".concat(L,"-").concat(q),q),"".concat(L,"-rtl"),"rtl"===S),h),H={},M=null!=W[0]&&W[0]>0?W[0]/-2:void 0,Q=null!=W[1]&&W[1]>0?W[1]/-2:void 0;if(M&&(H.marginLeft=M,H.marginRight=M),G){var _=(0,c.A)(W,2);H.rowGap=_[1]}else Q&&(H.marginTop=Q,H.marginBottom=Q);var z=(0,c.A)(W,2),U=z[0],$=z[1],K=i.useMemo((function(){return{gutter:[U,$],wrap:j,supportFlexGap:G}}),[U,$,j,G]);return i.createElement(m.A.Provider,{value:K},i.createElement("div",(0,n.A)({},O,{className:D,style:(0,n.A)((0,n.A)({},H),v),ref:t}),g))}))},47419:(e,t,r)=>{r.d(t,{A:()=>n});const n=r(28821).A}}]);
//# sourceMappingURL=236.291c0d58.chunk.js.map