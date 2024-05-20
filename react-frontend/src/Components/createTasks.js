import React from 'react'
import { Form,Button, Row, Col, Input, Select, Card, Layout, Divider,Radio, AutoComplete, Table, Modal } from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import App from "../App";
import MediaQuery from 'react-responsive';
import Appm from "../mApp"

const { Option } = Select;
const { Header, Content, Footer } = Layout;
const { TextArea } = Input;

const supplier = [
  { value: "1ST CALL SERVICES", label: "1ST CALL SERVICES" },
  { value: "365HEALTHCARE", label: "365HEALTHCARE" },
  { value: "A&D INSTRUMENTS LTD", label: "A&D INSTRUMENTS LTD" },
  { value: "5 STAR TECHNOLOGIES LTD", label: "5 STAR TECHNOLOGIES LTD" },
  { value: "AAH PHARMACEUTICALS LTD", label: "AAH PHARMACEUTICALS LTD" },
];

const data1 = [
    {
      value: 'ACAT31',
      type: 'Tablet',
      desc: 'Acamprosate 333mg gastro-resistant tablets',
      price: '38.25',
      date: '09-06-2021',
    },
    {
      value: 'ACAT19',
      type: 'Tablet',
      desc: 'Acarbose 100mg tablets',
      price: '25.29',
      date: '09-06-2021',
    },
    {
      value: 'ACAT59',
      type: 'Tablet',
      desc: 'Acarbose 50mg tablets',
      price: '14.58',
      date: '09-06-2021',
      },
    {
      value: 'ACE16',
      type: 'capsule',
      desc: 'Acetylcysteine 600mg capsules',
      price: '47.59',
      date: '09-06-2021',
    },
  ];

  const viewTasks = [
    {
      task: "Book the ACE16 medicine going to be shortage.",
      createdBy: 'Admin',
      assignTo: "Supplier",
      createdAt: "08-07-2021",
      priority:"High"
    },
    // {
    //   task: '1',
    //   createdBy: 'GENERI',
    //   assignTo: "SM_Analysis_Code_1",
    //   createdAt: 32,
    //   priority:"High"
    // },
    // {
    //   task: '1',
    //   createdBy: 'GENERI',
    //   assignTo: "SM_Analysis_Code_1",
    //   createdAt: 32,
    //   priority:"High"
    // },
    
  ];

class CreateTasks extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
          mainPage: false,
          tableData:[],
        };
      }

    onFinish = (values) => {
        // console.log('Received values of form: ', values);
    };

    onSearch=(n)=>{
        // console.log("In Search...",n)
        
         let arr = [];
         data1.map((i, j) => {
           if (i.value.toLowerCase().includes(n.toLowerCase()) 
          //  || i.key.includes(n) 
          //  || i.phone.toLowerCase().includes(n.toLowerCase())
            || i.desc.toLowerCase().includes(n.toLowerCase())
            // || i.address.toLowerCase().includes(n.toLowerCase())
            ) {
             arr.push(i);
           }
         });
         this.setState({
           tableData: arr
         })
         if(n == ''){
          // console.log("Search No Result:: ")
           
           this.setState({
             tableData: []
           })
         }
       }

       onSelected=(option)=>{
        //  console.log("In Selected..",option)
  
        //  this.props.history.push('/products')
       }

  // const { getFieldDecorator } = this.props.form;
  render(){

    const viewTask = [
    
        {
          title: 'Task',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.task}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Created By',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.createdBy}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Assign To',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.assignTo}</h3>
            )
          }
        },
        {
          title: 'Created At',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.createdAt}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Priority',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.priority}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        
      ];

      let userManage = viewTasks.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"100%", background: "#eaeaf2"}}> 
          
          <Row>
            <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Task : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.task}</span></h4>
            </Col>
            </Row>
  
            <Row >
            <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Created By : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.createdBy}</span></h4>
            </Col>
            </Row>
            
            <Row>
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

  return (
    <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App>
    <div>
    <div>
             {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Features</h4> */}
          <Button type="primary" onClick={()=>{this.setState({userModal: true})}}
             style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}
             >
             Create Task
         </Button>
         </div>

         {/* <Divider style={{fontWeight: "bold",}} /> */}

         {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

         <Table columns={viewTask} 
         rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
        //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Type</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Value</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
         dataSource={this.state.tableData.length > 0 ? this.state.tableData : viewTasks} 
         style={{margin:'2%'}} 
         pagination={false}
         className='product' 
         onHeaderRow={(columns, index) => {
         return {
          onClick: () => {}, // click header row
          };
          }}/>

         <Modal
             title="Create New Tasks"
             centered
             visible={this.state.userModal}
             onOk={()=>{this.setState({userModal: false})}}
             onCancel={()=>{this.setState({userModal: false})}}
             footer={null}
             width={600}
             >
       
       <Form 
      //  onFinish={this.onSubmit} 
       layout="vertical">
       
        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="task"
        label="Task"
        rules={[
          {
            required: true,
            message: 'Comment is Required',
          },
        ]}
      >
        <TextArea rows={2} />
       
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="assignTo"
        label="Assign To"
        rules={[
          {
            required: true,
            message: 'Comment is Required',
          },
        ]}
      >
        <Select placeholder="">
          <Option value="china">Buyer</Option>
          <Option value="usa">Supplier</Option>
          {/* <Option value="usa">U.S.A</Option>
          <Option value="usa">U.S.A</Option> */}
        </Select>
       
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="priority"
        label="Priority"
        rules={[
          {
            required: true,
            message: '',
          },
        ]}
      >
        <Radio.Group>
          <Radio value="low">Low</Radio>
          <Radio value="medium">Medium</Radio>
          <Radio value="high">High</Radio>
        </Radio.Group>
       
      </Form.Item>
        </Col>
        </Row>
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="supplier"
        label="Supplier"
        rules={[
          {
            // required: true,
            message: 'Select supplier',
          },
        ]}
      >
        <Select placeholder="Please select supplier">
          <Option value="china">ALPHA</Option>
          <Option value="usa">DEXCEL</Option>
          <Option value="usa">NEOLAB</Option>
          <Option value="usa">RECKIT</Option>
        </Select>
       
      </Form.Item>
        </Col>
        </Row>


        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="product"
        label="Product"
        rules={[
          {
            // required: true,
            message: 'Product is Required',
          },
        ]}
      >
        <AutoComplete style={{borderRadius: 5}}
            options={this.state.tableData.length > 0 ? this.state.tableData : ''}
            placeholder="Search Prod Code"
            // filterOption={(inputValue, option) => option.value.toUpperCase().indexOf(inputValue.toUpperCase()) !== -1
          // }
            onChange={(e)=>this.onSearch(e)}
            onSelect={(option)=>this.onSelected(option)}
            // onSearch={(val)=>this.onSearch(val)}     
          />
       
      </Form.Item>
        </Col>
        </Row>

        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                //  onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d',borderRadius:5, borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
        </Modal>
      
    </div>
    </App>
          )
      }else{
        return(
          <Appm>
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Create Tasks</h4>

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
  );
    }
}

export default CreateTasks;
