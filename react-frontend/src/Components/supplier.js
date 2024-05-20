import React, {Component} from "react";
import {Table, Divider, Button, Modal ,Space , Card,  Tag, Popconfirm, Form, Row, Col, Input, Select} from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import App from "../App";
import MediaQuery from 'react-responsive';
import Appm from "../mApp"
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
      name: 'ALLPHA',
      code: 'MO13',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '2',
      name: 'RECKIT',
      code: '400-1681',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '3',
      name: 'SOLVAY',
      code: '2TRI',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '4',
      name: 'NEOLAB',
      code: '1FAM42',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '5',
      name: 'BELLS',
      code: '3ALM50',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '6',
      name: 'DEXCEL',
      code: '1ENA20',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
    {
      key: '7',
      name: 'TILLOMED',
      code: '1ENA52',
      stopInd: "Account Trading",
      currCode: "UK",
      company:"1",
    },
  ];
  
  const role = [
    { value: "Administrator", label: "Administrator" },
    { value: "Buyer", label: "Buyer" },
    { value: "Manager", label: "Manager" },
    { value: "Staff", label: "Staff" },
  ];

class Supplier extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           collapsed: false,
           tableData: [],
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    }

    onSearch=(n)=>{
        // console.log("In Search",n)
   
         // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
         // const found = data.find(element => element.name == n);
         let arr = [];
         data.map((i, j) => {
           if (i.name.toLowerCase().includes(n.toLowerCase()) 
          //  || i.key.includes(n) 
          //  || i.phone.toLowerCase().includes(n.toLowerCase())
            || i.code.toLowerCase().includes(n.toLowerCase())
            // || i.address.toLowerCase().includes(n.toLowerCase())
            ) {
             arr.push(i);
           }
         });
  
        //  data.map((p)=>{
        //   p.tags.map((q)=>{{
        //    // console.log("...Tags",q)
        //     if (q.toLowerCase().includes(n.toLowerCase())) {
        //      arr.push(p);
        //    }
        //   }}) 
        // })
         
         // console.log("Search Index:: ",index)
        // console.log("Search Name UM:: ",arr)
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

    render(){
        // console.log("In Render");

        const columns = [
            // {
            //   title: 'No',
            //   dataIndex: 'key',
            // },
            {
              title: 'Code',
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
              title: 'Name',
              // dataIndex: 'name',
              key: 'name',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.name}</h3>
                )
              }
            },
            
            {
              title: 'Stop Ind',
              // dataIndex: 'email',
              key: 'stopInd',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.stopInd}</h3>
                )
              }
            },
            {
              title: 'Currency Code',
              // dataIndex: 'email',
              key: 'currCode',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.currCode}</h3>
                )
              }
            },
            {
              title: 'Company',
              // dataIndex: 'email',
              key: 'company',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.company}</h3>
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

          let userManage = data.map((p)=>{
            // console.log("record..",p)
              return(
                  <List.Item style={{marginBottom:'5px'}}>
                    <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"310px", background: "#eaeaf2"}}> 
              
              <Row>
                <Col span={24}>
                  <h4 style={{fontSize: "14px",}}> Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.code}</span></h4>
                </Col>
                </Row>
      
                <Row >
                <Col span={24}>
                  <h4 style={{fontSize: "14px",}}> Name : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>
                </Col>
                </Row>
                
                <Row>
                <Col span={24}>
                  <h4 style={{fontSize: "14px", }}> Stop Ind : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.stopInd}</span></h4>   
                </Col>
                </Row>
      
                <Row>
                <Col span={24}>
                  <h4 style={{fontSize: "14px", }}> Currency Code : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.currCode}</span></h4>   
                </Col>
                </Row>
    
                <Row>
                <Col span={24}>
                  <h4 style={{fontSize: "14px", }}> Company : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.company}</span></h4>   
                </Col>
                </Row>
      
                
                {/* <Row>
                <Col span={24}>
                <h4 style={{fontSize: "14px", }}> Relationship Type : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.rType}</span></h4>
                </Col>
                </Row> */}
                
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
      <App header={
        <Input placeholder="Search Supplier List" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        onSearch={(val)=>this.onSearch(val)}
        />
      // <Search placeholder="Search Code / Name in Supplier" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} onSearch={(val)=>this.onSearch(val)}/>
      }>
             
             {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }} /> */}
              
             {/* <Search placeholder="Search Code / Name" 
              // onSearch={(val)=>this.onSearch(val)} 
              onChange={(val)=>this.onSearch(val)} 
              allowClear style={{width:'30%'}} /> */}

             <Table columns={columns} 
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
             dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} 
             style={{margin:'2%'}}
             pagination={false} 
             className='product' />

         </App>
          )
      }else{
        return(
          <Appm>
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Supplier List</h4>

            <Row style={{marginTop:"20px",height:'100%', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {userManage.length > 0 ? 
                                        
                                        userManage 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>
          </Appm>
        )
      }
    }
  }
  </MediaQuery>
     )   
    }

}

export default Supplier