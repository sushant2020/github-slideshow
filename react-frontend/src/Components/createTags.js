import React, {Component} from "react";
import {Form, Button, Row, Col, Input, Select, Modal, Divider, Card, Table, Tag, Popconfirm ,Space,message,Switch } from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import App from "../App";
import MediaQuery from 'react-responsive';
import Appm from "../mApp"
// import { withApollo } from "react-apollo";

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    EditFilled,
    HighlightFilled,
    DeleteFilled
  
  } from '@ant-design/icons';
import mApp from "../mApp";

  const { Option } = Select;
  const { Search } = Input;

  
  const data = [
    {
      key: '1',
      name: 'Out Of Stock',
      age: 32,
      
    },
    {
      key: '2',
      name: 'In Offer',
      age: 42,
      
    },
    {
      key: '3',
      name: 'Short Dated',
      age: 32,
      },
    // {
    //   key: '4',
    //   name: 'Buyer',
    //   age: 32,
    // },
  ];
  
 
class CreateTags extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           tableData: [],
        collapsed: false,
        userModal: false,
        viewModal: false,
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    }

    onSubmit=()=>{

      this.setState({userModal: false, viewModal: false})
      message.success('Feature created Successfully');
      window.location.reload();
    }

    onSearch=(n)=>{
      console.log("In Search",n)
 
       // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
       // const found = data.find(element => element.name == n);
       let arr = [];
       data.map((i, j) => {
         if (i.name.toLowerCase().includes(n.toLowerCase()) || i.key.includes(n)) {
           arr.push(i);
         }
       });
       
       // console.log("Search Index:: ",index)
      // console.log("Search Name:: ",arr)
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
      
      const columns = [
    {
      title: 'No',
      width: 250,
      render:(data)=>{
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.key}</h3>
        )
      }
      //dataIndex: 'key',
    },
    {
      title: 'Tag',
      // dataIndex: 'name',
      key: 'name',
      width: 950,
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.name}</h3>
        )
      }
    },
    // {
    //   title: 'Value',
    //   // dataIndex: 'name',
    //   key: 'name',
    //   width: 450,
    //   render:(data)=>{
    //     // console.log("Data..",data)
    //     return(
    //       <h3 style={{fontWeight:'bold', fontSize: 16 }}>{data.name}</h3>
    //     )
    //   }
    // },
    {
      title: 'Action',
      key: 'action',
      render: (text, record) => (
        // <>
         <Space size="middle"> 
          {/* <Button type="primary" size="large" icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}} 
          onClick={()=>this.setState({viewModal: true,action: 'view'})} 
          />  */}
          <EditFilled 
          onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
          style={{fontSize:"25px"}}/>
          {/* <Button type="primary" icon={<EditOutlined style={{fontSize:"22px"}}/>} style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
          onClick={()=>this.setState({viewModal: true,action: 'edit'})}
          />  */}
          &nbsp;
          <Switch defaultChecked />
          {/* <Popconfirm title="Sure to delete?" 
          //onConfirm={() => this.handleDelete(data.prodId)}
          >
          <DeleteFilled 
                // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
                style={{fontSize:"25px",color:"#D10000"}}/>
          </Popconfirm>  */}
          {/* <DeleteOutlined /> */}
         </Space> 
        // </>
      ),
    },
  ];

  let userManage = data.map((p)=>{
    // console.log("record..",p)
      return(
          <List.Item style={{marginBottom:'5px'}}>
            <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"100%", background: "#eaeaf2"}}> 
      
      <Row>
        <Col span={24}>
          <h4 style={{fontSize: "14px",}}> Task : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>
        </Col>
        </Row>

        <Row style={{marginTop:"5%"}}>
        <Col span={18}>
           <EditFilled 
            onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
             style={{fontSize:"25px"}}/>
        </Col>
        <Col span={4}>
            <DeleteFilled 
                // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
             style={{fontSize:"25px",color:"#D10000"}}
             />
        </Col>
        </Row>
        
      {/*   <Row>
        <Col span={24}>
          <h4 style={{fontSize: "14px", }}> Assign To : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.assignTo}</span></h4>   
        </Col>
        </Row>

        <Row>
        <Col span={24}>
          <h4 style={{fontSize: "14px", }}> Created at : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.createdAt}</span></h4>   
        </Col>
        </Row>

        <Row>
        <Col span={24}>
          <h4 style={{fontSize: "14px", }}> Priority : &nbsp; &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.priority}</span></h4>   
        </Col>
        </Row> */}

        
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

        // console.log("In Render");
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App header={
        <Input placeholder="Search " size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        onSearch={(val)=>this.onSearch(val)}
        />
      // <Search placeholder="Search in Tag" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} onSearch={(val)=>this.onSearch(val)}/>
      }>
        {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
            Role 
          </Divider> */}
           <div>
             {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Features</h4> */}
          <Button type="primary" onClick={()=>{this.setState({userModal: true})}}
             style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}
             >
              Add Tags
         </Button>
         </div>

         {/* <Divider style={{fontWeight: "bold",}} /> */}

         {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

         <Table columns={columns} 
         rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
        //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Type</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Value</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
         dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} 
         style={{margin:'2%'}} 
         pagination={false}
         className='product' 
         onHeaderRow={(columns, index) => {
         return {
   onClick: () => {}, // click header row
 };
}}/>

         <Modal
             title="Create New Tags"
             centered
             visible={this.state.userModal}
             onOk={()=>{this.setState({userModal: false})}}
             onCancel={()=>{this.setState({userModal: false})}}
             footer={null}
             width={600}
             >
         <Form 
       onFinish={this.onSubmit} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="ftype"
        label="Tag"
        rules={[
          {
            required: true,
            message: 'Tag is Required',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
        {/* <Select placeholder="Please Select Type" >
        {type.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select> */}
      </Form.Item>
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17}>
       {/* <Form.Item
        name="fvalue"
        label="Feature Value"
        rules={[
          {
            required: true,
            message: 'Please Enter Feature Value',
          },
        ]}
      >
       <Input defaultValue="Rating" style={{width:'100%'}}/>
      </Form.Item> */}
        </Col>
        </Row>
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                //  onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
     </Modal>  

     <Modal
                title= {this.state.action == 'view' ? "View Tag" : "Edit Tag"}
                centered
                visible={this.state.viewModal}
                onOk={()=>{this.setState({viewModal: false})}}
                onCancel={()=>{this.setState({viewModal: false})}}
                footer={null}
                width={600}
                >
        {this.state.action == 'view' && 
        <div>
                  <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Title : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        Rating
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
        <Col span={8} style={{fontSize:20, fontWeight:'bold',}}> Value : </Col>
        <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        Rating
        </Col>
        </Row>
        </div>
       }

        {this.state.action == 'edit' &&           
           <Form 
              onFinish={this.onSubmit} 
              layout="vertical">
              <Row gutter={24}>
              <Col span={3}></Col>
              <Col span={17}>
              <Form.Item
               name="name"
               label="Tag"
               rules={[
                 {
                   required: true,
                   message: 'Tag is Required',
                 },
               ]}
             >
               <Input defaultValue="-" style={{width:'100%'}}/>
               {/* <Select defaultValue="Rating" placeholder="Please Select Type" >
               {type.map((c) => {
                 return (
                 <Option key={c.value} value={c.value}>
                   {c.label}
                 </Option>
                 );
               })}
              </Select> */}
             </Form.Item>
               </Col>
               </Row>
               <Row>
               <Col span={3}></Col>
               <Col span={17}>
               {/* <Form.Item
               name="cvalue"
               label="Value"
               rules={[
                 {
                   required: true,
                   message: 'Please Select Value',
                 },
               ]}
             >
              <Select defaultValue="GENERIC" placeholder="Please Select Value" >
               {type.map((c) => {
                 return (
                 <Option key={c.value} value={c.value}>
                   {c.label}
                 </Option>
                 );
               })}
              </Select>
             </Form.Item> */}
               </Col>
               </Row>
               <div style={{textAlign:"center"}}>
                      <Button type="primary" htmlType="submit"
                        // onClick={()=>{this.addFeature()}}
                       style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                         Submit
                   </Button>
                   </div>
               </Form>
        }
        </Modal>
      </App>
      )
    }else{
      return(
        <Appm>
          <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Create Tags</h4>
        
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

export default CreateTags