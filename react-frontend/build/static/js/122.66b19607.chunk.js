"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[122],{17116:(e,t,a)=>{a.d(t,{O:()=>r,i:()=>i});var n=a(44864);const o=JSON.parse(localStorage.getItem("portalUserDataToken")||""),r=e=>new Promise(((t,a)=>{try{fetch("".concat(n.u.MAIN_ENDPOINT).concat(e),{method:"get",headers:{Authorization:"Bearer ".concat(o),Accept:"application/json","Content-Type":"application/json"}}).then((e=>{e.json().then((function(e){t(e)}))})).catch((function(e){console.log("Request failed",e)}))}catch(r){console.log("ERR>>>",r)}})),i=(e,t)=>new Promise(((a,r)=>{try{fetch("".concat(n.u.MAIN_ENDPOINT).concat(e),{method:"Post",headers:{Authorization:"Bearer ".concat(o),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(t)}).then((e=>{e.json().then((function(e){a(e)}))})).catch((function(e){console.log("Request failed",e)}))}catch(i){console.log("ERR>>>",i)}}))},8122:(e,t,a)=>{a.r(t),a.d(t,{default:()=>k});var n=a(65043),o=a(44864),r=a(50289),i=a(15627),l=a(64435),s=a(89421),c=a(35696),d=a(92584),u=a(67289),p=a(10804),h=a(82217),m=a(47419),A=a(11645),x=a(3428),f=a(78186),y=a(60521),C=a(50525),E=a(86178),g=a.n(E),O=a(75337),T=a(67407),v=a(17116),S=a(69223),N=a.n(S),j=a(57284),I=a(424),R=a(70579);const k=()=>{let[e]=r.A.useForm();const[t,a]=(0,n.useState)([]),[E,S]=(0,n.useState)(!1),[k,b]=(0,n.useState)(1),[w,P]=(0,n.useState)(0),[D,F]=(0,n.useState)("BUYER"),[_,L]=(0,n.useState)(!1),[H,B]=(0,n.useState)(!1),[Y,U]=(0,n.useState)({}),[M,V]=(0,n.useState)([]),[W,G]=(0,n.useState)([]),[z,q]=(0,n.useState)({}),[X,Z]=(0,n.useState)({}),[K,J]=(0,n.useState)(""),[Q,$]=(0,n.useState)({}),[ee,te]=(0,n.useState)(!1),[ae,ne]=(0,n.useState)(""),[oe,re]=(0,n.useState)({}),[ie,le]=(0,n.useState)(!1),[se,ce]=(0,n.useState)(""),[de,ue]=(0,n.useState)(null),[pe,he]=(0,n.useState)(!0);let[me,Ae]=(0,n.useState)({AAH:!1,BESTWAY:!1,COLORAMA:!1,PHOENIX:!1,TRIDENT:!1}),[xe,fe]=(0,n.useState)({AAH:null,BESTWAY:null,COLORAMA:null,PHOENIX:null,TRIDENT:null});const[ye,Ce]=(0,n.useState)({columnKey:"AsOfDate",order:"ascend"});(0,n.useEffect)((()=>{Te()}),[D,ye,k]);let Ee="";const ge=t=>{console.log(t,"recordrecordrecord"),L(!0);let a=[],n={};e.resetFields(["PHOENIX","TRIDENT","AAH","COLORAMA","BESTWAY","phoenix_note","trident_note","aah_note","colorama_note","bestway_note"]),t.phoenix.name="PHOENIX",t.trident.name="TRIDENT",t.aah.name="AAH",t.colorama.name="COLORAMA",t.bestway.name="BESTWAY",a.push(t.phoenix),a.push(t.trident),a.push(t.aah),a.push(t.colorama),a.push(t.bestway);let o={};for(let e=0;e<a.length;e++){const t=a[e];o[t.name]=t,console.log(t,"elementelementelement"),me[t.name]=1==t.outofstock,V((()=>[...M,t.name])),G((()=>[...W,t.name])),n[t.name]=t.price}q(n),U(o),Z(t),J(t.ProductAC4),$(a),te(!0),L(!1)},Oe=(e,t)=>{e.preventDefault(),me[t]=e.target.checked,V((()=>[...M,e.target.checked]))},Te=async()=>{he(!0),L(!0),await fetch("".concat(o.u.MAIN_ENDPOINT,"/api/").concat("BUYER"==D?"get-competitor-pricing-buyerset":"PRESET"==D?"get-competitor-pricing-preset":"BUYERWATCHLIST"==D?"get-competitor-pricing-buyer-watchlist":"JAYLESHBHAI"==D?"get-competitor-pricing-preset-jayleshbhai":"get-undercost-lines","/").concat(k,"/").concat(ye&&ye.columnKey?ye.columnKey:"AsOfDate","/").concat(ye&&ye.order&&"ascend"==ye.order?0:1),{method:"get",headers:{Authorization:"Bearer ".concat(Ee),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){200!=e.status&&a([]),e.json().then((function(e){1==e.success&&(a(e.data.products),P(e.data.rowCnt))}))})).catch((function(e){console.log("Fetch Error :-S",e)})),L(!1)},ve=[{title:"ACTION",key:"action",fixed:!0,width:100,hidden:!pe,render:(e,t)=>(0,R.jsx)(R.Fragment,{children:(0,R.jsxs)(l.A,{size:"middle",children:[(0,R.jsx)(s.A,{type:"primary",disabled:!(!t||!(t.aah&&t.aah.price||t.bestway&&t.bestway.price||t.colorama&&t.colorama.price||t.phoenix&&t.phoenix.price||t.trident&&t.trident.price||"Search"==ae)),onClick:()=>ge(t),icon:(0,R.jsx)(O.A,{})}),(0,R.jsx)(s.A,{type:"primary",onClick:()=>ge(t),icon:(0,R.jsx)(T.A,{}),disabled:!t||!(t.aah&&t.aah.price||t.bestway&&t.bestway.price||t.colorama&&t.colorama.price||t.phoenix&&t.phoenix.price||t.trident&&t.trident.price||"Search"==ae)})]})})},{title:"DATE",dataIndex:"AsOfDate",key:"AsOfDate",width:120,fixed:!0,sorter:!0,render:e=>e?g()(e).format("DD-MMM-YYYY"):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"DESCRIPTION",dataIndex:"ProductDesc",key:"ProductDesc",width:200,sorter:!0,fixed:!0},{title:"PACK",dataIndex:"PackSize",key:"PackSize",sorter:!0,width:100,fixed:!0},{title:"Ranking",dataIndex:"Ranking",key:"Ranking",width:100},{title:"AGG CODE",dataIndex:"ProductAC4",key:"ProductAC4",sorter:!0,width:100},{title:"PROD CODE",dataIndex:"SPOTCode",key:"SPOTCode",sorter:!0,width:100,render:(e,t)=>(0,R.jsx)(c.A,{placement:"topLeft",title:"View Historical Pricing",children:(0,R.jsx)(I.N_,{to:{pathname:"/product-details",state:{prod_id:t.ProductAC4,spotid:t.SPOTCode,spotCode:e}},children:e})})},{title:"Cost",dataIndex:"Cost",key:"Cost",width:100,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"}),sorter:!0},{title:"True Cost",dataIndex:"True Cost",key:"True Cost",width:100,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"}),sorter:!0},{title:"Avg Cost",dataIndex:"Avg Cost",key:"Avg Cost",width:100,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"}),sorter:!0},{title:"Avg Vol",dataIndex:"Avg Vol",key:"Avg Vol",width:100,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"}),sorter:!0},{title:"COMPETITOR",children:[{},{title:"PHONEX",dataIndex:"phoenix",key:"phoenix",sorter:!0,width:100,render:e=>e?(0,R.jsx)("p",{style:{color:e&&1==e.outofstock?"red":"#000"},children:e.price||(0,R.jsx)(R.Fragment,{children:"-"})}):e},{title:"TRIDENT",dataIndex:"trident",key:"trident",sorter:!0,width:100,render:e=>e?(0,R.jsx)("p",{style:{color:e&&1==e.outofstock?"red":"#000"},children:e.price||(0,R.jsx)(R.Fragment,{children:"-"})}):e},{title:"AAH",dataIndex:"aah",key:"aah",sorter:!0,width:100,render:e=>e?(0,R.jsx)("p",{style:{color:e&&1==e.outofstock?"red":"#000"},children:e.price||(0,R.jsx)(R.Fragment,{children:"-"})}):e},{title:"COLORAMA",dataIndex:"colorama",key:"colorama",sorter:!0,width:100,render:e=>e?(0,R.jsx)("p",{style:{color:e&&1==e.outofstock?"red":"#000"},children:e.price||(0,R.jsx)(R.Fragment,{children:"-"})}):e},{title:"BESTWAY",dataIndex:"bestway",key:"bestway",sorter:!0,width:100,render:e=>e?(0,R.jsx)("p",{style:{color:e&&1==e.outofstock?"red":"#000"},children:e.price||(0,R.jsx)(R.Fragment,{children:"-"})}):e}]},{title:"CONTRACTS",children:[{title:"RRP",dataIndex:"RRP",key:"RRP",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"ATOZ",dataIndex:"ATOZ",key:"ATOZ",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"87",dataIndex:"c87",key:"c87",sorter:!0,width:100,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"122",dataIndex:"c122",key:"c122",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"DC",dataIndex:"DC",key:"DC",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"DG",dataIndex:"DG",key:"DG",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"RH",dataIndex:"RH",key:"RH",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"RBS",dataIndex:"RBS",key:"RBS",width:100,sorter:!0,render:e=>e?j.A.addZero(e):(0,R.jsx)(R.Fragment,{children:"-"})}]},{title:"SENSITIVITY",dataIndex:"sensitivity",key:"sensitivity",width:100,sorter:!0},{title:"Buyer Comments",dataIndex:"buyer_comments",key:"buyer_comments",width:300,sorter:!0,render:e=>e?(0,R.jsx)(R.Fragment,{children:e}):(0,R.jsx)(R.Fragment,{children:"-"})},{title:"Supplier Comments",dataIndex:"supplier_comments",key:"supplier_comments",width:300,sorter:!0,render:e=>e?(0,R.jsx)(R.Fragment,{children:e}):(0,R.jsx)(R.Fragment,{children:"-"})}].filter((e=>!e.hidden)),Se=e=>{ne("Search"==e?"Search":"Export Data"),S(!E)},Ne=e=>{const t=new Date(e),a=t.getFullYear(),n=("0"+(t.getMonth()+1)).slice(-2),o=("0"+t.getDate()).slice(-2);return"".concat(a,"-").concat(n,"-").concat(o)},je=async e=>{let t={};if(e&&e.enddate&&e.startdate){const a=Ne(e.startdate),n=Ne(e.enddate);t={...t,sdate:a,edate:n,group:"BUYER"==D?1:"PRESET"==D?2:"BUYERWATCHLIST"==D?4:"UNDERCOSTLINE"==D?7:6}}let a=await(0,v.i)("/api/download-competitor-pricing",t);if(null!==a&&void 0!==a&&a.error&&"No any competitor pricing data found"==(null===a||void 0===a?void 0:a.error))return void i.Ay.error("downloadData.error");let n=[];Object.keys(a.data).map((e=>{let t=e.split("-"),o={RANKING:"".concat(t[2],"-").concat(t[1],"-").concat(t[0]),AGGCODE:"",PRODCODE:"",DESCRIPTION:"",COST:"","TRUE COST":"","AVG COST":"","AVG VOL":"",PACK:"",PHOENIX:"",TRIDENT:"",AAH:"",COLORAMA:"",BESTWAY:"",SENSITIVITY:""};n.push(o),a.data[e].map((e=>{let t={RANKING:e.Ranking,"AGG CODE":e.ProductAC4,"PROD CODE":e.SPOTCode,DESCRIPTION:e.ProductDesc,COST:e.Cost,TRUECOST:e.TrueCost,AVGCOST:e.AvgCost,AVGVOL:e.AvgVol,PACK:e.PackSize,PHOENIX:e.phoenix,TRIDENT:e.trident,AAH:e.aah,COLORAMA:e.colorama,BESTWAY:e.bestway,SENSITIVITY:e.sensitivity,aah_outofstock:e.aah_outofstock,colorama_outofstock:e.colorama_outofstock,phoenix_outofstock:e.phoenix_outofstock,trident_outofstock:e.trident_outofstock,bestway_outofstock:e.bestway_outofstock};n.push(t)}))})),Ie(n)},Ie=async e=>{const t=new(N().Workbook),a=t.addWorksheet("Sheet1"),n=["RANKING","AGG CODE","PROD CODE","DESCRIPTION","COST","TRUE COST","AVG COST","AVG VOL","PACK","PHOENIX","TRIDENT","AAH","COLORAMA","BESTWAY","SENSITIVITY"];a.addRow(n),e.forEach((e=>{const{RANKING:t,"PROD CODE":o,"AGG CODE":r,DESCRIPTION:i,COST:l,TRUECOST:s,AVGCOST:c,AVGVOL:d,PACK:u,PHOENIX:p,TRIDENT:h,AAH:m,COLORAMA:A,BESTWAY:x,SENSITIVITY:f}=e,y=[t,o,r,i,l,s,c,d,u,p,h,m,A,x,f],C=a.addRow(y),E=n.indexOf("AAH")+1,g=n.indexOf("PHOENIX")+1,O=n.indexOf("TRIDENT")+1,T=n.indexOf("COLORAMA")+1,v=n.indexOf("BESTWAY")+1;e&&e.aah_outofstock&&1==e.aah_outofstock&&(C.getCell(E).fill={type:"pattern",pattern:"solid",fgColor:{argb:"FFFF00"}}),e&&e.phoenix_outofstock&&1==e.phoenix_outofstock&&(C.getCell(g).fill={type:"pattern",pattern:"solid",fgColor:{argb:"FFFF00"}}),e&&e.trident_outofstock&&1==e.trident_outofstock&&(C.getCell(O).fill={type:"pattern",pattern:"solid",fgColor:{argb:"FFFF00"}}),e&&e.bestway_outofstock&&1==e.bestway_outofstock&&(C.getCell(v).fill={type:"pattern",pattern:"solid",fgColor:{argb:"FFFF00"}}),e&&e.colorama_outofstock&&1==e.colorama_outofstock&&(C.getCell(T).fill={type:"pattern",pattern:"solid",fgColor:{argb:"FFFF00"}})}));const o=await t.xlsx.writeBuffer(),r=new Blob([o],{type:"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"}),i=window.URL.createObjectURL(r);var l=document.createElement("a");l.href=i,l.setAttribute("download","PriceReview".concat(".xlsx")),l.click()};return Ee=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,R.jsx)(R.Fragment,{children:(0,R.jsx)("div",{className:"pt-4 m-2",children:(0,R.jsx)("div",{className:"card",children:(0,R.jsxs)("div",{className:"card-body py-3 ",children:[(0,R.jsxs)("div",{className:"row m-4 p-2",children:[(0,R.jsx)("div",{className:"col-4",children:(0,R.jsx)("div",{className:"card-toolbar",style:{width:"100%"},children:(0,R.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,R.jsx)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:"Sigma Competitor Price Review"})})})}),(0,R.jsx)("div",{className:"col-4",children:(0,R.jsx)("div",{className:"text-center",children:(0,R.jsx)(c.A,{placement:"topLeft",title:"Notify Pricer",children:(0,R.jsx)(d.A,{title:"Are you sure? Do you want to mark watchlist as completed and notify Pricer?",onConfirm:async()=>{B(!0);let e={list_type:"BUYER"==D?1:"PRESET"==D?2:"BUYERWATCHLIST"==D?4:"UNDERCOSTLINE"==D?7:6,type:"4",dataCount:t.length};await fetch("".concat(o.u.MAIN_ENDPOINT,"/api/notify-user"),{method:"post",headers:{Authorization:"Bearer ".concat(Ee),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(e)}).then((function(e){e.json().then((function(e){1==e.success&&(i.Ay.success(e.message),Te())}))})).catch((function(e){})),B(!1)},okText:"Yes",cancelText:"No",children:(0,R.jsx)(s.A,{type:"primary",loading:H,children:"Notify Pricer"})})})})}),(0,R.jsx)("div",{className:"col-4",children:(0,R.jsxs)("div",{id:"outer",children:[(0,R.jsxs)("div",{className:"inner p-1",children:[(0,R.jsx)(s.A,{type:"primary",onClick:()=>{Se("Search")},children:"Search"})," ",(0,R.jsx)(s.A,{type:"primary",onClick:()=>{Te()},children:"Reset"}),"\xa0",(0,R.jsx)(s.A,{type:"primary",onClick:()=>Se("export_data"),children:"Export Data"})]}),(0,R.jsx)("div",{className:"inner p-1"})]})})]}),(0,R.jsxs)("ul",{className:"nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8 main-table",children:[(0,R.jsx)("li",{className:"nav-item",children:(0,R.jsx)("a",{className:"nav-link text-active-primary pb-4 active","data-bs-toggle":"tab",href:"#BUYER",onClick:()=>{F("BUYER"),b(1)},children:"BUYER INTEL"})}),(0,R.jsx)("li",{className:"nav-item",children:(0,R.jsx)("a",{className:"nav-link text-active-primary pb-4","data-kt-countup-tabs":"true","data-bs-toggle":"tab",href:"#BUYERWATCHLIST",onClick:()=>{F("BUYERWATCHLIST"),b(1)},children:"BUYER WATCHLIST"})}),(0,R.jsx)("li",{className:"nav-item",children:(0,R.jsx)("a",{className:"nav-link text-active-primary pb-4","data-kt-countup-tabs":"true","data-bs-toggle":"tab",href:"#PRESET",onClick:()=>{F("PRESET"),b(1)},children:"PRESET FOR OFFICE-DAILY"})}),(0,R.jsx)("li",{className:"nav-item",children:(0,R.jsx)("a",{className:"nav-link text-active-primary pb-4","data-kt-countup-tabs":"true","data-bs-toggle":"tab",href:"#PRESETFORJAYLESHBHAI",onClick:()=>{F("JAYLESHBHAI")},children:"PRESET FOR OFFICE - TWICE A WEEK"})}),(0,R.jsx)("li",{className:"nav-item",children:(0,R.jsx)("a",{className:"nav-link text-active-primary pb-4","data-kt-countup-tabs":"true","data-bs-toggle":"tab",href:"#UNDERCOSTLINE",onClick:()=>{F("UNDERCOSTLINE")},children:"UNDERCOST LINES"})})]}),(0,R.jsx)("div",{className:"tab-content",id:"myTabContent",children:(0,R.jsxs)("div",{className:"tab-pane fade show active ",id:D,role:"tabpanel",children:[(0,R.jsx)(u.A,{columns:ve,dataSource:t,loading:_,onChange:(e,t,a,n)=>{Ce({...a})},scroll:{x:2100,y:"auto"},pagination:!1}),(0,R.jsxs)("div",{className:"row text-end m-4",children:[(0,R.jsx)(p.A,{defaultCurrent:k,defaultPageSize:10,current:k,total:w,onChange:e=>{b(e)},hideOnSinglePage:!0,showSizeChanger:!1}),(0,R.jsx)(h.A,{title:K||"Update Price",open:ee,onCancel:()=>(te(!1),U({}),Z({}),J(""),void $([])),footer:null,destroyOnClose:!0,style:{right:"-400px"},width:700,children:(0,R.jsxs)(r.A,{form:e,onFinish:async t=>{try{await e.validateFields(["stockAAH","AAH","BESTWAY","COLORAMA","PHOENIX","TRIDENT"]);let a=new Intl.DateTimeFormat("en-IN",{year:"numeric",month:"2-digit",day:"2-digit"}).format(Date.now()).split("/"),n=a[2]+-+a[1]+-+a[0];if(X&&n){let e={asofdate:n,phoenix:t.PHOENIX?t.PHOENIX:null,trident:t.TRIDENT?t.TRIDENT:null,aah:t.AAH?t.AAH:null,colorama:t.COLORAMA?t.COLORAMA:null,bestway:t.BESTWAY?t.BESTWAY:null,phoenix_outofstock:me.PHOENIX?1:0,trident_outofstock:me.TRIDENT?1:0,aah_outofstock:me.AAH?1:0,colorama_outofstock:me.COLORAMA?1:0,bestway_outofstock:me.BESTWAY?1:0,phoenix_note:t.phoenix_note?t.phoenix_note:null,trident_note:t.trident_note?t.trident_note:null,aah_note:t.aah_note?t.aah_note:null,colorama_note:t.colorama_note?t.colorama_note:null,bestway_note:t.bestway_note?t.bestway_note:null,product_id:X.product_id,group:"BUYER"==D?1:"PRESET"==D?2:"BUYERWATCHLIST"==D?4:"UNDERCOSTLINE"==D?7:6,watchlist_id:X.watchlist_id},a="/api/price-review",o=await(0,v.i)(a,e);o&&o.data&&o.data&&(o.data&&o.success?(i.Ay.success(o.message),Te()):i.Ay.error(o.message))}e.resetFields(["AAH","BESTWAY","COLORAMA","PHOENIX","TRIDENT"]),Ae({AAH:!1,BESTWAY:!1,COLORAMA:!1,PHOENIX:!1,TRIDENT:!1}),fe({AAH:null,BESTWAY:null,COLORAMA:null,PHOENIX:null,TRIDENT:null})}catch(a){console.error("Validation error:",a)}te(!1)},children:[(0,R.jsxs)(m.A,{children:[(0,R.jsx)(A.A,{span:6,children:(0,R.jsx)("p",{children:"Competitor Name"})}),(0,R.jsx)(A.A,{span:4,children:(0,R.jsx)("p",{children:"Price"})}),(0,R.jsx)(A.A,{span:2,children:(0,R.jsx)("p",{children:"OOS"})}),(0,R.jsx)(A.A,{span:12,children:(0,R.jsx)("p",{children:"Notes"})}),Q&&Q.length>0&&Y&&Q.map((e=>(console.log(Y,Q,"111"),(0,R.jsxs)(R.Fragment,{children:[(0,R.jsx)(A.A,{span:6,children:(0,R.jsx)("p",{children:e.name})}),Y&&Q&&M&&(0,R.jsx)(A.A,{span:4,children:(0,R.jsx)(r.A.Item,{initialValue:e.price?e.price:null,name:e.name,rules:[{required:!1,message:"Please input Product Price!"}],children:(0,R.jsx)(x.A,{placeholder:" ---",min:0,className:"custom-input",style:{fontWeight:"600 !important"}})})}),(0,R.jsx)(A.A,{span:2,children:(0,R.jsxs)(r.A.Item,{name:"stock"+e.name,rules:[{required:!1,message:"Please input Product Stock!"}],children:[0==me[e.name]&&(0,R.jsx)(f.A,{checked:!!me[e.name]&&me[e.name],onChange:t=>Oe(t,e.name)}),1==me[e.name]&&M&&(0,R.jsx)(f.A,{checked:!!me[e.name]&&me[e.name],onChange:t=>Oe(t,e.name)})]})}),Y&&Q&&M&&(0,R.jsx)(A.A,{span:12,children:(0,R.jsx)(r.A.Item,{initialValue:e.note&&null!=e.note&&""!=e.note?e.note:null,name:e.name.toLowerCase()+"_note",rules:[{required:!1,message:"Please input notes!"}],children:(0,R.jsx)(y.A,{placeholder:" ",className:"custom-input",style:{fontWeight:"600 !important"}})})})]}))))]}),(0,R.jsx)(r.A.Item,{children:(0,R.jsx)("div",{className:"d-flex justify-content-end",children:(0,R.jsx)(s.A,{htmlType:"submit",type:"primary",className:"get-report-btn",style:{color:"#fff"},children:"Submit"})})})]})}),(0,R.jsx)(h.A,{title:ae,open:E,onCancel:()=>{S(!1)},footer:null,children:(0,R.jsxs)(r.A,{form:e,onFinish:t=>(async(t,n)=>{try{if(await e.validateFields(["enddate","prodcode","startdate"]),"Search"==ae){let e={};if(t&&!t.prodcode&&!t.enddate&&!t.startdate)return void S(!1);if(oe&&(e=t),t&&t.prodcode&&(e={...e,prodcode:t.prodcode,group:"BUYER"==D?1:"PRESET"==D?2:"BUYERWATCHLIST"==D?4:"UNDERCOSTLINE"==D?7:6}),t&&t.enddate&&t.startdate){const a=Ne(t.startdate),n=Ne(t.enddate);e={...e,sdate:a,edate:n,group:"BUYER"==D?1:"PRESET"==D?2:"BUYERWATCHLIST"==D?4:"UNDERCOSTLINE"==D?7:6}}let o="/api/search-competitor-pricing/".concat(n,"/Product_AC_4/1"),r=await(0,v.i)(o,e);re(e),r&&r.data&&r.data&&(he(!1),P(r.data.rowCnt),r.data.products.length>=0&&(he(!1),a(r.data.products)))}else je(t);S(!1),e.resetFields(["enddate","startdate","prodcode"])}catch(o){console.error("Validation error:",o)}})(t,1),children:[(0,R.jsx)("p",{children:"You Can Select Date Range 1 Month."}),(0,R.jsxs)(m.A,{children:[(0,R.jsx)(A.A,{span:12,children:(0,R.jsx)(r.A.Item,{label:"Start Date",name:"startdate",rules:[{required:!1,message:"Please input Start date!"}],children:(0,R.jsx)(C.A,{className:"rangePickerMobile",style:{width:"100%"},allowClear:!1,onChange:(t,a)=>{var n=a;g()(n).unix();e.resetFields(["toDate"]),ce(a),ue(void 0),le(!0)},disabledDate:e=>e>g()()})})}),(0,R.jsx)(A.A,{span:12,children:(0,R.jsx)(r.A.Item,{label:"End Date",name:"enddate",initialValue:void 0,rules:[{required:!1,message:"Please input End date!"}],children:(0,R.jsx)(C.A,{className:"rangePickerMobile",style:{width:"100%"},allowClear:!1,onChange:(e,t)=>{var a=t;g()(a).unix();g()(a).add(1439,"m").format("LLL");le(!1)},disabledDate:e=>((e,t)=>{let a=g()();const n=g()(t).startOf("day"),o=g()(t).add(1,"months").endOf("day");return e<n||e>o||e>a})(e,se),open:ie})})}),"Search"==ae&&(0,R.jsx)(A.A,{span:20,children:(0,R.jsx)(r.A.Item,{label:" Product Code",name:"prodcode",rules:[{required:!1,message:"Please input Product Code!"}],children:(0,R.jsx)(y.A,{placeholder:"Product Code"})})})]}),(0,R.jsx)(r.A.Item,{children:(0,R.jsx)("div",{className:"d-flex justify-content-end",children:(0,R.jsx)(s.A,{htmlType:"submit",type:"primary",className:"get-report-btn",style:{color:"#fff"},children:"Search"==ae?"Search":"Download"})})})]})})]})]})})]})})})})}},57284:(e,t,a)=>{a.d(t,{A:()=>o});class n{static capitalizeFirstLetter(e){return e.charAt(0).toUpperCase()+e.slice(1)}}n.addZero=e=>{if(null==e)return e;{const t=e;if(!t||""==t||null==t)return;return"."==(null===t||void 0===t?void 0:t.charAt(0))?"0"+t:e}};const o=n},67407:(e,t,a)=>{a.d(t,{A:()=>s});var n=a(89379),o=a(65043);const r={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M257.7 752c2 0 4-.2 6-.5L431.9 722c2-.4 3.9-1.3 5.3-2.8l423.9-423.9a9.96 9.96 0 000-14.1L694.9 114.9c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2 1-7.1 2.9L256.8 538.8c-1.5 1.5-2.4 3.3-2.8 5.3l-29.5 168.2a33.5 33.5 0 009.4 29.8c6.6 6.4 14.9 9.9 23.8 9.9zm67.4-174.4L687.8 215l73.3 73.3-362.7 362.6-88.9 15.7 15.6-89zM880 836H144c-17.7 0-32 14.3-32 32v36c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-36c0-17.7-14.3-32-32-32z"}}]},name:"edit",theme:"outlined"};var i=a(22172),l=function(e,t){return o.createElement(i.A,(0,n.A)((0,n.A)({},e),{},{ref:t,icon:r}))};const s=o.forwardRef(l)},75337:(e,t,a)=>{a.d(t,{A:()=>s});var n=a(89379),o=a(65043);const r={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M482 152h60q8 0 8 8v704q0 8-8 8h-60q-8 0-8-8V160q0-8 8-8z"}},{tag:"path",attrs:{d:"M192 474h672q8 0 8 8v60q0 8-8 8H160q-8 0-8-8v-60q0-8 8-8z"}}]},name:"plus",theme:"outlined"};var i=a(22172),l=function(e,t){return o.createElement(i.A,(0,n.A)((0,n.A)({},e),{},{ref:t,icon:r}))};const s=o.forwardRef(l)},92584:(e,t,a)=>{a.d(t,{A:()=>j});var n=a(58168),o=a(5544),r=a(51376),i=a(98139),l=a.n(i),s=a(28678),c=a(25001),d=a(65043),u=a(35296),p=function(e){return e?"function"===typeof e?e():e:null},h=a(83290),m=a(35696),A=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var o=0;for(n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(a[n[o]]=e[n[o]])}return a},x=function(e){var t=e.title,a=e.content,n=e.prefixCls;return d.createElement(d.Fragment,null,t&&d.createElement("div",{className:"".concat(n,"-title")},p(t)),d.createElement("div",{className:"".concat(n,"-inner-content")},p(a)))};const f=d.forwardRef((function(e,t){var a=e.prefixCls,o=e.title,r=e.content,i=e._overlay,l=e.placement,s=void 0===l?"top":l,c=e.trigger,p=void 0===c?"hover":c,f=e.mouseEnterDelay,y=void 0===f?.1:f,C=e.mouseLeaveDelay,E=void 0===C?.1:C,g=e.overlayStyle,O=void 0===g?{}:g,T=A(e,["prefixCls","title","content","_overlay","placement","trigger","mouseEnterDelay","mouseLeaveDelay","overlayStyle"]),v=d.useContext(u.QO).getPrefixCls,S=v("popover",a),N=v(),j=d.useMemo((function(){return i||(o||r?d.createElement(x,{prefixCls:S,title:o,content:r}):null)}),[i,o,r,S]);return d.createElement(m.A,(0,n.A)({placement:s,trigger:p,mouseEnterDelay:y,mouseLeaveDelay:E,overlayStyle:O},T,{prefixCls:S,ref:t,overlay:j,transitionName:(0,h.by)(N,"zoom-big",T.transitionName)}))}));var y=a(12701),C=a(89421),E=a(83085),g=a(38046),O=a(38097),T=a(8376),v=function(e){var t=e.prefixCls,a=e.okButtonProps,o=e.cancelButtonProps,r=e.title,i=e.cancelText,l=e.okText,s=e.okType,c=e.icon,h=e.showCancel,m=void 0===h||h,A=e.close,x=e.onConfirm,f=e.onCancel,y=d.useContext(u.QO).getPrefixCls;return d.createElement(O.A,{componentName:"Popconfirm",defaultLocale:T.A.Popconfirm},(function(e){return d.createElement("div",{className:"".concat(t,"-inner-content")},d.createElement("div",{className:"".concat(t,"-message")},c&&d.createElement("span",{className:"".concat(t,"-message-icon")},c),d.createElement("div",{className:"".concat(t,"-message-title")},p(r))),d.createElement("div",{className:"".concat(t,"-buttons")},m&&d.createElement(C.A,(0,n.A)({onClick:f,size:"small"},o),null!==i&&void 0!==i?i:e.cancelText),d.createElement(g.A,{buttonProps:(0,n.A)((0,n.A)({size:"small"},(0,E.D)(s)),a),actionFn:x,close:A,prefixCls:y("btn"),quitOnNullishReturnValue:!0,emitEvent:!0},null!==l&&void 0!==l?l:e.okText)))}))},S=void 0,N=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var o=0;for(n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(a[n[o]]=e[n[o]])}return a};const j=d.forwardRef((function(e,t){var a=e.prefixCls,i=e.placement,p=void 0===i?"top":i,h=e.trigger,m=void 0===h?"click":h,A=e.okType,x=void 0===A?"primary":A,C=e.icon,E=void 0===C?d.createElement(r.A,null):C,g=e.children,O=e.overlayClassName,T=e.onOpenChange,j=e.onVisibleChange,I=N(e,["prefixCls","placement","trigger","okType","icon","children","overlayClassName","onOpenChange","onVisibleChange"]),R=d.useContext(u.QO).getPrefixCls,k=(0,s.A)(!1,{value:void 0!==e.open?e.open:e.visible,defaultValue:void 0!==e.defaultOpen?e.defaultOpen:e.defaultVisible}),b=(0,o.A)(k,2),w=b[0],P=b[1],D=function(e,t){P(e,!0),null===j||void 0===j||j(e,t),null===T||void 0===T||T(e,t)},F=R("popover",a),_=R("popconfirm",a),L=l()(_,O);return d.createElement(f,(0,n.A)({},I,{trigger:m,prefixCls:F,placement:p,onOpenChange:function(t){var a=e.disabled;void 0!==a&&a||D(t)},open:w,ref:t,overlayClassName:L,_overlay:d.createElement(v,(0,n.A)({okType:x,icon:E},e,{prefixCls:F,close:function(e){D(!1,e)},onConfirm:function(t){var a;return null===(a=e.onConfirm)||void 0===a?void 0:a.call(S,t)},onCancel:function(t){var a;D(!1,t),null===(a=e.onCancel)||void 0===a||a.call(S,t)}}))}),(0,y.Ob)(g,{onKeyDown:function(e){var t,a;d.isValidElement(g)&&(null===(a=null===g||void 0===g?void 0:(t=g.props).onKeyDown)||void 0===a||a.call(t,e)),function(e){e.keyCode===c.A.ESC&&w&&D(!1,e)}(e)}}))}))},47419:(e,t,a)=>{a.d(t,{A:()=>n});const n=a(28821).A}}]);
//# sourceMappingURL=122.66b19607.chunk.js.map