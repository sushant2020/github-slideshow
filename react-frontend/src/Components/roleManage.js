import React, {Component} from "react";
import {Form, Button, Row, Col, Input, Card, Select, Modal, Divider, Table, Tag, Popconfirm ,Space,Switch } from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import App from "../App";
import axios from 'axios';
import MediaQuery from 'react-responsive';
import Appm from "../mApp"
// import { withApollo } from "react-apollo";
import CreateUser from "./createUser"
import "../CSS/table.css"

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    SearchOutlined,
    EditFilled,
    HighlightFilled,
    DeleteFilled
  } from '@ant-design/icons';
import mApp from "../mApp";

  const { Option } = Select;
  const { Search } = Input;

const role = [
    { value: "users_manage", label: "users_manage" },
    { value: "manageManualImport", label: "manageManualImport" },
    { value: "manageProduct", label: "manageProduct" },
    { value: "manageAccess", label: "manageAccess" },
    { value: "manageSupplier", label: "manageSupplier" },
    { value: "managePriceData", label: "managePriceData" },
    { value: "manageVolumeData", label: "manageVolumeData" },
    { value: "manageFileImport", label: "manageFileImport" },
    { value: "ALL", label: "ALL" }
  ];

  
  
  const data = [
    {
      key: '1',
      name: 'Administrator',
      age: 32,
      email: 'New York No. 1 Lake Park',
      status: 'Active',
      tags: ['All'],
    },
    {
      key: '2',
      name: 'Staff',
      age: 42,
      email: 'London No. 1 Lake Park',
      status: 'Active',
      tags: ['Manage manual Import  '],
    },
    {
      key: '3',
      name: 'Manager',
      age: 32,
      email: 'Sydney No. 1 Lake Park',
      status: 'Active',
      tags: ['Manage manual Import  ','Manage Product  ', 'Manage Supplier  ','Manage Price Data  ','Manage Volume Data  ','Manage File Import  '],
    },
    {
      key: '4',
      name: 'Buyer',
      age: 32,
      email: 'Sydney No. 1 Lake Park',
      status: 'Active',
      tags: ['Manage manual Import  ','Manage File Import  '],
    },
  ];
  
 

