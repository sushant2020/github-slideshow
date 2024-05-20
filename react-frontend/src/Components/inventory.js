import React, {Component} from "react";
import {Table, Divider, Button, Modal ,Space , Tag, Popconfirm, Form, Row, Col, Input, Select, Card} from "antd";
import App from "../App";
import Appm from "../mApp"
import MediaQuery from 'react-responsive';
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
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
      code: '1ACE16',
      pStock: '7',
      allStock: '0',
      allAft: '0',
      onOrder: '0',
      backorder: '0',
      lgDate: '',
      comp: '1',
      lpp:'3.7',
      avgCost:'3.702',
      trueCost:'3.15',
      minStock:'0',
      lsDate:'',
      stdCost:'3.15',
    },
    {
        key: '2',
        code: '1ACE16',
        pStock: '0',
        allStock: '0',
        allAft: '0',
        onOrder: '0',
        backorder: '0',
        lgDate: '',
        comp: '1',
        lpp:'3.15',
        avgCost:'3.15',
        trueCost:'3.15',
        minStock:'0',
        lsDate:'',
        stdCost:'3.15',
    },
    // {
    //   key: '3',
    //   name: 'SOLVAY',
    //   age: 32,
    //   phone: '9988776633',
    //   address: 'Sydney No. 1 Lake Park',
    //   code: '2TRI',
    //   email: 'joe.black@gmail.com',
    //   status: 'Active',
    //   tags: ['Manager'],
    // },
    // {
    //   key: '4',
    //   name: 'NEOLAB',
    //   age: 32,
    //   phone: '9988776622',
    //   address: 'London, Park Lane no. 2',
    //   code: '1FAM42',
    //   email: 'joe.black@gmail.com',
    //   status: 'Active',
    //   tags: ['Manager'],
    // },
    // {
    //   key: '5',
    //   name: 'BELLS',
    //   age: 32,
    //   phone: '9988776611',
    //   address: 'London, Park Lane no. 3',
    //   code: '3ALM50',
    //   email: 'joe.black@gmail.com',
    //   status: 'Active',
    //   tags: ['Manager'],
    // },
    // {
    //   key: '6',
    //   name: 'DEXCEL',
    //   age: 32,
    //   phone: '9988776612',
    //   address: 'London, Park Lane no. 4',
    //   code: '1ENA20',
    //   email: 'joe.black@gmail.com',
    //   status: 'Active',
    //   tags: ['Manager'],
    // },
    // {
    //   key: '7',
    //   name: 'TILLOMED',
    //   age: 32,
    //   phone: '9988776613',
    //   address: 'London, Park Lane no. 5',
    //   code: '1ENA52',
    //   email: 'joe.black@gmail.com',
    //   status: 'Active',
    //   tags: ['Manager'],
    // },
  ];
  
  const role = [
    { value: "Administrator", label: "Administrator" },
    { value: "Buyer", label: "Buyer" },
    { value: "Manager", label: "Manager" },
    { value: "Staff", label: "Staff" },
  ];

