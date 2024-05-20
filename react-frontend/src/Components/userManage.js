import React, {Component} from "react";
import {Table, Divider, Button, Modal ,Space , Card, Tag, Popconfirm, Form, Row, Col, Input, Select,Switch,message} from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import App from "../App";
import axios from 'axios';
import moment from 'moment'
import CreateUser from "./createUser"
import MediaQuery from 'react-responsive';
import Appm from "../mApp"

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
    name: 'John Brown',
    age: 32,
    email: 'john.brown@gmail.com',
    status: 'Active',
    tags: ['developer'],
  },
  {
    key: '2',
    name: 'Jim Green',
    age: 42,
    email: 'jim.green@gmail.com',
    status: 'Active',
    tags: ['Administrator'],
  },
  {
    key: '3',
    name: 'Joe Black',
    age: 32,
    email: 'joe.black@gmail.com',
    status: 'Active',
    tags: ['Manager'],
  },
];

const role = [
  { value: "Administrator", label: "Administrator" },
  { value: "Buyer", label: "Buyer" },
  { value: "Manager", label: "Manager" },
  { value: "Staff", label: "Staff" },
];

class UserManagement extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           userModal: false,
           tableData: [],
           viewModal: false,
           userData: [],
           editUserDetails: {}
        };
      }
      formRef = React.createRef();

    componentDidMount(){
        // console.log("In CDM");

        this.getUsers();
        
    }

    getUsers=()=>{

      let getURL = "https://api.sigmaproductmaster.webdezign.uk/api/users"

        axios.get(getURL).then((response) => {
          // console.log("Response...::",response.data)
          if(response){
          this.setState({
            userData: response.data.users,
            userloading: false
          })
        }else{
          this.setState({
            // prodData: response.data,
            prodloading: false
          })
        }
        })

    }

    handleOk=()=>{

      this.setState({userModal: false,})
    }

    onSearch=(e)=>{
      // console.log("In Search",e.target.value);
      let n = e.target.value;
            // return false
            //  let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
            //  const found = data.find(element => element.name == n);
       
       let arr = [];
       data.map((i, j) => {
         if (i.name.toLowerCase().includes(n.toLowerCase()) || i.key.includes(n) || i.email.toLowerCase().includes(n.toLowerCase()) || i.status.toLowerCase().includes(n.toLowerCase())) {
           arr.push(i);
         }
       });

       data.map((p)=>{
        p.tags.map((q)=>{{
         // console.log("...Tags",q)
          if (q.toLowerCase().includes(n.toLowerCase())) {
           arr.push(p);
         }
        }}) 
      })
       
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

     onUpdate = (values) => {

      console.log('Received values of form: ', values);
      // this.props.closeModal();
    // return
      // let roles = '' 
    // roles = values.role.map((p)=>{ return p})
    // console.log('Received values of form role:  ', roles);
    //  return
      const user = {
        firstname : values.fName,
        lastname : values.lName,
        email : values.email,
        // role : values.role
      }
      // console.log('Received values of form: ',user);
      // return
      let updateUser="https://api.sigmaproductmaster.webdezign.uk/api/updateuser"

    axios({
      method: "put",
      url: updateUser,
      data: user,
      // headers: { "Content-Type": "multipart/form-data" },
    })
    .then((response) => {
      console.log("Response...::",response)

      if(response.data.status == "success"){
        console.log("Response...::",response.data)
        // localStorage.setItem("userDetails",response.data);
        this.setState({
          userDataRes: response.data,
          viewModal: false,
          editUserDetails: {}
        })
        // message.success('User Created Successfully !');
        // return
      
      }else{
        this.setState({
        userDataRes: response.data.error,
        
      })
      // message.error(' User Creation Unsuccesfull !! ');
      // setTimeout(function(){ this.setState({
      //   loginStatus: false,  
      // }) }, 3000);
      }
    })

      // message.success('user Updated Successfully');
      // this.props.getUser();
      // this.formRef.current.resetFields();
      // this.props.closeModal();
  };

  onDelete = (values) => {

    console.log('Received values of form: ', values);
    // this.props.closeModal();
  // return
    // let roles = '' 
  // roles = values.role.map((p)=>{ return p})
  // console.log('Received values of form role:  ', roles);
  //  return
    const user = {
      firstname : values.fName,
      lastname : values.lName,
      email : values.email,
      // role : values.role
    }
    // console.log('Received values of form: ',user);
    // return
    let updateUser="https://api.sigmaproductmaster.webdezign.uk/api/deleteuser"

    axios.delete(`https://api.sigmaproductmaster.webdezign.uk/api/deleteuser/${values.id}`)
    .then(res => {
      console.log(res);
      console.log(res.data);
    },()=>this.getUsers())

  // axios({
  //   method: "delete",
  //   url: updateUser,
  //   data: user,
  //   // headers: { "Content-Type": "multipart/form-data" },
  // })
  // .then((response) => {
  //   console.log("Response...::",response)

  //   if(response.data.status == "success"){
  //     console.log("Response...::",response.data)
  //     // localStorage.setItem("userDetails",response.data);
  //     this.setState({
  //       userDataRes: response.data,
  //       viewModal: false,
  //       editUserDetails: {}
  //     })
  //     // message.success('User Created Successfully !');
  //     // return
    
  //   }else{
  //     this.setState({
  //     userDataRes: response.data.error,
      
  //   })
  //   // message.error(' User Creation Unsuccesfull !! ');
  //   // setTimeout(function(){ this.setState({
  //   //   loginStatus: false,  
  //   // }) }, 3000);
  //   }
  // })

    // message.success('user Updated Successfully');
    // this.props.getUser();
    // this.formRef.current.resetFields();
    // this.props.closeModal();
};



    render(){
      
      const columns = [
  {
    title: 'No',
    dataIndex: 'id',
  },
  {
    title: 'Name',
    // dataIndex: 'name',
    key: 'name',
    render:(data)=>{
      // console.log("Data..",data)
      return(
        <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.firstname}&nbsp;{data.lastname} </h3>
      )
    }
  },
  {
    title: 'Email',
    // dataIndex: 'email',
    key: 'email',
    render:(data)=>{
      // console.log("Data..",data)
      return(
        <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.email}</h3>
      )
    }
  },
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
  {
    title: 'Roles',
    key: 'tags',
    dataIndex: 'tags',
    render: () => (
            <Tag color='geekblue'  style={{fontWeight:'bold',fontSize:"15px"}}>
              Administrator
            </Tag>
          
    ),
  },
  {
    title: 'Status',
    // dataIndex: 'status',
    key: 'status',
    render:(data)=>{
      // console.log("Data..",data)
      return(
              // <Switch defaultChecked />   
        <Tag style={{fontWeight:'bold',color:'green',fontSize:"18px"}}>Active</Tag>
      )
    }
  },
  {
    title: 'Action',
    key: 'action',
    render: (text, record) => (
      <>
       <Space size="middle">
        {/* <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'view'})}} icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}}/>  */}
        <EditFilled 
          onClick={()=>{this.setState({viewModal: true, action: 'edit',editUserDetails: record})}} 
          style={{fontSize:"25px"}}/>
        {/* <Button type="primary" onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} icon={<EditOutlined style={{fontSize:"22px"}}/>} style={{marginLeft:"10px",background:"#353b8d",borderColor:"#353b8d"}}
        //onClick={() => this.handleMatch(data.prodId)}
        />  */}
        <Switch defaultChecked onChange={()=>this.onDelete(record)}/>
        {/* <Popconfirm title="Sure to delete?" 
        
        //onConfirm={() => this.handleDelete(data.prodId)}
        >
          &nbsp; 
          <DeleteFilled 
                // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
                style={{fontSize:"25px",color:"#D10000"}}/> */}
                {/* <Button icon={<DeleteOutlined style={{fontSize:"22px"}}/>}  type= "danger"  style={{marginLeft:"10px",cursor: "pointer", background:"#8f0021",borderColor:"#8f0021" }}/> */}
        {/* </Popconfirm>  */}
        {/* <DeleteOutlined /> */}
       </Space>
      </>
    ),
  },
];

    let userManage = data.map((p)=>{
      // console.log("record..",p)
        return(
            <List.Item style={{marginBottom:'5px'}}>
              <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"310px", background: "#eaeaf2"}}> 
        
        <Row>
          <Col span={24}>
            <h4 style={{fontSize: "14px",}}> Name : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>
          </Col>
          </Row>

          <Row >
          <Col span={24}>
            <h4 style={{fontSize: "14px",}}> Email : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.email}</span></h4>
          </Col>
          </Row>
          
          <Row>
          <Col span={24}>
            <h4 style={{fontSize: "14px", }}> Status : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
            {p.tags.map(tag => {
              return (
                <Tag color="geekblue" key={tag} style={{fontWeight:'bold',fontSize:"15px",marginTop: 5}}>
                  {tag.toUpperCase()}
                </Tag>
               );
             })}
            </span></h4>   
          </Col>
          </Row>

          <Row>
          <Col span={24}>
            <h4 style={{fontSize: "14px", }}> Role : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
            
                <Tag color="green" key={p.status} style={{fontWeight:'bold',fontSize:"15px",marginTop: 5}}>
                  {p.status}
                </Tag>
              
            </span></h4>   
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

        // console.log("In Render",this.state.editUserDetails);
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App header={
        <Input placeholder="Search in User" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        onSearch={(val)=>this.onSearch(val)}
        />
      // <Input placeholder="Search in Users" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} 
      // // onSearch={(val)=>this.onSearch(val)}
      // onChange={(e)=>this.onSearch(e)}
      // />
    }

      >
           {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}> */}
              
             {/* </Divider> */}

              <div>

                {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Users</h4> */}
             <Button type="primary" onClick={()=>{this.setState({userModal: true})}}
                style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}>
                New User
            </Button>
            </div>

            {/* <Divider style={{fontWeight: "bold",}} />
              
            <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

             <Table columns={columns} 
             rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Email</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.email}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Role</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.tags}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={this.state.userData.length > 0 ? this.state.userData : ""} 
             style={{margin:'2%'}} 
             className='product' />
          

        <Modal
          title="New User"
          centered
          visible={this.state.userModal}
          onOk={()=>{this.setState({userModal: false})}}
          onCancel={()=>{this.setState({userModal: false})}}
          footer={null}
          width={600}
         >
          <CreateUser
              // imageArr={this.state.imgArr}
              // exh={this.props.exh}
              getUser={this.getUsers}
              closeModal={this.handleOk}
            />
        </Modal>

        <Modal
                title= {this.state.action == 'view' ? "View User Details" : "Edit user Details"}
                centered
                visible={this.state.viewModal}
                onOk={()=>{this.setState({viewModal: false,editUserDetails: {}})}}
                onCancel={()=>{this.setState({viewModal: false,editUserDetails: {}})}}
                footer={null}
                width={600}
                >
        {this.state.action == 'view' && 
        <div>
                  <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Name : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        John Brown
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Email : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        john.brown@gmail.com
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Role : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        Administrator
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
        <Col span={8} style={{fontSize:20, fontWeight:'bold',}}> Status : </Col>
        <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        ACtive
        </Col>
        </Row>
        </div>
       }

        {this.state.action == 'edit' &&           
            <Form 
       onFinish={this.onUpdate} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="fname"
        label="First Name"
        rules={[
          {
            required: true,
            message: 'Please Enter First Name',
          },
        ]}
      >
        <Input defaultValue={this.state.editUserDetails.firstname} style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="lname"
        label="Last Name"
        rules={[
          {
            required: true,
            message: 'Please Enter Lame',
          },
        ]}
      >
        <Input defaultValue={this.state.editUserDetails.lastname} style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Email"
        initialValue={this.state.editUserDetails.email}
        rules={[
          {
            required: true,
            message: 'Please Enter Email',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row>
        <Col span={3}></Col>
        <Col span={17}>
        <Form.Item
        name="role"
        label="Permissions"
        rules={[
          {
            required: true,
            message: 'Please Select Role',
          },
        ]}
      >
       <Select 
      //  mode="multiple" 
       defaultValue="Administrator" placeholder="Please Select Role" >
        {role.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select>
      </Form.Item>
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
        }
        </Modal>
         </App>
          )
      }else{
        return(
          <Appm>
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Manage User</h4>

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

export default UserManagement