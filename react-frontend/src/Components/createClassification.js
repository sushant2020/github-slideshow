import React, {Component} from "react";
import {Form, 
  Button, 
  Row, 
  Col, 
  Input, 
  Select, 
  Modal, 
  Divider, 
  Table, 
  Tag, 
  Popconfirm, 
  Space, 
  message, 
  Radio, 
} from "antd";
import App from "../App";
import MediaQuery from 'react-responsive';
import Appm from "../mApp"
// import { withApollo } from "react-apollo";

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    MinusCircleOutlined, 
    PlusOutlined,
    EditFilled,
    HighlightFilled,
    DeleteFilled
  } from '@ant-design/icons';
import mApp from "../mApp";

  const { Option } = Select;
  const { Search } = Input;

  const type = [
    { value: "SM_Analysis_Code_1", label: "GENERI" },
    { value: "SM_Analysis_Code_1", label: "ETHIC" },
    { value: "SM_Analysis_Code_1", label: "H&B" },
  ];

  const role = [
    { value: "users_manage", label: "users_manage" },
    { value: "manageManualImport", label: "manageManualImport" },
    { value: "manageProduct", label: "manageProduct" },
    { value: "manageAccess", label: "manageAccess" },
    { value: "manageSupplier", label: "manageSupplier" },
    { value: "managePriceData", label: "managePriceData" },
    { value: "manageVolumeData", label: "manageVolumeData" },
    { value: "manageFileImport", label: "manageFileImport" },
  ];
  
  const data = [
    {
      key: '1',
      name: 'GENERI',
      value: "SM_Analysis_Code_1",
      age: 32,
    },
    // {
    //   key: '2',
    //   name: 'ETHIC',
    //   age: 42,
      
    // },
    {
      key: '3',
      name: 'H&B',
      value: "SM_Analysis_Code_1",
      age: 32,
      },
    {
      key: '4',
      name: 'SURGIC',
      value: "SM_Analysis_Code_1",
      age: 32,
    },
    // {
    //     key: '5',
    //     name: 'Quantity',
    //     age: 32,
    //   },
    //   {
    //     key: '6',
    //     name: 'Dose',
    //     age: 32,
    //   },
  ];
  
 

