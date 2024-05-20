import React, {Component} from "react";
import {Table, Divider, Button, Modal ,Space , Tag, Popconfirm, Form, Row, Col, Input, Select} from "antd";
import App from "../App";
// import { withApollo } from "react-apollo";

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined
  
  } from '@ant-design/icons';
  
  const { Option } = Select;
  const { Search } = Input;
  
  const data = [
    {
      key: '1',
      suppName: 'ALLPHA',
      prodCode: 'MO13',
      grnNo: "0",
      prodQty:"200",
      grnQty:"52",
      qtyDesc:"-",
      prodValue:"0",
      grnValue:"0",
      grnPrice:"0",
      receiptDate:"3-3-2017",
      orderDate:"3-3-2017"
    },
    {
      key: '2',
      suppName: 'RECKIT',
      prodCode: '400-1681',
      grnNo: "0",
      prodQty:"684",
      grnQty:"684",
      qtyDesc:"16",
      prodValue:"0",
      grnValue:"0",
      grnPrice:"0",
      receiptDate:"6-09-2017",
      orderDate:"6-09-2017"
    },
    {
      key: '3',
      suppName: 'NEOLAB',
      prodCode: '1FAM42',
      grnNo: "4",
      prodQty:"0",
      grnQty:"100",
      qtyDesc:"28",
      prodValue:"0",
      grnValue:"1200",
      grnPrice:"1.2",
      orderDate:"20-05-2021",
      receiptDate:"20-05-2021",
    },
    {
      key: '4',
      suppName: 'SOLVAY',
      prodCode: '2TRI',
      grnNo: "2",
      prodQty:"4000",
      grnQty:"4000",
      qtyDesc:"100M",
      prodValue:"4800",
      grnValue:"4800",
      grnPrice:"1.2",
      receiptDate:"11-05-2021",
      orderDate:"11-05-2021"
    },
    // {
    //   key: '5',
    //   suppName: 'BELLS',
    //   prodCode: '3ALM50',
    //   grnNo: "1",
    //   prodQty:"1",
    //   grnQty:"-",
    //   qtyDesc:"-",
    //   prodValue:"1",
    //   grnValue:"1",
    //   grnPriceL:"1",
    //   orderDate:"20-05-2021"
    // },
    // {
    //   key: '6',
    //   suppName: 'DEXCEL',
    //   prodeCode: '1ENA20',
    //   grnNo: "1",
    //   prodQty:"1",
    //   grnQty:"-",
    //   qtyDesc:"-",
    //   prodValue:"1",
    //   grnValue:"1",
    //   grnPriceL:"1",
    //   orderDate:"20-05-2021"
    // },
    // {
    //   key: '7',
    //   suppName: 'TILLOMED',
    //   prodCode: '1ENA52',
    //   grnNo: "1",
    //   prodQty:"1",
    //   grnQty:"-",
    //   qtyDesc:"-",
    //   prodValue:"1",
    //   grnValue:"1",
    //   grnPrice:"1",
    //   orderDate:"20-05-2021"
    // },
  ];
  
  const role = [
    { value: "Administrator", label: "Administrator" },
    { value: "Buyer", label: "Buyer" },
    { value: "Manager", label: "Manager" },
    { value: "Staff", label: "Staff" },
  ];