class RoleManagement extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           userModal: false,
           viewModal: false,
           tableData: [],
           action:'',
           searchText: '',
           searchedColumn: '',
        };
      }

    componentDidMount(){
        // console.log("In CDM");
        this.getUsers();
    }

    getUsers=()=>{

      let getURL = "https://api.sigmaproductmaster.webdezign.uk/api/roles"

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

    onSubmit=()=>{
      this.setState({viewModal: false})
    }

    // getColumnSearchProps = dataIndex => ({
    //   filterDropdown: ({ setSelectedKeys, selectedKeys, confirm, clearFilters }) => (
    //     <div style={{ padding: 8 }}>
    //       <Input
    //         ref={node => {
    //           this.searchInput = node;
    //         }}
    //         placeholder={`Search ${dataIndex}`}
    //         value={selectedKeys[0]}
    //         onChange={e => setSelectedKeys(e.target.value ? [e.target.value] : [])}
    //         onPressEnter={() => this.handleSearch(selectedKeys, confirm, dataIndex)}
    //         style={{ width: 188, marginBottom: 8, display: 'block' }}
    //       />
    //       <Space>
    //         <Button
    //           type="primary"
    //           onClick={() => this.handleSearch(selectedKeys, confirm, dataIndex)}
    //           icon={<SearchOutlined />}
    //           size="small"
    //           style={{ width: 90 }}
    //         >
    //           Search
    //         </Button>
    //         <Button onClick={() => this.handleReset(clearFilters)} size="small" style={{ width: 90 }}>
    //           Reset
    //         </Button>
            
    //       </Space>
    //     </div>
    //   ),
    //   filterIcon: filtered => <SearchOutlined style={{ color: filtered ? '#1890ff' : undefined }} />,
    //   onFilter: (value, record) =>
    //     record[dataIndex]
    //       ? record[dataIndex].toString().toLowerCase().includes(value.toLowerCase())
    //       : '',
    //   onFilterDropdownVisibleChange: visible => {
    //     if (visible) {
    //       setTimeout(() => this.searchInput.select(), 100);
    //     }
    //   },
    // });

    // handleSearch = (selectedKeys, confirm, dataIndex) => {
    //   confirm();
    //   this.setState({
    //     searchText: selectedKeys[0],
    //     searchedColumn: dataIndex,
    //   });
    // };
  
    handleReset = clearFilters => {
      clearFilters();
      this.setState({ searchText: '' });
    };

    onSearch=(n)=>{
      // console.log("In Search",n)
        // return
       // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
       // const found = data.find(element => element.name == n);
       let arr = [];
       data.map((i, j) => {
         if (i.name.toLowerCase().includes(n.toLowerCase()) || i.key.includes(n)) {
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
          render:(data)=>{
            return(
              <h3 style={{fontWeight:'bold'}}>{data.key}</h3>
            )
          }
          //dataIndex: 'key',
        },
        {
          title: 'Title',
          // ...this.getColumnSearchProps('name'),
          // dataIndex: 'name',
          key: 'name',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.name}</h3>
            )
          }
        },
        {
          title: 'Permissions',
          key: 'tags',
          dataIndex: 'tags',
          render: tags => (
            <>
              {tags.map(tag => {
                // console.log("Tag",tag)
                let color = tag.length > 5 ? 'geekblue' : 'green';
                if (tag === 'loser') {
                  color = 'volcano';
                }
                return (
                  <Tag color="geekblue" key={tag} style={{fontWeight:'bold',fontSize:"15px",marginTop: 5}}>
                    {tag.toUpperCase()}
                  </Tag>
                );
              })}
            </>
          ),
        },
        {
          title: 'Action',
          key: 'action',
          render: (text, record) => (
            // <>
             <Space size="middle"> 
              {/* <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'view'})}}  icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}}/>  */}
              <EditFilled 
              onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
              style={{fontSize:"25px"}}/>
              {/* <Button type="primary" onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} icon={<EditOutlined style={{fontSize:"22px"}}/>} style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
              // onClick={()=>{this.setState({userModal: true})}}
              />  */}
              &nbsp;

              <Switch defaultChecked />
              {/* <Popconfirm title="Sure to delete?" style={{width:'150%'}}
              //onConfirm={() => this.handleDelete(data.prodId)}
              >
                <DeleteFilled 
                // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
                style={{fontSize:"25px",color:"#D10000"}}/>
                      {/* <Button icon={<DeleteOutlined style={{fontSize:"22px"}}/>}  type= "danger"  style={{marginLeft:"10px",cursor: "pointer", background:"#8f0021",borderColor:'#8f0021',borderRadius:5}}/> */}
              {/* </Popconfirm>  */} 
              {/* <DeleteOutlined /> */}
             </Space> 
            // </>
          ),
        },
      ];

      let roleManage = data.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"310px", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Title : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Permissions : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
              {p.tags.map(tag => {
                return (
                  <Row>
                    <Col span={12}>
                  <Tag color="geekblue" key={tag} style={{fontWeight:'bold',fontSize:"15px",marginTop: 5,wordWrap:"break-word"}}>
                    {tag.toUpperCase()}
                  </Tag>
                  </Col>
                  </Row>
                );
              })}
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
        // console.log("In Render");
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App header={
        <Input placeholder="Search in Roles" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        onSearch={(val)=>this.onSearch(val)}
        />
      // <Search placeholder="Search in Roles" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} 
      // // onChange={(val)=>this.onSearch(val)}
      // onSearch={(val)=>this.onSearch(val)}
      // />
    }
      >
           {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               Role 
             </Divider> */}
              <div>
                {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Roles</h4> */}
             <Button type="primary" onClick={()=>{this.setState({userModal: true})}}
                style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius: 5}}>
                  New Role
            </Button>
            </div>

            {/* <Divider style={{fontWeight: "bold",}} /> */}

            {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

            <Table 
            columns={columns}
            rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"} 
            // expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Title</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Permissions</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.tags}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
            dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} 
            style={{margin:'2%',}} 
            className='product' 
            pagination={false}
            onHeaderRow={(columns, index) => {
    return {
      onClick: () => {}, // click header row
    };
  }}/>

            <Modal
                title="Create New Role"
                centered
                visible={this.state.userModal}
                onOk={()=>{this.setState({userModal: false})}}
                onCancel={()=>{this.setState({userModal: false})}}
                footer={null}
                width={600}
                >
            <Form 
    //    onFinish={this.onFinish} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="name"
        label="Title"
        rules={[
          {
            required: true,
            message: 'Please Enter Title',
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
       <Select mode="multiple" placeholder="Please Select Role" >
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
        </Modal>

        <Modal
                title= {this.state.action == 'view' ? "View Role" : "Edit Role"}
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
        Administrator
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
        <Col span={8} style={{fontSize:20, fontWeight:'bold',}}> Permissions : </Col>
        <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        ALL
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
        label="Title"
        rules={[
          {
            required: true,
            message: 'Please Enter Title',
          },
        ]}
      >
        <Input defaultValue="Administrator" style={{width:'100%'}}/>
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
       <Select mode="multiple" defaultValue="ALL" placeholder="Please Select Role" >
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
              <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Manage Role</h4>
               <div>
                 <Row style={{marginTop:"20px",height:'100%', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {roleManage.length > 0 ? 
                                        
                                        roleManage 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>
                </div>
            </Appm>
          )
        }
      }
    }
    </MediaQuery>
     )   
    }

}

export default RoleManagement