class CreateClassification extends Component {
    constructor(props) {
        super(props);
        this.state = {
        form: false,
        collapsed: false,
        tableData: [],
        action: '',
        userModal: false,
        viewModal: false,
        value: null,
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    }

    onSubmit=()=>{

      this.setState({userModal: false, viewModal: false})
      message.success('Classification created Successfully');
    }
    
    
    onFinish = values => {
        console.log('Received values of form:', values);
        this.setState({userModal: false, viewModal: false})
      };

    onSearch=(n)=>{
      // console.log("In Search",n)
 
       // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
       // const found = data.find(element => element.name == n);
       let arr = [];
       data.map((i, j) => {
         if (i.name.toLowerCase().includes(n.toLowerCase()) || i.key.includes(n) ) {
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

     onChange = (e) => {
      //console.log("radio checked", e.target.value);
      this.setState({
        value: e.target.value,
      });
    };

    render(){ 

      const formItemLayout = {
        labelCol: {
          xs: { span: 24 },
          sm: { span: 4 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 20 },
        },
      };
      const formItemLayoutWithOutLabel = {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 4 },
        },
      };
      const { value } = this.state;
      const columns = [
    {
      title: 'No',
      width: 200,
      render:(data)=>{
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.key}</h3>
        )
      }
      //dataIndex: 'key',
    },
    {
      title: 'Type',
      // dataIndex: 'name',
      key: 'type',
      width: 450,
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.value}</h3>
        )
      }
    },
    {
      title: 'Value',
      // dataIndex: 'name',
      key: 'value',
      width: 500,
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.name}</h3>
        )
      }
    },
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
          onClick={()=>this.setState({viewModal: true,action: 'edit'})}
          style={{fontSize:"25px"}}/>
          {/* <Button type="primary" icon={<EditOutlined style={{fontSize:"22px"}}/>} style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
          onClick={()=>this.setState({viewModal: true,action: 'edit'})}
          />  */}
          &nbsp;<Popconfirm title="Sure to delete?" 
          //onConfirm={() => this.handleDelete(data.prodId)}
          >
          <DeleteFilled 
                // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
                style={{fontSize:"25px",color:"#D10000"}}/>
                  {/* <Button icon={<DeleteOutlined style={{fontSize:"22px"}}/>} type= "danger"  style={{marginLeft:"10px",cursor: "pointer", background:"#8f0021",borderColor:'#8f0021' }}/> */}
          </Popconfirm> 
          {/* <DeleteOutlined /> */}
         </Space> 
        // </>
      ),
    },
  ];
        // console.log("In Render");
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App header={
        <Input placeholder="Search in Classification" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        onSearch={(val)=>this.onSearch(val)}
        />
      
      }>
           {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               Role 
             </Divider> */}
              <div>
                {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Classification</h4> */}
             <Button type="primary" onClick={()=>{this.setState({userModal: true})}}
                style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}>
                 Add Classification
            </Button>
            </div>

            {/* <Divider style={{fontWeight: "bold",}} /> */}

            {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

            <Table columns={columns} 
            rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Type</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Value</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
            dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} style={{margin:'2%'}} className='product' onHeaderRow={(columns, index) => {
    return {
      onClick: () => {}, // click header row
    };
  }}/>

            <Modal
                title="Create New Classification"
                centered
                visible={this.state.userModal}
                onOk={()=>{this.setState({userModal: false})}}
                onCancel={()=>{this.setState({userModal: false})}}
                footer={null}
                width={600}
                >
                  <Radio.Group onChange={this.onChange} value={value} style={{marginBottom : '5%'}}>
                            <Row>
                            <Col span={4}></Col>
                              <Col span={10} offset={5}>
                                <Radio
                                  style={{
                                    display: "block",
                                    height: "30px",
                                    lineHeight: "30px",
                                  }}
                                  value={1}
                                >
                                  {" "}
                                  Add Type
                                </Radio>
                              </Col>
                              <Col span={2} offset={2}>
                                <Radio
                                  style={{
                                    display: "block",
                                    height: "30px",
                                    lineHeight: "30px",
                                  }}
                                  value={2}
                                >
                                  Add Type & Value
                                </Radio>
                              </Col>
                            </Row>
                          </Radio.Group>
            {value == 1 ?
            <Form name="dynamic_form_item" {...formItemLayoutWithOutLabel} onFinish={this.onFinish}>
            {/* <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}> */}
       <Form.Item
        name="name"
        label="Type"
        rules={[
          {
            required: true,
            message: 'Type is Required',
          },
        ]}
      >
       <Input style={{width:'80%', marginLeft: '-15%'}}/>
      </Form.Item>
        {/* </Col>
        </Row> */}
      <Form.List
        name="names"
        rules={[
          {
            validator: async (_, names) => {
              if (!names || names.length < 2) {
                return Promise.reject(new Error('At least 2 passengers'));
              }
            },
          },
        ]}
      >
        {(fields, { add, remove }, { errors }) => (
          <>
            {fields.map((field, index) => (
              index < 4 &&
              <Form.Item
                // {...(index === 0 ? formItemLayout : formItemLayoutWithOutLabel)}
                label={index === 0 ? 'Type' : 'Type'}
                required={true}
                key={field.key}
              >
                <Form.Item
                  {...field}
                  validateTrigger={['onChange', 'onBlur']}
                  rules={[
                    {
                      required: true,
                      whitespace: true,
                      message: "Please input Type or delete this field.",
                    },
                  ]}
                  noStyle
                >
                  <Input placeholder="Enter Type" style={{ width: '80%',marginLeft: '-15%' }} />
                </Form.Item>
                {fields.length > 0 ? (
                  
                  <MinusCircleOutlined style={{marginLeft: '2%', fontSize: 25}}
                    className="dynamic-delete-button"
                    onClick={() => remove(field.name)}
                  />
                ) : null}
              </Form.Item>
            ))}
            <Form.Item>
            {fields.length < 4 ? (
              <Button
                type="dashed"
                onClick={() => add()}
                style={{ width: '71%', marginLeft: '-2%' }}
                icon={<PlusOutlined />}
              >
                Add field
              </Button>
              ) : null}
              {/* <Button
                type="dashed"
                onClick={() => {
                  add('The head item', 0);
                }}
                style={{ width: '60%', marginTop: '20px' }}
                icon={<PlusOutlined />}
              >
                Add field at head
              </Button> */}
              {/* <Form.ErrorList errors={errors} /> */}
            </Form.Item>
          </>
        )}
      </Form.List>
      <Form.Item>
        <Button type="primary" htmlType="submit">
          Submit
        </Button>
      </Form.Item>
    </Form>
    : null }
     {value == 2 ?
      <Form name="dynamic_form_nest_item" onFinish={this.onFinish} autoComplete="off">
      <Form.List name="users">
        {(fields, { add, remove }) => (
          <>
            {fields.map(({ key, name, fieldKey, ...restField }) => (
              <Space key={key} style={{ display: 'flex', marginBottom: 8 }} align="baseline">
                <Form.Item
                  {...restField}
                  name={[name, 'first']}
                  fieldKey={[fieldKey, 'first']}
                  rules={[{ required: true, message: 'Type is Required' }]}
                >
                  <Input placeholder="Type" />
                </Form.Item>
                <Form.Item
                  {...restField}
                  name={[name, 'last']}
                  fieldKey={[fieldKey, 'last']}
                  rules={[{ required: true, message: 'Type is Required' }]}
                >
                  <Input placeholder="Value" />
                </Form.Item>
                <MinusCircleOutlined style={{fontSize: 20}} onClick={() => remove(name)} />
              </Space>
            ))}
            <Form.Item>
              {fields.length < 5 &&
              <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                Add field
              </Button>
              }
            </Form.Item>
          </>
        )}
      </Form.List>
      <Form.Item>
        <Button type="primary" htmlType="submit">
          Submit
        </Button>
      </Form.Item>
    </Form>
     : null}
        </Modal>

        <Modal
                title= {this.state.action == 'view' ? "View Classification" : "Edit Classification"}
                centered
                visible={this.state.viewModal}
                onOk={()=>{this.setState({viewModal: false})}}
                onCancel={()=>{this.setState({viewModal: false})}}
                footer={null}
                width={600}
                >
        {/* {this.state.action == 'view' && 
        <div>
                  <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Type : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        GENERIC
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
        <Col span={8} style={{fontSize:20, fontWeight:'bold',}}> Value : </Col>
        <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        GENERIC
        </Col>
        </Row>
        </div>
       } */}

        {this.state.action == 'edit' &&           
            <Form 
       onFinish={this.onSubmit} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="name"
        label="Type"
        rules={[
          {
            required: true,
            message: 'Type is Required',
          },
        ]}
      >
        {/* <Select defaultValue="GENERIC" placeholder="Please Select Type" >
        {type.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select> */}
       <Input defaultValue="GENERIC" style={{width:'100%'}}/>
        {/* <Input defaultValue="GENERIC" style={{width:'100%'}}/> */}
      </Form.Item>
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17}>
        <Form.Item
        name="cvalue"
        label="Value"
        rules={[
          {
            required: true,
            message: 'Value is Required',
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
       {/* <Input defaultValue="GENERIC" style={{width:'100%'}}/> */}
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
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Create Classification</h4>
          </Appm>
        )
      }
    }
  }
  </MediaQuery>
     )   
    }

}

export default CreateClassification