class Grn extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           productName: '',
           collapsed: false,
           tableData: [],
           searchVal: '',
        };
      }

    componentDidMount(){
        // console.log("In CDM");
        const prodName = localStorage.getItem("productName");
        if (prodName && prodName != "") {
          this.setState({
            productName: prodName,
          });
        }
    }

    onSearch=(e)=>{
        console.log("In Search",e.target.value)
        let n = e.target.value;
         // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
         // const found = data.find(element => element.name == n);
         let arr = [];
        //  return
         data.map((i, j) => {
           if (i.suppName.toLowerCase().includes(n.toLowerCase()) 
              // || i.key.includes(n) 
              || i.prodCode.toLowerCase().includes(n.toLowerCase())
              // || i.code.toLowerCase().includes(n.toLowerCase())
              // || i.address.toLowerCase().includes(n.toLowerCase())
            ) {
              // console.log("In If",i)
             arr.push(i);
           }
         });
  
       
        // //  data.map((p)=>{
        // //   p.tags.map((q)=>{{
        // //    // console.log("...Tags",q)
        // //     if (q.toLowerCase().includes(n.toLowerCase())) {
        // //      arr.push(p);
        // //    }
        // //   }}) 
        // // })
         
        //  console.log("Search Index:: ",index)
        console.log("Search Name UM:: ",arr)
         this.setState({
           tableData: arr
         })
         if(n == ''){
          // console.log("Search No Result:: ")
           
           this.setState({
             tableData: data
           })
         }
       }

       onProducts=()=>{
        this.props.history.push('/products')
       }

    render(){
        // console.log("In Render");

        const columns = [
            // {
            //   title: 'No',
            //   dataIndex: 'key',
            // },
            {
              title: 'Supp Code / Name',
              // dataIndex: 'email',
              key: 'code',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.suppName}</h3>
                )
              }
            },
            {
              title: 'Prod Code',
              // dataIndex: 'name',
              key: 'pCode',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodCode}</h3>
                )
              }
            },
            
            {
              title: 'Grn No',
              // dataIndex: 'email',
              key: 'grnNo',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnNo}</h3>
                )
              }
            },
            {
              title: 'Prod Qty',
              // dataIndex: 'email',
              key: 'pQty',
              sorter: (a, b) => a.prodQty - b.prodQty,
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodQty}</h3>
                )
              }
            },
            {
              title: 'Grn Qty',
              // dataIndex: 'email',
              key: 'gQty',
              sorter: (a, b) => a.grnQty - b.grnQty,
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnQty}</h3>
                )
              }
            },
            {
                title: 'Qty Desc',
                // dataIndex: 'email',
                key: 'Qdesc',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.qtyDesc}</h3>
                  )
                }
              },
              {
                title: 'Prod Value',
                // dataIndex: 'email',
                key: 'pValue',
                sorter: (a, b) => a.prodValue - b.prodValue,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodValue}</h3>
                  )
                }
              },
              {
                title: 'Grn Value',
                // dataIndex: 'email',
                sorter: (a, b) => a.grnValue - b.grnValue,
                key: 'gValue',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnValue}</h3>
                  )
                }
              },
              {
                title: 'Grn Price',
                // dataIndex: 'email',
                key: 'gPrice',
                sorter: (a, b) => a.grnPrice - b.grnPrice,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnPrice}</h3>
                  )
                }
              },
              {
                title: 'Order Date',
                // dataIndex: 'email',
                key: 'date',
                // sorter: (a, b) => a.orderDate - b.orderDate,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.orderDate}</h3>
                  )
                }
              },
              {
                title: 'Receipt Date',
                // dataIndex: 'email',
                key: 'date',
                // sorter: (a, b) => a.receiptDate - b.receiptDate,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.receiptDate}</h3>
                  )
                }
              },
            
          ];

     return(
      <App header={
        <Input placeholder="Search Prod Code / Supplier Code/Name in GRN" size="large"
      allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
      // onChange={(val)=>this.setState({searchVal: val})}
      // onSearch={(val)=>this.onSearch(val)}
         onChange={(val)=>this.onSearch(val)}
      />
      // <Search placeholder="Search Prod Code / Supplier Code/Name in GRN" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} 
      
      // onSearch={(val)=>this.onSearch(val)}/>
    }
      >
        {this.state.productName ?
            <h4 
             style={{fontSize:"14px",color:'#213A87',float:"left",marginTop:"1%",marginLeft:"2%",cursor:"pointer"}} onClick={()=> {this.onProducts()}}>
              go to product <span style={{fontSize:"18px",fontWeight:"bold",color:'#213A87',cursor:"pointer"}}>
                            {this.state.productName ? this.state.productName : null}
                           </span>
             </h4>
             : null}

             <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               {/* Products Overview  */}
             </Divider>

             {/* <Search placeholder="Search Prod Code / Supplier Code/Name" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 

         allowClear style={{width:'30%'}} /> */}

             <Table columns={columns} 
             rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} 
             style={{margin:'2%'}} 
             className='product' 
             pagination={false}
             />

         </App>
     )   
    }

}

export default Grn