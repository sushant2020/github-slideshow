"use strict";(self.webpackChunkdemo1=self.webpackChunkdemo1||[]).push([[878],{40878:(e,a,s)=>{s.r(a),s.d(a,{default:()=>x});var c=s(65043),l=s(424),r=s(99442),t=s(86178),d=s.n(t),i=s(44864),n=s(70579);let o="";const x=(0,r.y)((e=>{const[a,s]=(0,c.useState)(!1),[r,t]=(0,c.useState)(""),[x,m]=(0,c.useState)([]);(0,c.useEffect)((()=>{let a=e.location.state;void 0!=a&&localStorage.setItem("Tagid",a.tag_id);let s=void 0!=a?a.tag_id:localStorage.getItem("Tagid");h(s)}),[]);const h=e=>{try{fetch("".concat(i.u.MAIN_ENDPOINT,"/api/tag-with-all-products/").concat(e),{method:"get",headers:{Authorization:"Bearer ".concat(o),Accept:"application/json","Content-Type":"application/json"}}).then((function(e){200!=e.status&&(localStorage.clear(),window.location.reload()),e.json().then((function(e){m(e.products),t(e.tag[0].name)}))})).catch((function(e){s(!1)}))}catch(a){}};return o=JSON.parse(localStorage.getItem("portalUserDataToken")||""),(0,n.jsx)(c.Fragment,{children:1==a?(0,n.jsx)("div",{style:{marginLeft:"50%"},children:(0,n.jsx)("div",{className:"spinner-border m-2",role:"status",children:(0,n.jsx)("span",{className:"sr-only",children:"Loading..."})})}):(0,n.jsxs)(c.Fragment,{children:[(0,n.jsxs)("ol",{className:"breadcrumb breadcrumb-dot fs-6 fw-bold",style:{margin:"1%"},children:[(0,n.jsx)(l.N_,{to:"/home",className:"pe-1 cursor-pointer",children:(0,n.jsx)("li",{className:"breadcrumb-item pe-1",children:"Home"})}),"/ \xa0",(0,n.jsx)("li",{className:"breadcrumb-item pe-3",children:"Tag-Product Details"})]}),(0,n.jsx)("div",{className:"row gy-5 g-xl-10",style:{marginTop:"1%"},children:(0,n.jsx)("div",{className:"col-xl-3",children:(0,n.jsx)("div",{className:"card-xxl-stretch mb-5 mb-xxl-8",children:(0,n.jsx)("div",{className:"row gy-5 g-xl-7",children:(0,n.jsx)("div",{className:"card",children:(0,n.jsx)("div",{className:"card-header border-0 pt-2",style:{alignSelf:"center"},children:(0,n.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,n.jsx)("span",{className:"card-label fw-bolder fs-2 mb-1",style:{color:"rgb(138, 38, 80)"},children:r})})})})})})})}),(0,n.jsx)("div",{className:"row g-5 gx-xxl-8",children:(0,n.jsx)("div",{className:"col-xxl-12",children:(0,n.jsx)("div",{className:"card-xxl-stretch mb-5 mb-xxl-8",children:(0,n.jsx)("div",{className:"row gy-5 g-xl-4",children:(0,n.jsxs)("div",{className:"card",children:[(0,n.jsxs)("div",{className:"card-header border-0 pt-2",children:[(0,n.jsx)("h3",{className:"card-title align-items-start flex-column",children:(0,n.jsx)("span",{className:"card-label fw-bolder fs-3 mb-1",style:{color:"rgb(138, 38, 80)"},children:"Products"})}),(0,n.jsx)("div",{className:"card-toolbar"})]}),(0,n.jsx)("div",{className:"card-body py-3 ",children:(0,n.jsx)("div",{className:"table-responsive h-500px",children:(0,n.jsxs)("table",{className:"table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4",children:[(0,n.jsx)("thead",{children:(0,n.jsxs)("tr",{className:"fw-bolder text-muted",children:[(0,n.jsx)("th",{className:"w-20px"}),(0,n.jsx)("th",{className:"min-w-100px",children:"Aggregate Code"}),(0,n.jsx)("th",{className:"min-w-150px",children:"Description"}),(0,n.jsx)("th",{className:"min-w-100px",children:"Added On"})]})}),(0,n.jsx)("tbody",{children:void 0!==x&&x.map((e=>(0,n.jsxs)("tr",{children:[(0,n.jsx)("td",{}),(0,n.jsx)("td",{children:(0,n.jsx)(l.N_,{to:{pathname:"/product",state:{prod_id:e.prod_id,aggregate_code:e.ac4,clean_description:e.clean_description,created_at:e.created_at,product_code:e.product_code}},children:(0,n.jsx)("span",{className:"text-danger fw-bolder text-hover-primary d-block fs-6 text-decoration-underline link",children:e.ac4?e.ac4:"-"})})}),(0,n.jsx)("td",{children:(0,n.jsx)("span",{className:"text-dark fw-bolder text-hover-primary d-block fs-6",children:e.clean_description?e.clean_description:"-"})}),(0,n.jsx)("td",{children:(0,n.jsx)("span",{className:"text-dark fw-bolder text-hover-primary d-block fs-6",children:e.created_at?d()(e.created_at).format("DD-MMMM-YYYY"):"-"})})]})))})]})})})]})})})})})]})})}))}}]);
//# sourceMappingURL=878.63061137.chunk.js.map