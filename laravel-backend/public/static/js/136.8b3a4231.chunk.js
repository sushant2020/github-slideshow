"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[136],{99136:(e,t,a)=>{a.r(t),a.d(t,{default:()=>f});var r=a(65043),s=a(99442),n=a(50525),c=a(35696),d=a(89421),o=a(67289),i=a(10804),l=a(82217),p=a(50289),m=a(44864),u=a(57284),h=a(86178),x=a.n(h),g=a(70579);const{RangePicker:j}=n.A;let y="";const f=(0,s.y)((function(e){const t=(0,s.W6)(),[a,n]=(0,r.useState)([]),[h,f]=(0,r.useState)(1),[b,N]=(0,r.useState)("Sigmapricing"),[A,S]=(0,r.useState)([]),[C,k]=(0,r.useState)(0),[w,v]=(0,r.useState)(!1),[I,P]=(0,r.useState)("abc"),[D,F]=(0,r.useState)(1),[T,_]=(0,r.useState)(1),[O,Y]=(0,r.useState)("abc"),[M,R]=(0,r.useState)("abc"),[Z,z]=(0,r.useState)({}),[B,E]=(0,r.useState)(!1),[H,L]=(0,r.useState)({columnKey:"date",order:"ascend"}),[K,U]=(0,r.useState)({columnKey:"date",order:"ascend"}),[G,W]=(0,r.useState)(e&&e.location&&e.location.state&&e.location.state.prod_id?e.location.state.prod_id:"ACAT31"),[J,X]=(0,r.useState)(e&&e.location&&e.location.state&&e.location.state.spotCode?e.location.state.spotCode:"1ACAR19"),[q,Q]=(0,r.useState)(e&&e.location&&e.location.state&&e.location.state.spotCode?e.location.state.spotCode:"1ACY22L");(0,r.useEffect)((()=>{$("SupplierPricing"==b?G:q),V(J),F(1)}),[D,K,b,H]);const V=async e=>{await fetch("".concat(m.u.MAIN_ENDPOINT,"/api/get-analytics-page-header/").concat(e),{method:"get",headers:{Authorization:"Bearer ".concat(y),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){e.json().then((function(e){e&&"No any product Supplier data found"!=e.data.error&&z(e.data.header)}))})).catch((e=>{console.log(e)}))},$=async e=>{let t;v(!0),t="SupplierPricing"==b?"ascend"==K.order?1:0:"ascend"==H.order?1:0,await fetch("".concat(m.u.MAIN_ENDPOINT,"/api/").concat("SupplierPricing"==b?"get-supplier-historical-pricing":"get-competitor-contract-pricing","/").concat(e,"/").concat(D,"/").concat("SupplierPricing"==b?K.columnKey:H.columnKey,"/").concat(t),{method:"get",headers:{Authorization:"Bearer ".concat(y),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){e.json().then((function(e){if(e){if(e&&!e.data.products||"NOT FOUND"==e.data.error)return S([]),f(1),void v(!1);e&&e.data&&e.data.products&&("SupplierPricing"==b?(S(e.data.products),f(e.data.rowCnt)):(n(e.data.products),f(e.data.rowCnt))),v(!1)}}))})).catch((function(e){}))};y=JSON.parse(localStorage.getItem("portalUserDataToken")||"");const ee=[{title:"Product Code",dataIndex:"product_code",key:"product_code",width:100,sorter:(e,t)=>e.pcid.localeCompare(t.pcid)},{title:"Supplier Code",dataIndex:"supp_code",key:"supp_code",width:100,sorter:(e,t)=>e.supp_code.localeCompare(t.supp_code)},{title:"Price",dataIndex:"price",key:"price",width:100,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"}),sorter:(e,t)=>e.price.localeCompare(t.price)},{title:"Supplier Comment",dataIndex:"comments",key:"comments",width:200},{title:"Forecast",dataIndex:"forecast",key:"forecast",width:100,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"}),sorter:(e,t)=>e.forecast.localeCompare(t.forecast)},{title:"Date",dataIndex:"date",key:"date",width:100,render:e=>e?x()(e).format("DD-MMM-YYYY"):(0,g.jsx)(g.Fragment,{children:"-"}),sorter:(e,t)=>e.date.localeCompare(t.date)},{title:"Buyer Comments",key:"buyer_comments",dataIndex:"buyer_comments",width:200,render:e=>e&&0!=e.length?(0,g.jsx)(g.Fragment,{children:e}):(0,g.jsx)(g.Fragment,{children:"-"})}],te=[{title:"Spot Code",dataIndex:"SPOTCode",key:"SPOTCode",sorter:!0,fixed:!0},{title:"COMPETITOR",children:[{title:"PHOENIX",dataIndex:"Phoenix",sorter:!0,key:"Phoenix",render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"TRIDENT",dataIndex:"Trident",sorter:!0,key:"Trident",render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"AAH",dataIndex:"AAH",sorter:!0,key:"AAH",render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"COLORAMA",dataIndex:"Colorama",sorter:!0,key:"Colorama",render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"BESTWAY",dataIndex:"Bestway",key:"Bestway",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})}]},{title:"CONTRACTS",children:[{title:"87",dataIndex:"c87",key:"c87",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"122",dataIndex:"c122",key:"c122",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"DC",dataIndex:"DC",key:"DC",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"DG",dataIndex:"DG",key:"DG",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})},{title:"RH",dataIndex:"RH",key:"RH",sorter:!0,render:e=>e?u.A.addZero(e):(0,g.jsx)(g.Fragment,{children:"-"})}]},{title:"Date",dataIndex:"date",key:"date",sorter:!0,render:e=>e?x()(e).format("DD-MMM-YYYY"):(0,g.jsx)(g.Fragment,{children:"-"})}];return(0,g.jsxs)(g.Fragment,{children:[(0,g.jsx)(r.Fragment,{children:(0,g.jsx)("div",{className:"toolbar",id:"kt_toolbar",children:(0,g.jsxs)("div",{id:"kt_toolbar_container",className:"container-fluid d-flex flex-stack",children:[(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between ",children:(0,g.jsx)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:(0,g.jsxs)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:[Z.parent_product_code," ",(0,g.jsx)("span",{className:"text-muted mt-1 fw-bold fs-8 header-custom-2 ",children:"Aggregate Code"})]})})}),(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between ",children:(0,g.jsx)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:(0,g.jsxs)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:[Z.spot_code,(0,g.jsx)("span",{className:"text-muted mt-1 fw-bold fs-8 header-custom-2 p-1",children:"Spot Code"})]})})}),(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between",children:(0,g.jsxs)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:[" ",(0,g.jsx)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:Z.clean_description})]})}),(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between",children:(0,g.jsx)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:(0,g.jsxs)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:[Z.pack_size," ",(0,g.jsx)("span",{className:"text-muted mt-1 fw-bold fs-8 header-custom-2",children:"DT Pack"})]})})}),(0,g.jsx)("div",{className:"d-flex align-items-center py-5 justify-content-space-between",children:(0,g.jsx)("a",{className:"btn btn-outline btn-outline-dashed m-1",style:{cursor:"text"},children:(0,g.jsxs)("span",{className:"card-label fs-2 mb-1 text-purple header-custom",style:{color:"rgb(138, 38, 80)"},children:[Z.Ranking," ",(0,g.jsx)("span",{className:"text-muted mt-1 fw-bold fs-8 header-custom-2",children:"Ranking"})]})})})]})})}),(0,g.jsx)("div",{className:"col-xxl-12 mt-20",children:(0,g.jsx)("div",{className:"card-xxl-stretch mb-5 mb-xxl-8 h-600",children:(0,g.jsx)("div",{className:"row gy-5 g-xl-4",children:(0,g.jsxs)("div",{className:"card",children:[(0,g.jsxs)("div",{className:"card-header border-0 pt-2",children:[(0,g.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,g.jsxs)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:["Product Analytics"," ",a&&a.length>=1?(0,g.jsxs)(g.Fragment,{children:["(",a[0].ProductAC4+"-"+a[0].ProductDesc,")"]}):""]})}),(0,g.jsxs)("div",{className:"float-right pb-1 m-2 ",children:[(0,g.jsx)(c.A,{placement:"topLeft",title:"Filter",children:(0,g.jsx)(d.A,{type:"primary",size:"large",className:"m-1",onClick:()=>{E(!0)},children:"Search"})}),(0,g.jsx)(c.A,{placement:"topLeft",title:"Reset",children:(0,g.jsx)(d.A,{type:"primary",size:"large",className:"m-1",onClick:()=>$("SupplierPricing"==b?G:q),children:"Reset"})}),(0,g.jsx)(c.A,{placement:"topLeft",title:"Back to Buyer Section",children:(0,g.jsx)(d.A,{type:"primary",size:"large",className:"m-1",onClick:t.goBack,children:"Back"})})]})]}),(0,g.jsx)("div",{className:"card-body py-3 ",children:(0,g.jsxs)("div",{className:"row mb-4",children:[(0,g.jsx)("div",{className:"col-3",children:(0,g.jsx)("div",{className:"card-toolbar",style:{width:"100%"},children:(0,g.jsx)("div",{children:" "})})}),(0,g.jsx)("div",{className:"col-3"}),(0,g.jsx)("div",{className:"col-3"}),(0,g.jsx)("div",{className:"col-3",children:(0,g.jsx)("div",{className:"text-center"})})]})}),(0,g.jsxs)("div",{className:"tab-content",id:"myTabContent",children:[(0,g.jsx)(o.A,{dataSource:"SupplierPricing"==b?A:a,columns:"SupplierPricing"==b?ee:te,className:"m-4",loading:w,pagination:!1,size:"large",onChange:(e,t,a,r)=>{"SupplierPricing"==b?U({...a}):L({...a})},scroll:{x:"SupplierPricing"==b?"auto":2800,y:500}}),(0,g.jsx)("div",{className:"row text-end",children:(0,g.jsx)("div",{className:"col float-end  m-4",children:(0,g.jsx)(i.A,{defaultCurrent:D,defaultPageSize:20,total:h,onChange:e=>{F(e)},hideOnSinglePage:!0,showSizeChanger:!1})})})]})]})})})}),(0,g.jsx)(l.A,{open:B,onCancel:()=>{E(!1)},footer:!1,destroyOnClose:!0,children:(0,g.jsxs)(p.A,{name:"time_related_controls",onFinish:e=>{let t="",a="";if(!e.productcode&&!e.daterange)return;e.daterange&&(t=x()(e.daterange[0]).format("YYYY-MM-DD"),a=x()(e.daterange[1]).format("YYYY-MM-DD"));var r={sdate:t||"",edate:a||""};fetch("".concat(m.u.MAIN_ENDPOINT,"/api/").concat("SupplierPricing"==b?"search-supplier-historical-pricing":"search-competitor-contract-pricing","/").concat("SupplierPricing"==b?G:q,"/1/date/1"),{method:"post",headers:{Authorization:"Bearer ".concat(y),Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(r)}).then((function(e){e.json().then((function(e){if(e){if(e&&!e.data.products||"NOT FOUND"==e.data.error)return S([]),f(1),n([]),k(1),void v(!1);e&&e.data&&e.data.products&&("SupplierPricing"==b?(S(e.data.products),f(e.data.rowCnt)):(n(e.data.products),f(e.data.rowCnt))),v(!1)}}))})).catch((function(e){console.log("Fetch Error :-S",e)})),E(!1)},style:{maxWidth:600},children:[(0,g.jsx)(p.A.Item,{name:"daterange",label:"From Date - To Date",children:(0,g.jsx)(j,{})}),(0,g.jsx)(p.A.Item,{children:(0,g.jsx)(d.A,{type:"primary",htmlType:"submit",children:"Submit"})})]})})]})}))},57284:(e,t,a)=>{a.d(t,{A:()=>s});class r{static capitalizeFirstLetter(e){return e.charAt(0).toUpperCase()+e.slice(1)}}r.addZero=e=>{if(null==e)return e;{const t=e;if(!t||""==t||null==t)return;return"."==(null===t||void 0===t?void 0:t.charAt(0))?"0"+t:e}};const s=r}}]);
//# sourceMappingURL=136.8b3a4231.chunk.js.map