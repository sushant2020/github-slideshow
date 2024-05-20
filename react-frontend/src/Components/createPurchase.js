import React from 'react'
import { Form,Button, Row, Col, Input, Select,Layout, Divider } from "antd";


const { Option } = Select;
const { Header, Content, Footer } = Layout;

const supplier = [
  { value: "1ST CALL SERVICES", label: "1ST CALL SERVICES" },
  { value: "365HEALTHCARE", label: "365HEALTHCARE" },
  { value: "A&D INSTRUMENTS LTD", label: "A&D INSTRUMENTS LTD" },
  { value: "5 STAR TECHNOLOGIES LTD", label: "5 STAR TECHNOLOGIES LTD" },
  { value: "AAH PHARMACEUTICALS LTD", label: "AAH PHARMACEUTICALS LTD" },
];

class CreatePurchase extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
          mainPage: false,
        };
      }

    onFinish = (values) => {
        // console.log('Received values of form: ', values);
    };
  // const { getFieldDecorator } = this.props.form;
  render(){
  return (
    <div>
       
       <Form 
    //    onFinish={this.onFinish} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="qty"
        label="Quantity"
        rules={[
          {
            required: true,
            message: 'Please Enter Quantity',
          },
        ]}
      >
        <Input style={{width:'100%'}} bordered={false}/>
        <Divider style={{marginTop:-3}}/>
      </Form.Item>
        </Col></Row><Row>
        <Col span={3}></Col>
        <Col span={17} >
        <Form.Item
        name="price"
        label="Price"
        rules={[
          {
            required: true,
            message: 'Please Enter Price',
          },
        ]}
      >
        <Input style={{width:'100%'}} bordered={false}/>
        <Divider style={{marginTop:-3}}/>
      </Form.Item> 
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17}>
       <Form.Item
        name="supplier"
        label="Supplier"
        rules={[
          {
            required: true,
            message: 'Please Select Supplier',
          },
        ]}
      >
       <Select placeholder="Please Select" bordered={false}>
        {supplier.map((c) => {
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
        <Row>
        <Col span={3}></Col>
        <Col span={17}>
        <Form.Item
        name="note"
        label="Note"
        rules={[
          {
            required: true,
            message: 'Please Enter Note',
          },
        ]}
      >
        <Input.TextArea style={{width:'100%'}} bordered={false}/>
        <Divider style={{marginTop:-3}}/>
      </Form.Item> 
        </Col>
      </Row>
      

      <Row>
      <Col span={9}></Col>
      <Col span={13} style={{marginLeft:'3%'}}>
      <Button type="primary" style={{marginLeft:'1%',backgroundColor:'#353b8d',marginTop:'3%'}} htmlType="submit">Submit</Button>
      {/* <Button style={{marginLeft:'5%',marginTop:'3%'}} onClick={()=>this.setState({mainPage:true})}>Back</Button> */}
      </Col>
      {/* <Col xs={{ span: 24 }} sm={{ span: 24 }} md={{ span: 6 }} lg={{ span: 6 }} ></Col> */}
      </Row>

      </Form>
      
    </div>
  );
    }
}

export default CreatePurchase;
