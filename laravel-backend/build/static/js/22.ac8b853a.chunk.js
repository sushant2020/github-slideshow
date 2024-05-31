"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[22],{39022:(e,t,a)=>{a.r(t),a.d(t,{default:()=>N});var r=a(50289),n=a(15627),o=a(64435),c=a(89421),s=a(67289),l=a(10804),d=a(82217),i=a(47419),u=a(11645),h=a(50525),p=a(60521),g=a(65043),m=a(44864),j=a(69223),A=a.n(j),x=a(86178),R=a.n(x),C=a(70579);function y(e){let t="#00FF00";if(!(e=String(e)))return"";let a=e.replace("%","").replace("-","");return 0==a?t:a>=100?"#8B0000":a>=60&&a<=99?"#FF0000":a>=15&&a<=59?"#FFBF00":a>=0&&a<=14?t:void 0}let f="";const N=()=>{let[e]=r.A.useForm();const[t,a]=(0,g.useState)(!1),[j,x]=(0,g.useState)(!1),[N,E]=(0,g.useState)("middle"),[T,v]=(0,g.useState)(!1),[S,w]=(0,g.useState)([]),[O,k]=(0,g.useState)(!1),[b,P]=(0,g.useState)(1),[D,I]=(0,g.useState)(0),[M,F]=(0,g.useState)({columnKey:"ProductAC4",order:"ascend"});(0,g.useEffect)((()=>{U()}),[M,b]);const U=async()=>{k(!0),await fetch("".concat(m.u.MAIN_ENDPOINT,"/api/get-runrate/").concat(b,"/").concat(M.columnKey,"/").concat("ascend"==M.order?1:0),{method:"get",headers:{Authorization:"Bearer ".concat(f),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){200!=e.status&&w([]),e.json().then((function(e){1==e.success&&(w(e.data.products),I(e.data.rowCnt),k(!1))}))})).catch((function(e){console.log("Fetch Error :-S",e)}))},V=[{title:"Aggregate Code",dataIndex:"ProductAC4",key:"ProductAC4",sorter:!0},{title:"Current Usage",dataIndex:"currentUsage",key:"currentUsage",sorter:!0},{title:"Target Volume",dataIndex:"targetVolume",key:"targetVolume",sorter:!0},{title:"Current run rate",dataIndex:"CurrentRunRate",key:"CurrentRunRate",sorter:!0},{title:"Projected Volume",dataIndex:"projectedVolume",key:"projectedVolume",sorter:!0},{title:"Projected Run Rate",dataIndex:"projectedRunRate",key:"projectedRunRate",sorter:!0},{title:"Projected Run Rate Over Target",dataIndex:"projectedRunRateOverTarget",key:"projectedRunRateOverTarget",sorter:!0,render:(e,t)=>{let a=y(e);return(0,C.jsx)("div",{style:{background:"".concat(a)},className:"text-center text-white fw-bold",children:e})}},{title:"Date",dataIndex:"asofdate",key:"asofdate",render:e=>(0,C.jsx)(C.Fragment,{children:R()(e).format("DD-MMM-YYYY")}),sorter:!0}],Y=e=>{const t=new Date(e),a=t.getFullYear(),r=("0"+(t.getMonth()+1)).slice(-2),n=("0"+t.getDate()).slice(-2);"".concat(a,"-").concat(r,"-").concat(n);return{year:a,month:r}},G=async e=>{let t=e&&e.Month?Y(e.Month._d):null,r=e&&e.Year?Y(e.Year._d):null,o=e&&e.AggregateCode?e.AggregateCode:null,c={year:r&&r.year?r.year:"",month:t&&t.month?t.month:"",prodcode:o};await fetch("".concat(m.u.MAIN_ENDPOINT,"/api/download-runrate"),{method:"post",headers:{Authorization:"Bearer ".concat(f),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(c)}).then((function(e){e.json().then((function(e){var t;null!==e&&void 0!==e&&null!==(t=e.data)&&void 0!==t&&t.products?((async e=>{const t=new(A().Workbook),a=t.addWorksheet("Sheet1"),r=["RANKING","AGG CODE","SIGMA CODE","PRODUCT","CURRENT USAGE","CURRENT RUNRATE","TARGET VOLUME","PROJECTED VOLUME","PROJECTED RUNRATE","PROJECTED RUNRATE OVER TARGET","DATE"];a.addRow(r),e.forEach((e=>{const{Ranking:t,ProductAC4:n,SigmaCode:o,ProductDesc:c,currentUsage:s,CurrentRunRate:l,targetVolume:d,projectedVolume:i,projectedRunRate:u,projectedRunRateOverTarget:h,asofdate:p}=e,g=[t,n,o,c,s,l,d,i,u,h,p],m=a.addRow(g),j=r.indexOf("PROJECTED RUNRATE OVER TARGET")+1;let A=y(h);e&&e.projectedRunRateOverTarget&&(m.getCell(j).fill={type:"pattern",pattern:"solid",fgColor:{argb:A&&""!=A?A.replace("#",""):""}})}));const n=await t.xlsx.writeBuffer(),o=new Blob([n],{type:"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"}),c=window.URL.createObjectURL(o);var s=document.createElement("a");s.href=c,s.setAttribute("download","PriceReview".concat(".xlsx")),s.click()})(e.data.products),a(!1)):(n.Ay.error(e.message),a(!1))}))})).catch((function(e){console.log("Fetch Error :-S",e)}))};return f=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,C.jsx)("div",{className:"pt-4 m-2",children:(0,C.jsx)("div",{className:"card",children:(0,C.jsxs)("div",{className:"card-body py-3 ",children:[(0,C.jsxs)("div",{className:"row m-4 p-2",children:[(0,C.jsx)("div",{className:"col-4",children:(0,C.jsx)("div",{className:"card-toolbar",style:{width:"100%"},children:(0,C.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,C.jsx)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:"Runrate"})})})}),(0,C.jsx)("div",{className:"col-4",children:(0,C.jsx)("div",{className:"text-center"})}),(0,C.jsx)("div",{className:"col-4",children:(0,C.jsxs)("div",{id:"outer",children:[(0,C.jsx)("div",{className:"inner p-1",children:(0,C.jsxs)(o.A,{children:[(0,C.jsx)(c.A,{type:"primary",onClick:()=>a(!0),children:"Search"}),(0,C.jsx)(c.A,{type:"primary",onClick:()=>U,children:"Reset"}),(0,C.jsx)(c.A,{type:"primary",onClick:()=>{a(!0),x(!0)},children:"Export Data"})]})}),(0,C.jsx)("div",{className:"inner p-1"})]})})]}),(0,C.jsx)("div",{className:"p-2"}),(0,C.jsx)(s.A,{loading:O,dataSource:S,columns:V,pagination:!1,onChange:(e,t,a,r)=>{F({...a})},scroll:{}}),(0,C.jsx)("div",{className:"row text-end m-4",children:(0,C.jsx)(l.A,{defaultCurrent:b,defaultPageSize:20,current:b,total:D,onChange:e=>{P(e)},hideOnSinglePage:!0,showSizeChanger:!1})}),(0,C.jsx)(d.A,{title:!1,open:t,onCancel:()=>{a(!1),x(!1)},footer:null,destroyOnClose:!0,children:(0,C.jsxs)(r.A,{form:e,onFinish:e=>{1==j?G(e):(async e=>{let t=e&&e.Month?Y(e.Month._d):null,r=e&&e.Year?Y(e.Year._d):null,n=e&&e.AggregateCode?e.AggregateCode:null,o={year:r&&r.year?r.year:"",month:t&&t.month?t.month:"",prodcode:n};await fetch("".concat(m.u.MAIN_ENDPOINT,"/api/search-runrate/1/ProductAC4/1"),{method:"post",headers:{Authorization:"Bearer ".concat(f),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(o)}).then((function(e){200!=e.status&&w([]),e.json().then((function(e){1==e.success&&(w(e.data.products),I(e.data.rowCnt),k(!1),a(!1))}))})).catch((function(e){console.log("Fetch Error :-S",e)}))})(e)},preserve:!1,children:[(0,C.jsx)("p",{children:"You Can Select Date Range 1 Month."}),(0,C.jsxs)(i.A,{children:[(0,C.jsx)(u.A,{span:12,children:(0,C.jsx)(r.A.Item,{label:"Year",name:"Year",rules:[{required:!1,message:"Please input Year!"}],children:(0,C.jsx)(h.A,{size:N,picker:"year"})})}),(0,C.jsx)(u.A,{span:12,children:(0,C.jsx)(r.A.Item,{label:"Month",name:"Month",initialValue:void 0,rules:[{required:!1,message:"Please input Month!"}],children:(0,C.jsx)(h.A,{size:N,picker:"month"})})}),(0,C.jsx)(u.A,{span:23,children:(0,C.jsx)(r.A.Item,{label:"Aggregate Code",name:"AggregateCode",rules:[{required:!1,message:"Please input Aggregate Code!"}],children:(0,C.jsx)(p.A,{placeholder:"Aggregate Code"})})})]}),(0,C.jsx)(r.A.Item,{children:(0,C.jsx)("div",{className:"d-flex justify-content-end",children:(0,C.jsx)(c.A,{htmlType:"submit",type:"default",className:"get-report-btn",style:{backgroundColor:"#FAFAFA"},children:1==j?"Download":"Search"})})})]})})]})})})}},47419:(e,t,a)=>{a.d(t,{A:()=>r});const r=a(28821).A}}]);
//# sourceMappingURL=22.ac8b853a.chunk.js.map