class Inventory extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           productName: '',
           collapsed: false,
           tableData: [],
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
         if (i.code.toLowerCase().includes(n.toLowerCase()) 
            || i.pStock.includes(n) 
            // || i.prodCode.toLowerCase().includes(n.toLowerCase())
            // || i.code.toLowerCase().includes(n.toLowerCase())
            // || i.address.toLowerCase().includes(n.toLowerCase())
          ) {
            console.log("In If",i)
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
              title: 'Prod Code',
              // dataIndex: 'email',
              key: 'code',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.code}</h3>
                )
              }
            },
            
            {
              title: 'Phys Stock',
              // dataIndex: 'name',
              key: 'pStock',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.pStock}</h3>
                )
              }
            },
            
            {
              title: 'Alloc Stock',
              // dataIndex: 'email',
              key: 'allStock',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.allStock}</h3>
                )
              }
            },
            {
              title: 'Alloc After',
              // dataIndex: 'email',
              key: 'allAft',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.allAft}</h3>
                )
              }
            },
            {
              title: 'On Order',
              // dataIndex: 'email',
              key: 'onOrder',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.onOrder}</h3>
                )
              }
            },
            {
                title: 'Backorder',
                // dataIndex: 'email',
                key: 'backorder',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.backorder}</h3>
                  )
                }
              },
              {
                title: 'LG-Date',
                // dataIndex: 'email',
                key: 'lgDate',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.lgDate}</h3>
                  )
                }
              },
              {
                title: 'Company',
                // dataIndex: 'email',
                key: 'comp',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.comp}</h3>
                  )
                }
              },
              {
                title: 'LPP Cost',
                // dataIndex: 'email',
                key: 'lpp',
                sorter: (a, b) => a.lpp - b.lpp,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.lpp}</h3>
                  )
                }
              },
              {
                title: 'Avg Cost',
                // dataIndex: 'email',
                key: 'avgCost',
                sorter: (a, b) => a.avgCost - b.avgCost,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.avgCost}</h3>
                  )
                }
              },
              {
                title: 'True Cost',
                // dataIndex: 'email',
                key: 'trueCost',
                sorter: (a, b) => a.trueCost - b.trueCost,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.trueCost}</h3>
                  )
                }
              },
              {
                title: 'Min Stock',
                // dataIndex: 'email',
                key: 'minStock',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.minStock}</h3>
                  )
                }
              },
              {
                title: 'LS-Date',
                // dataIndex: 'email',
                key: 'lsDate',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.lsDate}</h3>
                  )
                }
              },
              {
                title: 'Std Cost',
                // dataIndex: 'email',
                key: 'stdCost',
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.stdCost}</h3>
                  )
                }
              },
            // {
            //   title: 'Code',
            //   // dataIndex: 'email',
            //   key: 'code',
            //   render:(data)=>{
            //     // console.log("Data..",data)
            //     return(
            //       <h3 style={{fontWeight:'bold'}}>{data.code}</h3>
            //     )
            //   }
            // },
            // {
            //   title: 'Roles',
            //   dataIndex: 'tags',
            //   key: 'tags',
            // },
            
            // {
            //   title: 'Action',
            //   dataIndex: 'address',
            //   key: 'address',
            // },
            // {
            //   title: 'Roles',
            //   key: 'tags',
            //   dataIndex: 'tags',
            //   render: tags => (
            //     <>
            //       {tags.map(tag => {
            //         let color = tag.length > 5 ? 'geekblue' : 'green';
            //         if (tag === 'loser') {
            //           color = 'volcano';
            //         }
            //         return (
            //           <Tag color='#353b8d' key={tag} style={{fontWeight:'bold',fontSize:15}}>
            //             {tag.toUpperCase()}
            //           </Tag>
            //         );
            //       })}
            //     </>
            //   ),
            // },
            // {
            //   title: 'Status',
            //   // dataIndex: 'status',
            //   key: 'status',
            //   render:(data)=>{
            //     // console.log("Data..",data)
            //     return(
            //       <h3 style={{fontWeight:'bold',color:'green'}}>{data.status}</h3>
            //     )
            //   }
            // },
            // {
            //   title: 'Action',
            //   key: 'action',
            //   render: (text, record) => (
            //     <>
            //      <Space size="middle">
            //       {/* <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'view'})}} icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}}/>  */}
            //       <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} icon={<EditOutlined style={{fontSize:25}}/>} style={{marginLeft:"10px",background:"#353b8d",borderColor:"#353b8d"}}
            //       //onClick={() => this.handleMatch(data.prodId)}
            //       /> 
            //       <Popconfirm title="Sure to delete?" 
            //       //onConfirm={() => this.handleDelete(data.prodId)}
            //       >
            //               <Button icon={<DeleteOutlined style={{fontSize:25}}/>} size="large" type= "danger"  style={{marginLeft:"10px",cursor: "pointer", background:"#8f0021",borderColor:"#8f0021" }}/>
            //       </Popconfirm> 
            //       {/* <DeleteOutlined /> */}
            //      </Space>
            //     </>
            //   ),
            // },
          ];

          let products = data.map((p)=>{
            // console.log("record..",p)
              return(
                  <List.Item style={{marginBottom:'5px'}}>
                    <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"98%", background: "#eaeaf2"}}> 
               
               <Row >
                 <Col span={24}>
                  <h4 style={{fontSize: "14px",}}> PO-ID : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.key}</span></h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                  <h4 style={{fontSize: "14px", }}> Supplier : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>   
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Quantity : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.qty}</span></h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Price : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.price}</span>
                   </h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Created At : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>-</span>
                   </h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Created By : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>-</span></h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Note : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.note}</span>
                   </h4>
                 </Col>
                 </Row>
                 <Row>
                 <Col span={24}>
                 <h4 style={{fontSize: "14px", }}> Status : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
                 <Tag style={{fontWeight:'bold',fontSize:"14px", color:"grey"}}>
                   {p.status}
                  </Tag>
                   </span>
                   </h4>
                 </Col>
                 </Row>
                 </Card>
                      <WhiteSpace size="sm" />
                  </List.Item>
              )
          })

     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      //  <App>
      <App header={
        <Input placeholder="Search by Product Code in Inventory" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        // onSearch={(val)=>this.onSearch(val)}
        onChange={(val)=>this.onSearch(val)}
        />
      }>

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

             {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

             <Table columns={columns} 
             rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' 
             />

         </App>
          )
        }else{
          return(
              <Appm>
              
              </Appm>
          )
        }
        }
      }
      </MediaQuery>
     )   
    }

}

export default Inventory