"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[659],{70659:(e,t,n)=>{n.r(t),n.d(t,{default:()=>N});var r=n(65043),a=n(94406),s=n(50525),o=n(30460),i=n(47419),l=n(11645),c=n(5397),d=n(10804),u=n(424),p=n(99442),f=n(94539),h=n(86178),m=n.n(h),x=(n(60919),n(28671)),y=n.n(x),b=n(44864),g=n(70579);const{Option:v}=a.A;let j="",w=0;const{RangePicker:A}=s.A;const N=(0,p.y)((e=>{const[t,n]=(0,r.useState)(function(){const{innerWidth:e,innerHeight:t}=window;return{width:e,height:t}}()),[s,p]=(0,r.useState)(!1),[h,x]=(0,r.useState)(!1),[N,O]=(0,r.useState)(""),[S,T]=(0,r.useState)([]),[k,C]=(0,r.useState)({}),[_,E]=(0,r.useState)([]),[P,W]=(0,r.useState)(e),[L,M]=(0,r.useState)(1),[R,D]=(0,r.useState)(1),[I,Y]=(0,r.useState)({keyword:"",user:""}),[z,F]=(0,r.useState)(null),[H,B]=(0,r.useState)(null),[U,G]=(0,r.useState)("Desc"),[J,q]=(0,r.useState)(!1),[Q,$]=(0,r.useState)(null),[K,V]=(0,r.useState)([]),[X,Z]=(0,r.useState)(!0);function ee(e,t,n){q(!0);try{fetch("".concat(b.u.MAIN_ENDPOINT,"/api/get-historical-comments/").concat(void 0!=e?e:localStorage.getItem("productId"),"/").concat(t,"/").concat(n),{method:"get",headers:{Authorization:"Bearer ".concat(j),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){200!=e.status&&(localStorage.clear(),window.location.reload()),e.json().then((function(e){q(!1),E(e.data.historical_comments),M(e.data.row_count),V(e.data.users),T(e.data.historical_comments),C(e.data.product_details),p(!1),x(!1),Z(!0)}))})).catch((function(e){p(!1)}))}catch(r){console.log("ERR>>>",r)}}(0,r.useEffect)((()=>{void 0!=e.location.state&&localStorage.setItem("productId",e.location.state.data),W(e);let t=void 0!=P.location.state?P.location.state.data:localStorage.getItem("productId");O(t),ee(t,1,w)}),[e]);return j=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,g.jsx)(g.Fragment,{children:1==s?(0,g.jsxs)(r.Fragment,{children:[(0,g.jsx)(o.A,{loading:s,active:!0}),(0,g.jsx)(o.A,{loading:s,active:!0}),(0,g.jsx)(o.A,{loading:s,active:!0})]}):(0,g.jsxs)(r.Fragment,{children:[(0,g.jsxs)(i.A,{style:{marginTop:"4%"},children:[(0,g.jsx)(l.A,{span:20,children:(0,g.jsxs)("ol",{className:"breadcrumb breadcrumb-dot fs-6 fw-bold",style:{margin:"1%"},children:[(0,g.jsx)(u.N_,{to:"/home",className:"pe-1 cursor-pointer",children:(0,g.jsx)("li",{className:"breadcrumb-item pe-1",children:"Home"})}),"/ \xa0",(0,g.jsx)(u.N_,{to:{pathname:"/product",state:{prod_id:N,data:k}},children:(0,g.jsx)("li",{className:"breadcrumb-item pe-1",children:"Product"})}),"/\xa0",(0,g.jsx)("li",{className:"breadcrumb-item pe-3",children:"All Comments"})]})}),(0,g.jsx)(l.A,{children:(0,g.jsx)(u.N_,{to:{pathname:"/product",state:{prod_id:N,data:k}},children:(0,g.jsx)("button",{className:"btn btn-sm btn-primary ",children:"Back To Product"})})})]}),t.width>800?(0,g.jsx)(r.Fragment,{children:(0,g.jsx)("div",{className:"toolbar",id:"kt_toolbar",children:(0,g.jsxs)("div",{id:"kt_toolbar_container",className:"container-fluid d-flex flex-stack",children:[(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between ",children:(0,g.jsx)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:(0,g.jsxs)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:[k.parent_product_code," ",(0,g.jsx)("span",{className:"text-muted mt-1 fw-bold fs-8 header-custom-2 ",children:"Aggregate Code"})]})})}),""==k.clean_description?(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between"}):(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between",children:(0,g.jsxs)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:[" ",(0,g.jsx)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:k.clean_description})]})}),(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between"})]})})}):null,(0,g.jsx)("div",{className:"row gy-5 g-xl-10",style:{marginTop:"20px"}}),1==J?(0,g.jsx)(c.A,{tip:"Loading...",size:"large",style:{justifyContent:"center"}}):(0,g.jsx)("div",{className:"row g-5 gx-xl-8",children:(0,g.jsx)("div",{className:"col-xl-12",children:(0,g.jsx)("div",{className:"card-xxl-stretch mb-4 mb-xxl-7",children:(0,g.jsx)("div",{className:"row gy-5 g-xl-4",children:(0,g.jsxs)("div",{className:"card",children:[(0,g.jsxs)("div",{className:"card-header border-0 pt-2",children:[(0,g.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,g.jsx)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:"All Comments"})}),(0,g.jsx)("form",{onSubmit:e=>{Y({keyword:"",user:""}),$(null),F(null),B(null),e.preventDefault();let t={productid:N,from_date:z,to_date:H,comment:I.keyword,added_by:Q};try{fetch("".concat(b.u.MAIN_ENDPOINT,"/api/search-historical-comments"),{method:"post",headers:{Authorization:"Bearer ".concat(j),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(t)}).then((function(e){200==e.status&&e.json().then((function(e){p(!1),T(e.data.historical_comments),p(!1),x(!1),Z(!1)}))})).catch((function(e){p(!1)}))}catch(n){console.log("ERR>>>",n)}},children:(0,g.jsx)(i.A,{children:(0,g.jsxs)("div",{className:"card-toolbar",children:[(0,g.jsx)(l.A,{xs:{span:5,offset:1},lg:{span:7,offset:1},children:(0,g.jsx)(A,{onChange:e=>(e=>{if(e){let t=m()(e[0]).format("YYYY-MM-DD"),n=m()(e[1]).format("YYYY-MM-DD");F(t),F(t),B(n)}else ee(N,1,w)})(e)})}),(0,g.jsx)(l.A,{xs:{span:5,offset:1},lg:{span:4,offset:1},children:(0,g.jsx)("input",{type:"text",className:"form-control form-control-solid",placeholder:"Filter by keyword",name:"keyword",value:I.keyword,onChange:e=>{Y({...I,[e.target.name]:e.target.value})}})}),(0,g.jsx)(l.A,{xs:{span:5,offset:1},lg:{span:4,offset:1},children:(0,g.jsx)(a.A,{placeholder:"Please Select User",value:Q,onChange:e=>{$(e)},children:K.map((e=>(0,g.jsx)(v,{value:e.id,children:e.username})))})}),(0,g.jsx)(l.A,{xs:{span:2,offset:1},lg:{span:2,offset:1},children:(0,g.jsx)("button",{className:"btn btn-sm btn-primary ",type:"submit",children:"Submit"})}),(0,g.jsx)(l.A,{xs:{span:2,offset:1},lg:{span:2},children:(0,g.jsx)("button",{className:"btn btn-sm btn-danger ",type:"reset",onClick:()=>{ee(N,1,w),Z(!1),$(null)},children:"Reset"})})]})})})]}),(0,g.jsxs)("div",{className:"card-body py-3 table-align",children:[(0,g.jsx)("div",{className:"table-responsive h-600px table-scroll",children:(0,g.jsxs)("table",{className:"table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 main-table ",children:[(0,g.jsx)("thead",{children:(0,g.jsxs)("tr",{className:"fw-bolder text-muted",children:[(0,g.jsx)("th",{className:"min-w-100px",style:{textAlign:"left"},children:"Comment"}),(0,g.jsx)("th",{className:"min-w-200px",style:{textAlign:"left"},children:"Added by"}),(0,g.jsxs)("th",{className:"min-w-200px",style:{textAlign:"left"},onClick:()=>{0==w?(w++,G("Asc"),ee(N,R,w)):(w--,G("Desc"),ee(N,R,w))},children:["Added on","Desc"!=U?(0,g.jsx)(f.aO,{className:"text-hover-primary cursor-pointer",path:"/media/icons/duotune/arrows/arr062.svg"}):(0,g.jsx)(f.aO,{className:"text-hover-primary cursor-pointer",path:"/media/icons/duotune/arrows/arr068.svg"})]})]})}),(0,g.jsx)("tbody",{children:void 0!=S&&S.map((e=>(0,g.jsxs)("tr",{children:[(0,g.jsx)("td",{style:{maxWidth:"250px",textAlign:"left"},children:(0,g.jsx)("span",{className:"text-gray text-hover-primary fw-bold fs-6 align-left",children:(0,g.jsx)(y(),{lines:1,more:"Show more",less:"Show less",anchorClass:"",children:e.comment})})}),(0,g.jsx)("td",{style:{minWidth:"200px",textAlign:"left"},children:(0,g.jsx)("span",{className:"text-dark fw-bolder text-hover-primary d-block fs-6 m-2",children:e.added_by})}),(0,g.jsx)("td",{style:{minWidth:"200px",textAlign:"left"},children:(0,g.jsx)("span",{className:"text-dark fw-bolder text-hover-primary d-block fs-6 m-2",children:m()(e.created_at).format("DD-MMM-YYYY")})})]})))})]})}),1==X?(0,g.jsx)(d.A,{defaultCurrent:1,total:L,onChange:e=>{D(e),ee(void 0!=P.location.state?P.location.state.data:localStorage.getItem("productId"),e,w)},current:R,style:{margin:"2%"}}):""]})]})})})})})]})})}))},11645:(e,t,n)=>{n.d(t,{A:()=>r});const r=n(30227).A},95150:(e,t,n)=>{n.d(t,{A:()=>r});const r=(0,n(65043).createContext)({})},30227:(e,t,n)=>{n.d(t,{A:()=>f});var r=n(64467),a=n(58168),s=n(82284),o=n(98139),i=n.n(o),l=n(65043),c=n(35296),d=n(95150),u=function(e,t){var n={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var a=0;for(r=Object.getOwnPropertySymbols(e);a<r.length;a++)t.indexOf(r[a])<0&&Object.prototype.propertyIsEnumerable.call(e,r[a])&&(n[r[a]]=e[r[a]])}return n};var p=["xs","sm","md","lg","xl","xxl"];const f=l.forwardRef((function(e,t){var n=l.useContext(c.QO),o=n.getPrefixCls,f=n.direction,h=l.useContext(d.A),m=h.gutter,x=h.wrap,y=h.supportFlexGap,b=e.prefixCls,g=e.span,v=e.order,j=e.offset,w=e.push,A=e.pull,N=e.className,O=e.children,S=e.flex,T=e.style,k=u(e,["prefixCls","span","order","offset","push","pull","className","children","flex","style"]),C=o("col",b),_={};p.forEach((function(t){var n={},o=e[t];"number"===typeof o?n.span=o:"object"===(0,s.A)(o)&&(n=o||{}),delete k[t],_=(0,a.A)((0,a.A)({},_),(0,r.A)((0,r.A)((0,r.A)((0,r.A)((0,r.A)((0,r.A)({},"".concat(C,"-").concat(t,"-").concat(n.span),void 0!==n.span),"".concat(C,"-").concat(t,"-order-").concat(n.order),n.order||0===n.order),"".concat(C,"-").concat(t,"-offset-").concat(n.offset),n.offset||0===n.offset),"".concat(C,"-").concat(t,"-push-").concat(n.push),n.push||0===n.push),"".concat(C,"-").concat(t,"-pull-").concat(n.pull),n.pull||0===n.pull),"".concat(C,"-rtl"),"rtl"===f))}));var E=i()(C,(0,r.A)((0,r.A)((0,r.A)((0,r.A)((0,r.A)({},"".concat(C,"-").concat(g),void 0!==g),"".concat(C,"-order-").concat(v),v),"".concat(C,"-offset-").concat(j),j),"".concat(C,"-push-").concat(w),w),"".concat(C,"-pull-").concat(A),A),N,_),P={};if(m&&m[0]>0){var W=m[0]/2;P.paddingLeft=W,P.paddingRight=W}if(m&&m[1]>0&&!y){var L=m[1]/2;P.paddingTop=L,P.paddingBottom=L}return S&&(P.flex=function(e){return"number"===typeof e?"".concat(e," ").concat(e," auto"):/^\d+(\.\d+)?(px|em|rem|%)$/.test(e)?"0 0 ".concat(e):e}(S),!1!==x||P.minWidth||(P.minWidth=0)),l.createElement("div",(0,a.A)({},k,{style:(0,a.A)((0,a.A)({},P),T),className:E,ref:t}),O)}))},28821:(e,t,n)=>{n.d(t,{A:()=>y});var r=n(58168),a=n(64467),s=n(82284),o=n(5544),i=n(98139),l=n.n(i),c=n(65043),d=n(35296),u=n(46058),p=n(19304),f=n(29592),h=n(95150),m=function(e,t){var n={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(n[r]=e[r]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var a=0;for(r=Object.getOwnPropertySymbols(e);a<r.length;a++)t.indexOf(r[a])<0&&Object.prototype.propertyIsEnumerable.call(e,r[a])&&(n[r[a]]=e[r[a]])}return n};(0,f.P)("top","middle","bottom","stretch"),(0,f.P)("start","end","center","space-around","space-between","space-evenly");function x(e,t){var n=c.useState("string"===typeof e?e:""),r=(0,o.A)(n,2),a=r[0],i=r[1];return c.useEffect((function(){!function(){if("string"===typeof e&&i(e),"object"===(0,s.A)(e))for(var n=0;n<p.ye.length;n++){var r=p.ye[n];if(t[r]){var a=e[r];if(void 0!==a)return void i(a)}}}()}),[JSON.stringify(e),t]),a}const y=c.forwardRef((function(e,t){var n=e.prefixCls,i=e.justify,f=e.align,y=e.className,b=e.style,g=e.children,v=e.gutter,j=void 0===v?0:v,w=e.wrap,A=m(e,["prefixCls","justify","align","className","style","children","gutter","wrap"]),N=c.useContext(d.QO),O=N.getPrefixCls,S=N.direction,T=c.useState({xs:!0,sm:!0,md:!0,lg:!0,xl:!0,xxl:!0}),k=(0,o.A)(T,2),C=k[0],_=k[1],E=c.useState({xs:!1,sm:!1,md:!1,lg:!1,xl:!1,xxl:!1}),P=(0,o.A)(E,2),W=P[0],L=P[1],M=x(f,W),R=x(i,W),D=(0,u.A)(),I=c.useRef(j);c.useEffect((function(){var e=p.Ay.subscribe((function(e){L(e);var t=I.current||0;(!Array.isArray(t)&&"object"===(0,s.A)(t)||Array.isArray(t)&&("object"===(0,s.A)(t[0])||"object"===(0,s.A)(t[1])))&&_(e)}));return function(){return p.Ay.unsubscribe(e)}}),[]);var Y=O("row",n),z=function(){var e=[void 0,void 0];return(Array.isArray(j)?j:[j,void 0]).forEach((function(t,n){if("object"===(0,s.A)(t))for(var r=0;r<p.ye.length;r++){var a=p.ye[r];if(C[a]&&void 0!==t[a]){e[n]=t[a];break}}else e[n]=t})),e}(),F=l()(Y,(0,a.A)((0,a.A)((0,a.A)((0,a.A)({},"".concat(Y,"-no-wrap"),!1===w),"".concat(Y,"-").concat(R),R),"".concat(Y,"-").concat(M),M),"".concat(Y,"-rtl"),"rtl"===S),y),H={},B=null!=z[0]&&z[0]>0?z[0]/-2:void 0,U=null!=z[1]&&z[1]>0?z[1]/-2:void 0;if(B&&(H.marginLeft=B,H.marginRight=B),D){var G=(0,o.A)(z,2);H.rowGap=G[1]}else U&&(H.marginTop=U,H.marginBottom=U);var J=(0,o.A)(z,2),q=J[0],Q=J[1],$=c.useMemo((function(){return{gutter:[q,Q],wrap:w,supportFlexGap:D}}),[q,Q,w,D]);return c.createElement(h.A.Provider,{value:$},c.createElement("div",(0,r.A)({},A,{className:F,style:(0,r.A)((0,r.A)({},H),b),ref:t}),g))}))},47419:(e,t,n)=>{n.d(t,{A:()=>r});const r=n(28821).A},28671:(e,t,n)=>{Object.defineProperty(t,"__esModule",{value:!0});var r=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),a=n(65043),s=l(a),o=l(n(65173)),i=l(n(94069));function l(e){return e&&e.__esModule?e:{default:e}}function c(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!==typeof t&&"function"!==typeof t?e:t}var d=function(e){function t(){var e,n,r;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t);for(var a=arguments.length,s=Array(a),o=0;o<a;o++)s[o]=arguments[o];return n=r=c(this,(e=t.__proto__||Object.getPrototypeOf(t)).call.apply(e,[this].concat(s))),r.state={expanded:!1,truncated:!1},r.handleTruncate=function(e){e!==r.state.truncated&&r.setState({truncated:e})},r.toggleLines=function(e){e.preventDefault(),r.setState({expanded:!r.state.expanded})},c(r,n)}return function(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}(t,e),r(t,[{key:"render",value:function(){var e=this.props,t=e.children,n=e.more,r=e.less,a=e.lines,o=e.anchorClass,l=this.state,c=l.expanded,d=l.truncated;return s.default.createElement("div",null,s.default.createElement(i.default,{lines:!c&&a,ellipsis:s.default.createElement("span",null,"... ",s.default.createElement("a",{href:"#",className:o,onClick:this.toggleLines},n)),onTruncate:this.handleTruncate},t),!d&&c&&s.default.createElement("span",null," ",s.default.createElement("a",{href:"#",className:o,onClick:this.toggleLines},r)))}}]),t}(a.Component);d.defaultProps={lines:3,more:"Show more",less:"Show less",anchorClass:""},d.propTypes={children:o.default.node,lines:o.default.number,more:o.default.node,less:o.default.node,anchorClass:o.default.string},t.default=d,e.exports=t.default},94069:(e,t,n)=>{n.r(t),n.d(t,{default:()=>c});var r=n(65043),a=n(65173),s=n.n(a),o=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}();var l=function(e){function t(){var e;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t);for(var n=arguments.length,r=Array(n),a=0;a<n;a++)r[a]=arguments[a];var s=function(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!==typeof t&&"function"!==typeof t?e:t}(this,(e=t.__proto__||Object.getPrototypeOf(t)).call.apply(e,[this].concat(r)));return s.state={},s.styles={ellipsis:{position:"fixed",visibility:"hidden",top:0,left:0}},s.elements={},s.onResize=s.onResize.bind(s),s.onTruncate=s.onTruncate.bind(s),s.calcTargetWidth=s.calcTargetWidth.bind(s),s.measureWidth=s.measureWidth.bind(s),s.getLines=s.getLines.bind(s),s.renderLine=s.renderLine.bind(s),s}return function(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}(t,e),i(t,[{key:"componentDidMount",value:function(){var e=this.elements.text,t=this.calcTargetWidth,n=this.onResize,r=document.createElement("canvas");this.canvasContext=r.getContext("2d"),t((function(){e&&e.parentNode.removeChild(e)})),window.addEventListener("resize",n)}},{key:"componentDidUpdate",value:function(e){this.props.children!==e.children&&this.forceUpdate(),this.props.width!==e.width&&this.calcTargetWidth()}},{key:"componentWillUnmount",value:function(){var e=this.elements.ellipsis,t=this.onResize,n=this.timeout;e.parentNode.removeChild(e),window.removeEventListener("resize",t),window.cancelAnimationFrame(n)}},{key:"innerText",value:function(e){var t=document.createElement("div"),n="innerText"in window.HTMLElement.prototype?"innerText":"textContent";t.innerHTML=e.innerHTML.replace(/\r\n|\r|\n/g," ");var r=t[n],a=document.createElement("div");return a.innerHTML="foo<br/>bar","foo\nbar"!==a[n].replace(/\r\n|\r/g,"\n")&&(t.innerHTML=t.innerHTML.replace(/<br.*?[\/]?>/gi,"\n"),r=t[n]),r}},{key:"onResize",value:function(){this.calcTargetWidth()}},{key:"onTruncate",value:function(e){var t=this.props.onTruncate;"function"===typeof t&&(this.timeout=window.requestAnimationFrame((function(){t(e)})))}},{key:"calcTargetWidth",value:function(e){var t=this.elements.target,n=this.calcTargetWidth,r=this.canvasContext,a=this.props.width;if(t){var s=a||Math.floor(t.parentNode.getBoundingClientRect().width);if(!s)return window.requestAnimationFrame((function(){return n(e)}));var o=window.getComputedStyle(t),i=[o["font-weight"],o["font-style"],o["font-size"],o["font-family"]].join(" ");r.font=i,this.setState({targetWidth:s},e)}}},{key:"measureWidth",value:function(e){return this.canvasContext.measureText(e).width}},{key:"ellipsisWidth",value:function(e){return e.offsetWidth}},{key:"trimRight",value:function(e){return e.replace(/\s+$/,"")}},{key:"getLines",value:function(){for(var e=this.elements,t=this.props,n=t.lines,a=t.ellipsis,s=t.trimWhitespace,o=this.state.targetWidth,i=this.innerText,l=this.measureWidth,c=this.onTruncate,d=this.trimRight,u=[],p=i(e.text).split("\n").map((function(e){return e.split(" ")})),f=!0,h=this.ellipsisWidth(this.elements.ellipsis),m=1;m<=n;m++){var x=p[0];if(0!==x.length){var y=x.join(" ");if(l(y)<=o&&1===p.length){f=!1,u.push(y);break}if(m===n){for(var b=x.join(" "),g=0,v=b.length-1;g<=v;){var j=Math.floor((g+v)/2);l(b.slice(0,j+1))+h<=o?g=j+1:v=j-1}var w=b.slice(0,g);if(s)for(w=d(w);!w.length&&u.length;){w=d(u.pop())}y=r.createElement("span",null,w,a)}else{for(var A=0,N=x.length-1;A<=N;){var O=Math.floor((A+N)/2);l(x.slice(0,O+1).join(" "))<=o?A=O+1:N=O-1}if(0===A){m=n-1;continue}y=x.slice(0,A).join(" "),p[0].splice(0,A)}u.push(y)}else u.push(),p.shift(),m--}return c(f),u}},{key:"renderLine",value:function(e,t,n){if(t===n.length-1)return r.createElement("span",{key:t},e);var a=r.createElement("br",{key:t+"br"});return e?[r.createElement("span",{key:t},e),a]:a}},{key:"render",value:function(){var e=this,t=this.elements.target,n=this.props,a=n.children,s=n.ellipsis,i=n.lines,l=function(e,t){var n={};for(var r in e)t.indexOf(r)>=0||Object.prototype.hasOwnProperty.call(e,r)&&(n[r]=e[r]);return n}(n,["children","ellipsis","lines"]),c=this.state.targetWidth,d=this.getLines,u=this.renderLine,p=this.onTruncate,f=void 0;return"undefined"!==typeof window&&!(!t||!c)&&(i>0?f=d().map(u):(f=a,p(!1))),delete l.onTruncate,delete l.trimWhitespace,r.createElement("span",o({},l,{ref:function(t){e.elements.target=t}}),r.createElement("span",null,f),r.createElement("span",{ref:function(t){e.elements.text=t}},a),r.createElement("span",{ref:function(t){e.elements.ellipsis=t},style:this.styles.ellipsis},s))}}]),t}(r.Component);l.propTypes={children:s().node,ellipsis:s().node,lines:s().oneOfType([s().oneOf([!1]),s().number]),trimWhitespace:s().bool,width:s().number,onTruncate:s().func},l.defaultProps={children:"",ellipsis:"\u2026",lines:1,trimWhitespace:!1,width:0};const c=l},60919:()=>{}}]);
//# sourceMappingURL=659.57e13035.chunk.js.map