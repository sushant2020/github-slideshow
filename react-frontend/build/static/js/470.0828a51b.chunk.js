"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[470],{17116:(e,t,a)=>{a.d(t,{O:()=>o,i:()=>c});var r=a(44864);const n=JSON.parse(localStorage.getItem("portalUserDataToken")||""),o=e=>new Promise(((t,a)=>{try{fetch("".concat(r.u.MAIN_ENDPOINT).concat(e),{method:"get",headers:{Authorization:"Bearer ".concat(n),Accept:"application/json","Content-Type":"application/json"}}).then((e=>{e.json().then((function(e){t(e)}))})).catch((function(e){console.log("Request failed",e)}))}catch(o){console.log("ERR>>>",o)}})),c=(e,t)=>new Promise(((a,o)=>{try{fetch("".concat(r.u.MAIN_ENDPOINT).concat(e),{method:"Post",headers:{Authorization:"Bearer ".concat(n),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(t)}).then((e=>{e.json().then((function(e){a(e)}))})).catch((function(e){console.log("Request failed",e)}))}catch(c){console.log("ERR>>>",c)}}))},7470:(e,t,a)=>{a.r(t),a.d(t,{default:()=>B});var r=a(424),n=a(99442),o=a(65043),c=a(9254),s=a(50289),d=a(60521),i=a(15627),l=a(47419),p=a(11645),u=a(89421),h=a(67289),m=a(82217),x=a(94406),g=a(39998),A=a(64435),y=a(44864),f=a(57284),j=a(86178),C=a.n(j),v=a(75337),N=a(70579);const{Option:w}=c.A,S=o.createContext(null);let I="",F="";const P=e=>{let{index:t,...a}=e;const[r]=s.A.useForm();return(0,N.jsx)(s.A,{form:r,component:!1,children:(0,N.jsx)(S.Provider,{value:r,children:(0,N.jsx)("tr",{...a})})})},b=e=>{let{title:t,editable:a,children:r,dataIndex:n,record:c,handleSave:i,...l}=e;const[p,u]=(0,o.useState)(!1),h=(0,o.useRef)(null),m=(0,o.useContext)(S);(0,o.useEffect)((()=>{p&&h.current.focus()}),[p]);const x=()=>{u(!p),m.setFieldsValue({[n]:c[n]})},g=async()=>{try{const e=await m.validateFields();x(),i({...c,...e})}catch(e){console.log("Save failed:",e)}};let A=r;return a&&(A=p?(0,N.jsx)(s.A.Item,{style:{margin:0},name:n,rules:[{required:!1}],children:(0,N.jsx)(d.A,{ref:h,onPressEnter:g,onBlur:g})}):(0,N.jsx)("div",{className:"editable-cell-value-wrap",style:{paddingRight:24},onClick:x,children:r})),(0,N.jsx)("td",{...l,children:A})};I=JSON.parse(localStorage.getItem("portalUserDataToken")||""),F=JSON.parse(localStorage.getItem("portalUserDataRole")||"");const k=e=>{const[t,a]=(0,o.useState)([]),[r,n]=(0,o.useState)(!1),[j,S]=(0,o.useState)(!1),[F,k]=(0,o.useState)([]),[_,O]=(0,o.useState)([]),[T]=s.A.useForm(),[R,E]=(0,o.useState)(""),D=(0,o.useRef)(null),[M,z]=(0,o.useState)(""),[Z,B]=(0,o.useState)([]),q=(0,o.useRef)(null),[Y,J]=(0,o.useState)({key:"",value:"",children:""});(0,o.useEffect)((()=>{H()}),[e,R]);const H=()=>{n(!0),fetch("".concat(y.u.MAIN_ENDPOINT,"/api/get-supplier-pricing/").concat(e.record.ProductAC4),{method:"get",headers:{Authorization:"Bearer ".concat(I),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){200!=e.status&&n(!1),e.json().then((function(e){1==e.success&&(a(e.data),n(!1))}))})).catch((function(e){console.log("Fetch Error :-S",e),n(!1)}))},L=e=>{z(e.target.value)},V=e=>{e.preventDefault(),F.push({value:M,label:M}),z(""),setTimeout((()=>{var e;null===(e=D.current)||void 0===e||e.focus()}),0)},G={body:{row:P,cell:b}},K=[{title:"Supplier Code",dataIndex:"supp_code",key:"supp_code",className:"table-color-1",sorter:(e,t)=>e.supp_code.localeCompare(t.supp_code)},{title:"Product Code",dataIndex:"product_code",key:"product_code",sorter:(e,t)=>e.product_code.localeCompare(t.product_code)},{title:"Forecast",dataIndex:"forecast",key:"forecast",editable:!0,sorter:(e,t)=>e.forecast.localeCompare(t.forecast),render:e=>e?(0,N.jsx)(N.Fragment,{children:(0,N.jsxs)("div",{className:"form-control",children:[" ",f.A.addZero(e)]})}):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"Price",dataIndex:"price",key:"price",editable:!0,width:"10%",className:"",render:e=>e?(0,N.jsx)(N.Fragment,{children:(0,N.jsxs)("div",{className:"form-control",children:[" ",f.A.addZero(e)]})}):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:(e,t)=>e.price.localeCompare(t.price),multiple:1},{title:"Supplier Comments",dataIndex:"comments",key:"comments",editable:!0,render:e=>e?(0,N.jsx)(N.Fragment,{children:(0,N.jsxs)("div",{className:"form-control",children:[" ",e]})}):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:(e,t)=>e.price.localeCompare(t.price),multiple:1},{title:"Date",dataIndex:"date",key:"date",render:e=>e?C()(e).format("DD-MMMM-YYYY"):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:(e,t)=>e.date.localeCompare(t.date)},{title:"Buyer Comments",key:"buyer_comments",dataIndex:"buyer_comments",width:"100",render:(e,t)=>(0,N.jsx)(N.Fragment,{children:e})},{title:"Add Comment",dataIndex:"ProductAC4",key:"ProductAC4",width:200,render:(t,a,r)=>(0,N.jsx)(s.A,{onFinish:t=>(async(t,a)=>{if(!t.comment)return;let r={};r=t.comment==Y.value?{pcid:a.pcid,product_id:e.record.Product_Id,comment_id:Y.key,custom_comment:t.comment}:{pcid:a.pcid,product_id:e.record.Product_Id,comment_id:"",custom_comment:t.comment},await fetch("".concat(y.u.MAIN_ENDPOINT,"/api/add-comment"),{method:"post",headers:{Authorization:"Bearer ".concat(I),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(r)}).then((function(e){e.status,e.json().then((function(e){1==e.success&&(i.Ay.success(e.data),H())}))})).catch((function(e){console.log("Fetch Error :-S",e)}))})(t,a),children:(0,N.jsxs)(l.A,{gutter:16,children:[(0,N.jsx)(p.A,{className:"gutter-row",span:18,children:(0,N.jsx)("div",{children:(0,N.jsx)(s.A.Item,{name:"comment",rules:[{required:!1,message:"Please input your username!"}],children:(0,N.jsx)(c.A,{className:"editable-cell-value-wrap form-control mr-1",style:{paddingRight:0,height:24,width:120},onSelect:(e,t)=>{J(t)},onSearch:e=>{var t;(t=e)?fetch("".concat(y.u.MAIN_ENDPOINT,"/api/search-comment/").concat(t),{method:"get",headers:{Authorization:"Bearer ".concat(I),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){e.json().then((function(e){B(e.comments)}))})).catch((function(e){})):B([])},placeholder:"Add comment",ref:q,children:Z.map((e=>(0,N.jsx)(w,{value:e.value,children:e.value},e.key)))})})})}),(0,N.jsx)(p.A,{className:"gutter-row",span:6,children:(0,N.jsx)("div",{children:(0,N.jsx)(s.A.Item,{children:(0,N.jsx)(u.A,{type:"primary",htmlType:"submit",icon:(0,N.jsx)(v.A,{}),size:"small"})})})})]})})}].map((e=>e.editable?{...e,onCell:t=>({record:t,editable:e.editable,dataIndex:e.dataIndex,title:e.title,handleSave:e=>{(e=>{if(n(!0),(e.oldPrice===e.price||e.oldPrice==e.price)&&(e.oldComments===e.comments||e.oldComments==e.comments)&&(e.oldForecast===e.forecast||e.oldForecast==e.forecast))return void n(!1);let t;t=e.oldPrice!=e.price?{pricing_id:e.pcid,negotiated_price:e.price}:e.oldForecast!=e.forecast?{pricing_id:e.pcid,forecast:e.forecast}:{pricing_id:e.pcid,scomment:e.comments},fetch("".concat(y.u.MAIN_ENDPOINT,"/api/update-supplier-pricing"),{method:"post",headers:{Authorization:"Bearer ".concat(I),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(t)}).then((function(e){e.json().then((function(e){i.Ay.success(e.data),H(),n(!1)}))})).catch((function(e){}))})({...t,oldPrice:t.price,...e,oldComments:t.comments,oldForecast:t.forecast})}})}:e));return(0,N.jsxs)(N.Fragment,{children:[(0,N.jsx)(h.A,{components:G,columns:K,dataSource:t,pagination:!1,rowClassName:()=>"editable-row",bordered:!0,loading:r}),(0,N.jsx)(m.A,{title:"Attach Tag",open:j,onCancel:()=>{S(!1)},footer:null,destroyOnClose:!0,children:(0,N.jsxs)(s.A,{form:T,name:"control-hooks",onFinish:e=>{n(!0);var t="",a="";e.tag&&!isNaN(e.tag)?t=e.tag:a=e.tag;const r={tag_id:"".concat(t),severity:"".concat(e.severity),pcid:"".concat(R.pcid),custom_tag:"".concat(a)};fetch("".concat(y.u.MAIN_ENDPOINT,"/api/assign-tag"),{method:"post",headers:{Authorization:"Bearer ".concat(I),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(r)}).then((function(e){e.json().then((function(e){1==e.success&&(i.Ay.success(e.data),H())})),e.status})).catch((function(e){})),S(!1),n(!1)},style:{maxWidth:600},preserve:!1,children:[(0,N.jsxs)("div",{className:"m-4",children:[(0,N.jsx)(s.A.Item,{name:"tag",label:"Tag",rules:[{required:!0}],labelCol:{span:4},wrapperCol:{span:16},children:(0,N.jsx)(x.A,{style:{width:300},placeholder:"Attach tag",dropdownRender:e=>(0,N.jsxs)(N.Fragment,{children:[e,(0,N.jsx)(g.A,{style:{margin:"8px 0"}}),(0,N.jsxs)(A.A,{style:{padding:"0 8px 4px"},children:[(0,N.jsx)(d.A,{placeholder:"Please enter new tag",ref:D,value:M,onChange:L,onKeyDown:e=>e.stopPropagation()}),(0,N.jsx)(u.A,{type:"text",icon:(0,N.jsx)(v.A,{}),onClick:V,children:"Add Tag"})]})]}),options:F.map((e=>({label:e.label,value:e.value}))),allowClear:!0,showSearch:!0})}),(0,N.jsx)(s.A.Item,{name:"severity",label:"Severity",rules:[{required:!1}],labelCol:{span:4},wrapperCol:{span:16},children:(0,N.jsx)(x.A,{options:_,showSearch:!0})})]}),(0,N.jsx)(s.A.Item,{children:(0,N.jsx)(u.A,{type:"primary",htmlType:"submit",children:"Submit"})})]})})]})};var _=a(35696),O=a(10804),T=a(32064),R=a(17116);let E=new Date,D=C()(E).format("DD-MMM-YYYY"),M=new Date(E.getFullYear(),E.getMonth(),1),z=C()(M).format("DD-MMM-YYYY"),Z="";const B=(0,n.y)((()=>{const[e,t]=o.useState([]),[a,n]=(0,o.useState)([]),[c,l]=(0,o.useState)(!0),[p]=s.A.useForm(),[x,g]=(0,o.useState)(1),[A,j]=(0,o.useState)(1),[C,v]=(0,o.useState)([]),[w,S]=(0,o.useState)(0),[I,F]=(0,o.useState)(30),[P,b]=(0,o.useState)(!1),[E,M]=(0,o.useState)(!1),[B,q]=(0,o.useState)(4),[Y]=s.A.useForm(),[J,H]=(0,o.useState)({columnKey:"ProductAC4",order:"ascend"});(0,o.useEffect)((()=>{L(x),l(!0)}),[x,J]);const L=async e=>{g(e),l(!0);let a="/api/get-spot-product-pricing/1/Product_AC_4/0";if(e&&null==J&&(a="/api/get-spot-product-pricing/".concat(e,"/Product_AC_4/0")),e&&null!=J&&J&&J.order){let t="ascend"==J.order?1:0;a="/api/get-spot-product-pricing/".concat(e,"/").concat(J.columnKey,"/").concat(t),j(t)}let r=await(0,R.O)(a);r&&r.data&&r.data.products&&r.data.products.length>0?(F(r.data.products.length),t(r.data.products),S(r.data.rowCnt)):(F(30),t([]),S(0)),l(!1)},V=[{title:"Ranking",dataIndex:"Ranking",key:"Ranking",sorter:!0,fixed:!0,width:100},{title:"Agg Code",dataIndex:"ProductAC4",key:"ProductAC4",sorter:!0,fixed:!0,width:100,render:(e,t)=>(0,N.jsx)(_.A,{placement:"topLeft",title:"View Product Page",children:(0,N.jsx)(r.N_,{to:{pathname:"/product",state:{prod_id:t.productpage_id,data:t}},children:e})})},{title:"Spot Code",dataIndex:"SPOTCode",key:"SPOTCode",sorter:!0,fixed:!0,width:100,render:(e,t)=>(0,N.jsx)(_.A,{placement:"topLeft",title:"View Historical Pricing",children:(0,N.jsx)(r.N_,{to:{pathname:"/productanalytics",state:{prod_id:t.ProductAC4,spotid:t.SPOTCode,spotCode:e}},children:e})})},{title:"Description",dataIndex:"ProductDesc",key:"ProductDesc",sorter:!0,width:300},{title:"Pack Size",dataIndex:"PackSize",key:"PackSize",width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:!0},{title:"Cost",dataIndex:"Cost",key:"Cost",width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:!0},{title:"True Cost",dataIndex:"True Cost",key:"True Cost",width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:!0},{title:"Avg Cost",dataIndex:"Avg Cost",key:"Avg Cost",width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:!0},{title:"Avg Vol",dataIndex:"Avg Vol",key:"Avg Vol",width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"}),sorter:!0},{title:"COMPETITOR",children:[{},{title:"PHOENIX",dataIndex:"Phoenix",key:"Phoenix",sorter:!0,width:100,render:(e,t)=>e?(0,N.jsx)("div",{style:{background:"0"==t.phoenix_outofstock?"":"#FFFF00",padding:"0px 10px"},children:f.A.addZero(e)}):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"TRIDENT",dataIndex:"Trident",key:"Trident",sorter:!0,width:100,render:(e,t)=>e?(0,N.jsx)("div",{style:{background:"0"==t.trident_outofstock?"":"#FFFF00",padding:"0px 10px"},children:f.A.addZero(e)}):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"AAH",dataIndex:"AAH",key:"AAH",sorter:!0,width:100,render:(e,t)=>e?(0,N.jsx)("div",{style:{background:"0"==t.aah_outofstock?"":"#FFFF00",padding:"0px 10px"},children:f.A.addZero(e)}):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"COLORAMA",dataIndex:"Colorama",key:"Colorama",sorter:!0,width:100,render:(e,t)=>e?(0,N.jsx)("div",{style:{background:"0"==t.colorama_outofstock?"":"#FFFF00",padding:"0px 10px"},children:f.A.addZero(e)}):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"BESTWAY",dataIndex:"Bestway",key:"Bestway",sorter:!0,width:100,render:(e,t)=>e?(0,N.jsx)("div",{style:{background:"0"==t.bestway_outofstock?"":"#FFFF00",padding:"0px 10px"},children:f.A.addZero(e)}):(0,N.jsx)(N.Fragment,{children:"-"})}]},{title:"CONTRACTS",children:[{title:"RRP",dataIndex:"RRP",key:"RRP",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"ATOZ",dataIndex:"ATOZ",key:"ATOZ",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"87",dataIndex:"c87",key:"c87",sorter:!0,width:100,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"122",dataIndex:"c122",key:"c122",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"DC",dataIndex:"DC",key:"DC",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"DG",dataIndex:"DG",key:"DG",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"RH",dataIndex:"RH",key:"RH",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})},{title:"RBS",dataIndex:"RBS",key:"RBS",width:100,sorter:!0,render:e=>e?f.A.addZero(e):(0,N.jsx)(N.Fragment,{children:"-"})}]}],G={selectedRowKeys:C,onChange:e=>{v(e)}};return Z=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,N.jsxs)(N.Fragment,{children:[(0,N.jsx)("div",{className:"pt-4 m-2",children:(0,N.jsxs)("div",{className:"card",children:[(0,N.jsxs)("div",{className:"card-header border-0 pt-2",children:[(0,N.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,N.jsxs)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:["Price Comparision (",z,")-(",D,")"]})}),(0,N.jsxs)("div",{className:"float-right pb-1 m-2 ",children:[(0,N.jsx)(_.A,{placement:"topLeft",title:"Filter",children:(0,N.jsx)(u.A,{type:"primary",size:"large",className:"m-1",onClick:()=>{b(!0)},children:"Search"})}),(0,N.jsx)(_.A,{placement:"topLeft",title:"Reset",children:(0,N.jsx)(u.A,{type:"primary",size:"large",className:"m-1",onClick:()=>L(x),children:"Reset"})}),(0,N.jsx)(_.A,{placement:"topLeft",title:"Filter",children:(0,N.jsx)(u.A,{type:"primary",size:"large",className:"m-1",onClick:()=>{M(!0)},children:"Create Watchlist"})})]})]}),(0,N.jsx)("div",{className:"card-body py-3 ",children:(0,N.jsxs)("div",{className:"row",children:[(0,N.jsx)("div",{className:"card-toolbar",style:{width:"100%"}}),(0,N.jsx)("div",{className:"col-3"}),(0,N.jsx)("div",{className:"col-3"}),(0,N.jsx)("div",{className:"col-3",children:(0,N.jsx)("div",{className:"text-center"})})]})}),(0,N.jsx)(h.A,{loading:c,columns:V,rowSelection:G,expandable:{expandedRowRender:e=>(0,N.jsx)(N.Fragment,{children:(0,N.jsx)(k,{record:e})}),rowExpandable:e=>!0,onExpand:(e,t)=>{var a=[];e&&a.push(t.Product_Id),n(a)}},expandedRowKeys:[...a],dataSource:e,className:"p-4",size:"large",rowKey:e=>e.Product_Id,pagination:!1,onChange:(e,t,a,r)=>{H({...a})},scroll:{x:2400,y:500}}),(0,N.jsx)("div",{className:"row text-end m-4",children:(0,N.jsx)(O.A,{defaultCurrent:x,defaultPageSize:I||20,current:x,total:w,onChange:e=>{g(e)},hideOnSinglePage:!0,showSizeChanger:!1})})]})}),(0,N.jsx)(m.A,{open:P,onCancel:()=>{b(!1)},footer:!1,destroyOnClose:!0,children:(0,N.jsxs)(s.A,{name:"time_related_controls",onFinish:async e=>{l(!0);let a={prodcode:e.productcode};await fetch("".concat(y.u.MAIN_ENDPOINT,"/api/search-spot-product-pricing/").concat(x,"/ProductAC4/0"),{method:"post",headers:{Authorization:"Bearer ".concat(Z),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(a)}).then((function(e){200!=e.status&&setTimeout((()=>{l(!1)}),500),e.json().then((function(e){e.success&&e&&e.data&&e.data.products&&(t(e.data.products),S(e.data.rowCnt),l(!1)),"No any product pricing data found"==e.data.error&&(t([]),S(1),b(!1),i.Ay.warning(e.message),setTimeout((()=>{l(!1)}),500))}))})).catch((function(e){console.log("Fetch Error :-S",e),setTimeout((()=>{l(!1)}),500)})),b(!1)},style:{maxWidth:600},className:"p-5",children:[(0,N.jsx)(s.A.Item,{name:"productcode",label:"Product Code",rules:[{required:!0,message:"Please enter Product Code!"}],children:(0,N.jsx)(d.A,{placeholder:"Search Product"})}),(0,N.jsx)(s.A.Item,{children:(0,N.jsx)(u.A,{type:"primary",htmlType:"submit",children:"Submit"})})]})}),(0,N.jsx)(m.A,{open:E,onCancel:()=>{M(!1)},footer:!1,destroyOnClose:!0,children:(0,N.jsxs)(s.A,{name:"time_related_controls",onFinish:e=>{let t=new Array,a=localStorage.getItem("userId"),r=D;C.map((e=>{t.push({product_id:e})}));let n={products:t,type:B,inserted_by:a,as_of_date:r};if(!(t.length>0))return i.Ay.error("Please select products to create watchlist"),void M(!1);fetch("".concat(y.u.MAIN_ENDPOINT,"/api/add-bulkproducts-watchlist"),{method:"post",headers:{Authorization:"Bearer ".concat(Z),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(n)}).then((function(e){200==e.status&&e.json().then((function(e){v([]),M(!1),i.Ay.success(e.data),M(!1)}))})).catch((function(e){}))},style:{maxWidth:600},className:"p-5",children:[(0,N.jsx)(s.A.Item,{name:"types",label:"Types",children:(0,N.jsxs)(T.Ay.Group,{defaultValue:B,onChange:e=>{q(e.target.value)},children:[(0,N.jsx)(T.Ay,{value:4,children:"Buyer Watchlist"}),(0,N.jsx)(T.Ay,{value:5,children:"Offers"})]})}),(0,N.jsx)(s.A.Item,{children:(0,N.jsx)(u.A,{type:"primary",htmlType:"submit",children:"Submit"})})]})})]})}))},57284:(e,t,a)=>{a.d(t,{A:()=>n});class r{static capitalizeFirstLetter(e){return e.charAt(0).toUpperCase()+e.slice(1)}}r.addZero=e=>{if(null==e)return e;{const t=e;if(!t||""==t||null==t)return;return"."==(null===t||void 0===t?void 0:t.charAt(0))?"0"+t:e}};const n=r},75337:(e,t,a)=>{a.d(t,{A:()=>d});var r=a(89379),n=a(65043);const o={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M482 152h60q8 0 8 8v704q0 8-8 8h-60q-8 0-8-8V160q0-8 8-8z"}},{tag:"path",attrs:{d:"M192 474h672q8 0 8 8v60q0 8-8 8H160q-8 0-8-8v-60q0-8 8-8z"}}]},name:"plus",theme:"outlined"};var c=a(22172),s=function(e,t){return n.createElement(c.A,(0,r.A)((0,r.A)({},e),{},{ref:t,icon:o}))};const d=n.forwardRef(s)},9254:(e,t,a)=>{a.d(t,{A:()=>y});var r=a(58168),n=a(82284),o=a(5544),c=a(98139),s=a.n(c),d=a(62149),i=a(18574),l=a(65043),p=a(35296),u=a(94406),h=a(12701),m=u.A.Option;function x(e){return e&&e.type&&(e.type.isSelectOption||e.type.isSelectOptGroup)}var g=function(e,t){var a,c=e.prefixCls,g=e.className,A=e.popupClassName,y=e.dropdownClassName,f=e.children,j=e.dataSource,C=(0,d.A)(f);if(1===C.length&&(0,h.zO)(C[0])&&!x(C[0])){var v=(0,o.A)(C,1);a=v[0]}var N,w=a?function(){return a}:void 0;return N=C.length&&x(C[0])?f:j?j.map((function(e){if((0,h.zO)(e))return e;switch((0,n.A)(e)){case"string":return l.createElement(m,{key:e,value:e},e);case"object":var t=e.value;return l.createElement(m,{key:t,value:t},e.text);default:return}})):[],l.createElement(p.TG,null,(function(a){var n=(0,a.getPrefixCls)("select",c);return l.createElement(u.A,(0,r.A)({ref:t},(0,i.A)(e,["dataSource"]),{prefixCls:n,popupClassName:A||y,className:s()("".concat(n,"-auto-complete"),g),mode:u.A.SECRET_COMBOBOX_MODE_DO_NOT_USE},{getInputElement:w}),N)}))},A=l.forwardRef(g);A.Option=m;const y=A},11645:(e,t,a)=>{a.d(t,{A:()=>r});const r=a(30227).A},39998:(e,t,a)=>{a.d(t,{A:()=>l});var r=a(58168),n=a(64467),o=a(98139),c=a.n(o),s=a(65043),d=a(35296),i=function(e,t){var a={};for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.indexOf(r)<0&&(a[r]=e[r]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var n=0;for(r=Object.getOwnPropertySymbols(e);n<r.length;n++)t.indexOf(r[n])<0&&Object.prototype.propertyIsEnumerable.call(e,r[n])&&(a[r[n]]=e[r[n]])}return a};const l=function(e){var t=s.useContext(d.QO),a=t.getPrefixCls,o=t.direction,l=e.prefixCls,p=e.type,u=void 0===p?"horizontal":p,h=e.orientation,m=void 0===h?"center":h,x=e.orientationMargin,g=e.className,A=e.children,y=e.dashed,f=e.plain,j=i(e,["prefixCls","type","orientation","orientationMargin","className","children","dashed","plain"]),C=a("divider",l),v=m.length>0?"-".concat(m):m,N=!!A,w="left"===m&&null!=x,S="right"===m&&null!=x,I=c()(C,"".concat(C,"-").concat(u),(0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)((0,n.A)({},"".concat(C,"-with-text"),N),"".concat(C,"-with-text").concat(v),N),"".concat(C,"-dashed"),!!y),"".concat(C,"-plain"),!!f),"".concat(C,"-rtl"),"rtl"===o),"".concat(C,"-no-default-orientation-margin-left"),w),"".concat(C,"-no-default-orientation-margin-right"),S),g),F=(0,r.A)((0,r.A)({},w&&{marginLeft:x}),S&&{marginRight:x});return s.createElement("div",(0,r.A)({className:I},j,{role:"separator"}),A&&"vertical"!==u&&s.createElement("span",{className:"".concat(C,"-inner-text"),style:F},A))}},47419:(e,t,a)=>{a.d(t,{A:()=>r});const r=a(28821).A}}]);
//# sourceMappingURL=470.0828a51b.chunk.js.map