import React from 'react'
import { Form,Button, Row, Col, Input, Select,Layout,message } from "antd";


const { Option } = Select;
const { Header, Content, Footer } = Layout;

const type = [
  { value: "SM_Analysis_Code_1", label: "GENERI" },
  { value: "SM_Analysis_Code_1", label: "ETHIC" },
  { value: "SM_Analysis_Code_1", label: "H&B" },
];

const data = [
  {
    key: '1',
    name: 'UNICHEM',
    age: 32,
    
  },
  {
    key: '2',
    name: 'UNIDRUG',
    age: 42,
    
  },
  {
    key: '3',
    name: 'DALKEIT2',
    age: 32,
    },
  // {
  //   key: '4',
  //   name: 'Buyer',
  //   age: 32,
  // },
];

const role = [
  { value: "Administrator", label: "Administrator" },
  { value: "Buyer", label: "Buyer" },
  { value: "Manager", label: "Manager" },
  { value: "Staff", label: "Staff" },
];

class CreateProduct extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
          mainPage: false,
        };
      }

    onFinish = (values) => {
        // console.log('Received values of form: ', values);
        // message.success('Created Successfully');
    };
  // const { getFieldDecorator } = this.props.form;

  render(){
  return (
    <div>
       
       <Form 
       onFinish={this.onFinish} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="fName"
        label="Prod Code"
        rules={[
          {
            required: true,
            message: 'Please Enter Code',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17} >
        <Form.Item
        name="lName"
        label="DT Type"
        rules={[
          {
            required: true,
            message: 'Please Enter Type',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
      </Form.Item> 
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17} >
        <Form.Item
        name="email"
        label="DT Desc"
        rules={[
          {
            required: true,
            message: 'Please Enter Description',
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
        label="Prod Price"
        rules={[
          {
            required: true,
            message: 'Please Enter Price',
          },
        ]}
      >
          <Input style={{width:'100%'}}/>
       {/* <Select mode="multiple" placeholder="Please Select Role" >
        {role.map((c) => {
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
      
        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Classification"
        rules={[
          {
            // required: true,
            message: 'Please Select Classsification',
          },
        ]}
      >
        <Select style={{width:'100%'}}>
        {type.map((c) => {
          return (
          <Option key={c.label} value={c.label}>
            {c.label}
          </Option>
          );
        })}
        </Select>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Features"
        rules={[
          {
            // required: true,
            message: 'Please Select Feature',
          },
        ]}
      >
        <Select style={{width:'100%'}}>
        {data.map((c) => {
          return (
          <Option key={c.label} value={c.name}>
            {c.name}
          </Option>
          );
        })}
        </Select>
      </Form.Item>
        </Col>
        </Row>

      <Row>
      <Col span={9}></Col>
      <Col span={13} style={{marginLeft:'3%'}}>
      <Button type="primary" style={{backgroundColor:'#353b8d', borderColor:'#353b8d', marginLeft:'1%',marginTop:'3%'}} htmlType="submit">Submit</Button>
      {/* <Button style={{marginLeft:'5%',marginTop:'3%'}} onClick={()=>this.setState({mainPage:true})}>Back</Button> */}
      </Col>
      </Row>

      </Form>
      
    </div>
  );
    }
}

export default CreateProduct;
